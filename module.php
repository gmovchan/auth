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
         * запроса с неименованными параметрами для защиты от инъекций
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
         * хранит объект класса Mysql для работы с БД, чтобы любая функция этого 
         * класса могла им воспользоваться
         */
        private $db;
        
        function __construct(Mysql $db_class) {
            $this->db = $db_class;
        }
        
        function check_new_user() {

        }
        
        function reg() {
            
        }
        
        /**
         * проверка авторизации
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
         * Авторизация
         */
        function authentication() {
            $login = $_POST['login'];
            //хэщ пароля с солью
            //было: $password = md5($this->db->screening($_POST['password']).'lol');
            //убрал петод screening, потому что поле все равно шифруется
            //TODO: надо переделать обычные запросы к БД на подготавливаемые запросы для защиты от инъекций
            $password = md5($_POST['password'].'lol');
            
            if ($this->db->query("SELECT * FROM `users` WHERE `login_user` = ? AND `password_user` = ?;", 'num_row', '', array($login, $password)) == 1) {                
                    //пользователь с таким логинов и паролем найден
                    $_SESSION['id_user'] = $this->db->query("SELECT * FROM `users` "."WHERE `login_user` = ? AND `password_user` = ?;", 'result', 0, array($login, $password));
                    $_SESSION['login_user'] = $login;
                    //добавляет/обновляет запись в таблице сессий и ставит куки
                    $r_code = $this->generateCode(15);
                    
                    if ($this->db->query("SELECT * FROM `session` WHERE `id_user` = ?;", 'num_row', '', array($_SESSION['id_user'])) == 1) {

                        //запись уже есть - обновляем
                        $this->db->query("UPDATE `session` SET `code_sess` = '".$r_code."', `user_agent_sess` = ? where `id_user` = ?;", '', '', 
                                array($_SERVER['HTTP_USER_AGENT'], $_SESSION['id_user']));          
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
            //XXX: по какой-то причине, если не делать выход, то скрипт будет дальше
            // выполняться, пока не дойдет до конца и только тогда сменет хейдер
            exit();
        }
        
        function recovery_pass($param) {
            
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
            $r='<h2>Произошли ошибки:</h2>'."\n".'<ul>';
            foreach ($error as $key=>$value) {
                $r .= '<li>'.$value.'</li>';
            }
            return $r.'</ul>';
        }

    }
    
    /* авторизация на страницах
     * if (!$auth->check()) {
        //~ совершаем процедуру выхода
        $auth->exit_user();
        }
     */

?>