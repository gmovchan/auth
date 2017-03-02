<?php
    /**
     * принимает ссылку на файл конфигурации для подключения к БД
     */
    class Mysql {
        
        //хранит подключение к БД для доступа к нему из методов класса
        private $dbh;
        
        function connect($config_path, $section_name) {
            //получение данных из файла конфигурации
            $config_data = $this->config_load($config_path, $section_name);
            
            //отлов ошибок подключения к БД
            try {
                
                $this->dbh = new PDO('mysql:host='.$config_data['host'].';dbname='.
                        $config_data['db'], $config_data['user'], $config_data['password']);
                // требуется чтобы PDO сообщало об ошибке и прерывало выполнение скрипта
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            } catch (PDOException $e) {
                echo '<p>'.$e->getMessage().'</p>';
                exit();
            }
        }
        
        /**
         * запрос к БД
         * без понятия зачем тут нужен новый уровень абстракции
         * $section_name принимает массив с параметрами для подготавливаемого 
         * запроса с неименованными псевдопеременными для защиты от инъекций
         */
        function query($query, $type = null, $num = null, array $query_param = array()) {
            try {
                if ($q = $this->dbh->prepare($query)) {
                    switch ($type) {
                        case 'num_row': 
                            $q->execute($query_param);
                            return $q->rowCount();
                            break;
                        
                        case 'result': 
                            $q->execute($query_param);
                            return $q->fetchColumn($num);
                            break;
                        
                        case 'accos': 
                            $q->execute($query_param);
                            return $q->fetch(PDO::FETCH_ASSOC);
                            break;
                        
                        case 'none': 
                            $q->execute($query_param);
                            return $q;
                            break;

                        default: 
                            $q->execute($query_param);
                            return $q;
                    }
                }
            } catch (PDOException $e) {
                //TODO: убрать при переносе на сервер, строка только для отладки
                echo '<p>'.$query.'</p>';
                echo '<p>'.$e->getMessage().'</p>';
                exit();
            }
            
            
        }
        
        /**
         * экранирует данные
         * FIXME: лучше переделать на подготавливаемые запросы, функция возвращает
         * строку в кавычках, которая ломает некоторые функции
         */        
        function screening($data) {
            $data = trim($data);
            return $this->dbh->quote($data);
        }


        /**
         * получает путь к файлу конфигурации и возвращает массив
         * если передан $section_name, то возвращает только массив с данными из
         * определенной секции конфига
         */
        function config_load($config_path, $section_name = false) {
            if (file_exists($config_path)) {
                $config_array = parse_ini_file($config_path, true);
                if ($section_name) {
                    return $config_array[$section_name];
                }
                return $config_array;
            }       
        }
    }
    
    class Auth {
        
        /**
         * $db хранит объект класса Mysql для работы с БД, чтобы любая функция этого 
         * класса могла им воспользоваться
         */
        private $db;
        
        function __construct(Mysql $db_class) {
            $this->db = $db_class;
        }
        
        function reg($login, $password, $password2, $mail) {
            $check = $this->check_new_user($login, $password, $password2, $mail);
            if ($check == 'good') {
                $password = md5($password.'lol');
                if ($this->db->query("INSERT INTO `users` ( `login_user`,"
                        . " `password_user`, `mail_user`) VALUES (?, ?, ?);",
                    'num_row', '', array($login, $password, $mail)) != 0) {
                    return true;
               } else {
                   echo '<p>Возникла ошибка при регистрации нового пользователя. Свяжитесь с администратором</p>';
                   return false;
               }
            } else {
                $_SESSION['error'] = $this->error_print($check);
                return false;
            }
        }
        
        /*
         * TODO: вынести валидацию формы на фронт с помощью AJAX
         */
        function check_new_user($login, $password, $password2, $mail) {
            if (empty($login) or empty($password) or empty($mail)) {
                $error[]='Все поля обязательны для заполнения';
            }
            if ($password != $password2) {
                $error[]='Введенные пароли не совпадают';
            }
            if (strlen($login) < 3 or strlen($login) > 30) {
                $error[]='Длина логина должна быть от 3 до 30 символов';
            }
            /*
             * Валидация почты не используя регулярки 
             * http://www.php.net/manual/en/filter.examples.validation.php
             */ 
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $error[]='Некорректный email';
            }
            
            if ($this->db->query("SELECT * FROM `users` WHERE `login_user` = ?;",
                    'num_row', '', array($login)) != 0) {
                $error[]='Пользователь с таким именем уже существует';
            }
            
            if ($this->db->query("SELECT * FROM `users` WHERE `mail_user` = ?;",
                    'num_row', '', array($mail)) != 0) {
                $error[]='Пользователь с таким email уже существует';
            }
            
            if (isset($error)) {
                return $error;
            } else {
                return 'good';
            }
        }
        
        /**
         * ajax валидация формы регистрации
         * @param type $json содержание ajax запроса в формате JSON
         */
        /*
        function check_entry_field_ajax($json) {
            $form = json_decode($json, true);
            $field_name = $form['name'];
            $field_value = $form['value'];
            $response = array('error' => '');
            
            if (empty($field_value)) {
                $error = 'Поле обязательно должно быть заполнено';
            } else {
                switch ($field_name) {
                    case 'login':

                        if ($this->db->query("SELECT * FROM `users` WHERE `login_user` = ?;",
                                'num_row', '', array($field_value)) != 0) {
                            $error = 'Пользователь с таким именем уже существует';
                            break;
                        }

                        break;

                    case 'mail':

                        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                            $error = 'Некорректный email';
                            break;
                        }

                        if ($this->db->query("SELECT * FROM `users` WHERE `mail_user` = ?;",
                                'num_row', '', array($field_value)) != 0) {
                            $error = 'Пользователь с таким email уже существует';
                            break;
                        }

                        break;

                    default:
                        $error = false;
                        break;
               }
            }
            
            if ($error) {
                $response('error') = $error;
            } else {
                $response('error') = false;
            }
            
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        */
        /**
         * Проверяет строку и возвращает текст ошибки, если она пустая
         * @param type $value
         * @return boolean|string
         */
        function check_empty($value) {
            if (empty($value)) {
                return 'Поле обязательно должно быть заполнено';
            }
            return false;
        }


        /**
         * Авторизация
         */
        function authorization() {
            if (isset($_SESSION['id_user']) and isset($_SESSION['login_user'])) {
                return true;
            } else {
                //проеверяет ниличие кук
                if (isset($_COOKIE['id_user']) and isset($_COOKIE['code_user'])) {
                    
                    //если куки есть, то сверяет их с таблицей сессий
                    $id_user = $_COOKIE['id_user'];
                    $code_user = $_COOKIE['code_user'];
                    
                    if ($this->db->query("SELECT * FROM `session` WHERE `id_user` = ?;", 'num_row', '', array($id_user)) == 1) {
                        
                        //есть запись, сверяет данные
                        $data = $this->db->query("SELECT * FROM `session` WHERE `id_user` = ?;", 'accos', '', array($id_user));

                        if ($data['code_sess'] == $code_user and $data['user_agent_sess'] == $_SERVER['HTTP_USER_AGENT']) {
                            
                            //данные верны, стартуем сессию
                            $_SESSION['id_user'] = $id_user;
                            $_SESSION['login_user'] = $this->db->query("SELECT login_user FROM `users` WHERE `id_user` = ?;", 'result', 0, array($id_user));
                            
                            //обновляет куки                            
                            setcookie("id_user", $_SESSION['id_user'], time()+3600*24*14);
                            setcookie("code_user", $code_user, time()+3600*24*14);

                            return true;
                        } else {                           
                            //данные в таблице сессий не совпадают с куками
                            return false;
                        }
                    } else {
                            
                            //в таблице сессий не найдено такого пользователя
                            return false;
                        }
                } else {
                    return false;
                }
            } 
        }
        
        /**
         * Аутентификация
         */
        function authentication() {
            $login = $_POST['login'];
            $password = md5($_POST['password'].'lol');
            
            if ($this->db->query("SELECT * FROM `users` WHERE `login_user` = ? AND `password_user` = ?;", 'num_row', '', array($login, $password)) == 1) {                
                    //пользователь с таким логинов и паролем найден
                    $_SESSION['id_user'] = $this->db->query("SELECT * FROM `users` WHERE `login_user` = ? AND `password_user` = ?;", 'result', 0, array($login, $password));
                    $_SESSION['login_user'] = $login;
                    //добавляет/обновляет запись в таблице сессий и ставит куки
                    $r_code = $this->generateCode(15);
                    
                    if ($this->db->query("SELECT * FROM `session` WHERE `id_user` = ?;", 'num_row', '', array($_SESSION['id_user'])) == 1) {

                        //запись уже есть - обновляем
                        $this->db->query("UPDATE `session` SET `code_sess` = ?, `user_agent_sess` = ? where `id_user` = ?;", '', '', 
                                array($r_code, $_SERVER['HTTP_USER_AGENT'], $_SESSION['id_user']));          
                    } else {

                        // записи нет, добавляет
                        $this->db->query("INSERT INTO `session` (`id_user`, `code_sess`, `user_agent_sess`) VALUE (?, ?, ?);", '', '',
                                array($_SESSION['id_user'], $r_code, $_SERVER['HTTP_USER_AGENT']));
                     }
                    //ставим куки на 2 недели
                    setcookie("id_user", $_SESSION['id_user'], time()+3600*24*14);
                    setcookie("code_user", $r_code, time()+3600*24*14);
                    return true;
            } else {
                
                //пользователь не найден в БД или пароль неверный
                if ($this->db->query("SELECT * FROM `users` WHERE `login_user` = ?;", 'num_row', 0, array($login)) == 1) {
                    $error[] = 'Неверный пароль';      
                } else {
                    $error[] = 'Пользователя не сущестует';                         
                }
                $_SESSION['error'] = $this->error_print($error);
                return false;
            }
        }
        
        function exit_user() {
            session_destroy();
            setcookie("id_user", '', time()-3600);
            setcookie("code_user", '', time()-3600);
            header("Location: index.php");
            /*
             * XXX: по какой-то причине, если не делать выход, то скрипт будет дальше
             * выполняться, пока не дойдет до конца и только тогда сменит хейдер
             * из-за чего куки будут переписаны функцией аутентификации
             */
            exit();
        }
        
        function recovery_pass($login, $mail) {        
            if ($this->db->query("SELECT * FROM `users` WHERE `login_user`=?;", 
                    'num_row', '', array($login)) != 1) {
                //не найден такой пользователь
                $error[] = 'Пользователь с таким именем не найден';
                return $this->error_print($error);
            } else {
                $db_inf = $this->db->query("SELECT * FROM `users` WHERE `login_user`=?;", 
                    'accos', '', array($login));
                if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                    $error[] = 'Введен некорректный email';
                }
                if ($mail != $db_inf['mail_user']) {
                    $error[] = 'Введенный email не соответствует введенному при регистрации';
                }
                if (!isset($error)) {
                    //если нет ошибок, то восстанавливает пароль
                    $new_password = $this->generateCode(8);
                    $new_password_sql = md5($new_password.'lol');
                    $message = "Вы запросили восстановление пароля на сайте "
                            . "%sitename% для учетной записи ".$db_inf['login_user'].
                            " \nВаш новый пароль: ".$new_password."\n\n С уважением "
                            . "администрация сайта %sitename%.";
                    
                    if($send_mail = mail($mail, "Восстановление пароля", $message, "From: webmaster@satename.ru\r\n".
                            "Reply-To: webmaster@satename.ru\r\n"."X-Mailer: PHP/". phpversion())) {
                        //обновляет пароль к базе
                        $this->db->query("UPDATE `users` SET `password_user` = ? WHERE `id_user` = ?;",
                                '', '', array($new_password_sql, $db_inf['id_user']));
                        return 'good';
                    } else {
                        //ошибка при отправки письма
                        var_dump($message);
                        $error[]='В данный момент восстановление пароля невозможно, свяжитесь с администрацией сайта';
                        return $this->error_print($error);
                    } 
                } else {
                        return $this->error_print($error);
                    }
            }
        }
        
        /**
         * генерирует случайную строку
         */
        function generateCode($length) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
            $code = '';
            $clean = strlen($chars) - 1;
            while (strlen($code) < $length) {
                $code .= $chars[mt_rand(0, $clean)];
            }

            return $code;
        }
        
        /**
         * Формирует список ошибок
         */
        function error_print($error) {
            $r='<div class="bg-danger">Произошли ошибки:'."\n".'<ul>';
            foreach ($error as $key=>$value) {
                $r .= '<li>'.$value.'</li>';
            }
            return $r.'</ul></div>';
        }

    }

?>