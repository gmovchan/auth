<?php
/**
 * В родительском классе Model находится реализация работы с БД
 */
class AuthModel extends Model
{

    public $errors = [];

    function reg($login, $password, $password2, $mail)
    {
        $check = $this->check_new_user($login, $password, $password2, $mail);
        if ($check == 'good') {
            $password = md5($password . 'lol');
            if ($this->query("INSERT INTO `users` ( `login_user`,"
                            . " `password_user`, `mail_user`) VALUES (?, ?, ?);", 'num_row', '', array($login, $password, $mail)) != 0) {
                return true;
            } else {
                //echo '<p>Возникла ошибка при регистрации нового пользователя. Свяжитесь с администратором</p>';
                $this->errors[] = '<p>Возникла ошибка при регистрации нового пользователя. Свяжитесь с администратором</p>';
                return false;
            }
        } else {
            //в случае ошибки при проверки полей формы $check возвращает массив с текстом ошибок
            $this->errors = $check;
            //$_SESSION['error'] = $this->error_print($check);
            return false;
        }
    }

    /*
     * TODO: вынести валидацию формы на фронт с помощью AJAX
     */

    function check_new_user($login, $password, $password2, $mail)
    {
        if (empty($login) or empty($password) or empty($mail)) {
            $error[] = 'Все поля обязательны для заполнения';
        }
        if ($password != $password2) {
            $error[] = 'Введенные пароли не совпадают';
        }
        if (strlen($login) < 3 or strlen($login) > 30) {
            $error[] = 'Длина логина должна быть от 3 до 30 символов';
        }
        /*
         * Валидация почты не используя регулярки 
         * http://www.php.net/manual/en/filter.examples.validation.php
         */
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $error[] = 'Некорректный email';
        }

        if ($this->query("SELECT * FROM `users` WHERE `login_user` = ?;", 'num_row', '', array($login)) != 0) {
            $error[] = 'Пользователь с таким именем уже существует';
        }

        if ($this->query("SELECT * FROM `users` WHERE `mail_user` = ?;", 'num_row', '', array($mail)) != 0) {
            $error[] = 'Пользователь с таким email уже существует';
        }

