<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<title></title>
</head>
<body>
<?php
    $r = '';
    require 'module.php';
    $db = new Mysql();
    $db->connect('config.ini', 'vagrant');
    $auth = new Auth($db);
    
    //Авторизация
    if (isset($_POST['send'])) {
        if (!$auth->authentication()) {
            $error = $_SESSION['error'];
            unset ($_SESSION['error']);
        }
    }

    //выход
    if (isset($_GET['exit'])) {
            $auth->exit_user();
            
    }
    
    if ($auth->authorization()) {
        
        $r .= 'Добро пожаловать ' . $_SESSION['login_user'] . 
                '<br/><a href="?exit">Выйти</a>';
    } else {
        echo 'Авторизацию не прошел';
        if (isset($error)) {
            $r .= 'Какая-то ошибка' . $error;
            
        }
        
        $r.='
	<a href="join.php">Зарегистрироваться</a>
	<form action="" method="post">
		login <input type="text" name="login" value="'.@$_POST['login'].'" /><br />
		passwd <input type="password" name="password" id="" /><br />
		<input type="submit" value="send" name="send" />
	</form>
	';
    }
    
    echo $r;
?>
</body>
</html>