        if (isset($error)) {
            return $error;
        } else {
            return 'good';
        }
    }

    /**
     * ajax валидация формы регистрации
     * @param type $data текст в поле input
     * @param type $type название поля input
     * @return ответ в формате json с названием поля и текстом ошибки, если она есть
     */
    function check_input($data, $type)
    {
        $message = 'false';
        $error = false;
        // убирает пробелы, чтобы не получилось, что имя или пароль будут состоять из одного пробела
        $data = trim($data);

        switch ($type) {
            case 'join-name':
                if (empty($data)) {
                    $error = true;
                    $message = 'Логин не может быть пустым';
                } else if ($this->query("SELECT * FROM `users` WHERE `login_user` = ?;", 'num_row', '', array($data)) != 0) {
                    $error = true;
                    $message = 'Логин занят';
                } else {
                    $error = false;
                    $message = 'Логин прошел проверку';
                }
                break;

            case 'join-password':
                if (empty($data)) {
                    $error = true;
                    $message = 'Пароль не введен';
                } else {
                    $error = false;
                }

                break;

            case 'join-password2':
                if ($data == 'empty') {
                    $error = true;
                    $message = 'Введите пароль еще раз';
                } else if ($data == 'match') {
                    $error = false;
                } else if ($data == 'false') {
                    $error = true;
                    $message = 'Пароли не совпадают';
                }
                break;

            case 'join-mail':
                if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
                    $error = true;
                    $message = 'Некорректный email';
                } else if ($this->query("SELECT * FROM `users` WHERE `mail_user` = ?;", 'num_row', '', array($data)) != 0) {
                    $error = true;
                    $message = 'Пользователь с таким email уже существует';
                } else {
                    $error = false;
                }
                break;
            default:
                # code...
                break;
        }

        if ($error) {
            return json_encode(array('error' => "true", 'message' => $message));
        } else {
            return json_encode(array('error' => "false", 'message' => $message));
        }
    }

    /**
     * Проверяет строку и возвращает текст ошибки, если она пустая
     * @param type $value
     * @return boolean|string
     */
    function check_empty($value)
    {
        if (empty($value)) {
            return 'Поле обязательно должно быть заполнено';
        }
        return false;
    }

    /**
     * Авторизация
     */
    function authorization()
    {
        if (isset($_SESSION['id_user']) and isset($_SESSION['login_user'])) {
            return true;
        } else {
            //проеверяет ниличие кук
            if (isset($_COOKIE['id_user']) and isset($_COOKIE['code_user'])) {

                //если куки есть, то сверяет их с таблицей сессий
                $id_user = $_COOKIE['id_user'];
                $code_user = $_COOKIE['code_user'];

                if ($this->query("SELECT * FROM `session` WHERE `id_user` = ?;", 'num_row', '', array($id_user)) == 1) {

                    //есть запись, сверяет данные
                    $data = $this->query("SELECT * FROM `session` WHERE `id_user` = ?;", 'accos', '', array($id_user));

                    if ($data['code_sess'] == $code_user and $data['user_agent_sess'] == $_SERVER['HTTP_USER_AGENT']) {

                        //данные верны, стартуем сессию
                        $_SESSION['id_user'] = $id_user;
                        $_SESSION['login_user'] = $this->query("SELECT login_user FROM `users` WHERE `id_user` = ?;", 'result', 0, array($id_user));

                        //обновляет куки                            
                        setcookie("id_user", $_SESSION['id_user'], time() + 3600 * 24 * 14, '/');
                        setcookie("code_user", $code_user, time() + 3600 * 24 * 14, '/');

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
    function authentication()
    {
        $login = $_POST['login'];
        $password = md5($_POST['password'] . 'lol');

        if ($this->query("SELECT * FROM `users` WHERE `login_user` = ? AND `password_user` = ?;", 'num_row', '', array($login, $password)) == 1) {
            //пользователь с таким логинов и паролем найден
            $_SESSION['id_user'] = $this->query("SELECT * FROM `users` WHERE `login_user` = ? AND `password_user` = ?;", 'result', 0, array($login, $password));
            $_SESSION['login_user'] = $login;
            //добавляет/обновляет запись в таблице сессий и ставит куки
            $r_code = $this->generateCode(15);

            if ($this->query("SELECT * FROM `session` WHERE `id_user` = ?;", 'num_row', '', array($_SESSION['id_user'])) == 1) {

                //запись уже есть - обновляем
                $this->query("UPDATE `session` SET `code_sess` = ?, `user_agent_sess` = ? where `id_user` = ?;", '', '', array($r_code, $_SERVER['HTTP_USER_AGENT'], $_SESSION['id_user']));
            } else {

                // записи нет, добавляет
                $this->query("INSERT INTO `session` (`id_user`, `code_sess`, `user_agent_sess`) VALUE (?, ?, ?);", '', '', array($_SESSION['id_user'], $r_code, $_SERVER['HTTP_USER_AGENT']));
            }
            //ставим куки на 2 недели
            setcookie("id_user", $_SESSION['id_user'], time() + 3600 * 24 * 14, '/');
            setcookie("code_user", $r_code, time() + 3600 * 24 * 14, '/');
            return true;
        } else {

            //пользователь не найден в БД или пароль неверный
            if ($this->query("SELECT * FROM `users` WHERE `login_user` = ?;", 'num_row', 0, array($login)) == 1) {
                $error[] = 'Неверный пароль';
            } else {
                $error[] = 'Пользователя не сущестует';
            }
            $this->errors = $error;
            return false;
        }
    }

    function exit_user()
    {
        session_start();
        session_destroy();
        setcookie("id_user", '', time() - 3600, '/');
        setcookie("code_user", '', time() - 3600, '/');
        var_dump($_COOKIE);
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/auth';
        header("Location:" . $host);
        exit();
    }

    function recovery_pass($login, $mail)
    {
        if ($this->query("SELECT * FROM `users` WHERE `login_user`=?;", 'num_row', '', array($login)) != 1) {
            //не найден такой пользователь
            $error[] = 'Пользователь с таким именем не найден';
            $this->errors = $error;
            return false;
        } else {
            $db_inf = $this->query("SELECT * FROM `users` WHERE `login_user`=?;", 'accos', '', array($login));
            if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                $error[] = 'Введен некорректный email';
            }
            if ($mail != $db_inf['mail_user']) {
                $error[] = 'Введенный email не соответствует введенному при регистрации';
            }
            if (!isset($error)) {
                //если нет ошибок, то восстанавливает пароль
                $new_password = $this->generateCode(8);
                $new_password_sql = md5($new_password . 'lol');
                $message = "Вы запросили восстановление пароля на сайте "
                        . "%sitename% для учетной записи " . $db_inf['login_user'] .
                        " \nВаш новый пароль: " . $new_password . "\n\n С уважением "
                        . "администрация сайта %sitename%.";

                if ($send_mail = mail($mail, "Восстановление пароля", $message, "From: webmaster@satename.ru\r\n" .
                        "Reply-To: webmaster@satename.ru\r\n" . "X-Mailer: PHP/" . phpversion())) {
                    //обновляет пароль к базе
                    $this->query("UPDATE `users` SET `password_user` = ? WHERE `id_user` = ?;", '', '', array($new_password_sql, $db_inf['id_user']));
                    return 'good';
                } else {
                    //ошибка при отправки письма
                    var_dump($message);
                    $error[] = 'В данный момент восстановление пароля невозможно, свяжитесь с администрацией сайта';
                    $this->errors = $error;
                    return false;
                }
            } else {
                $this->errors = $error;
                return false;
            }
        }
    }

    /**
     * генерирует случайную строку
     */
    function generateCode($length)
    {
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
    /*
      function error_print($error)
      {
      $r = '<div class="bg-danger">Произошли ошибки:' . "\n" . '<ul>';
      foreach ($error as $key => $value) {
      $r .= '<li>' . $value . '</li>';
      }
      return $r . '</ul></div>';
      }
     * 
     */

    public function getErrors()
    {
        return $this->errors;
    }

}

?>