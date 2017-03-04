<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Auth</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    
    <link href="css/signin.css" rel="stylesheet">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
        <div class="container">
<?php
    require 'php/mysql.php';
    require 'php/module.php';
    $db = new Mysql();
    $db->connect('config.ini', 'vagrant');
    $reg = new auth($db);  //~ Создаем новый объект класса

    function print_form($error = false) {
        $form = '
                    <form action="" method="post" class="form-signin" id="join-form">   
                        <h2 class="form-signin-heading">Регистрация</h2>
                        ';
        
        if ($error) {
            $form .= $error;
        }
        
        $form .= '
                        <div class="form-group">
                          <label for="name">Логин</label>
                          <input id="join-name" type="text" class="form-control" name="login" placeholder="Логин" value="'.@$_POST['login'].'">
                          <span id="join-name-help" class="help-block" style="display: none">Help</span>
                        </div>
                        <div class="form-group">
                          <label for="password">Пароль</label>
                          <input id="join-password" type="password" class="form-control" placeholder="Пароль" name="password">
                          <span id="join-password-help" class="help-block" style="display: none">Help</span>
                        </div>
                        <div class="form-group">
                          <label for="password2">Повторите пароль</label>
                          <input id="join-password2" type="password" class="form-control" placeholder="Пароль" name="password2">
                          <span id="join-password2-help" class="help-block" style="display: none">Help</span>
                        </div>
                        <div class="form-group">
                            <label for="mail">Email</label>
                            <input id="join-mail" type="email" class="form-control" name="mail" placeholder="Email" value="'.@$_POST['mail'].'">
                            <span id="join-mail-help" class="help-block" style="display: none">Help</span>
                        </div>
                        <input class="hidden" name="send" value="send">
                        <button id="join-submit" type="submit" class="btn btn-default">Зарегистрироваться</button> или <a href="index.php">войти</a><br />
                    </form>    
                    ';
        return $form;
    }
    
    if (isset($_POST['send'])) {
            if ($reg->reg($_POST['login'], $_POST['password'], $_POST['password2'], $_POST['mail'])) {

                print '
                        <h2>Регистрация успешна.</h2>
                        Вы можете войти <a href="index.php">авторизоваться</a>.
                ';
            } else {

                if (isset($_SESSION['error'])) {
                    $error = $_SESSION['error'];
                    unset ($_SESSION['error']);
                    echo print_form($error);
                }
                
            }
                
    } else {
        echo print_form();;
    }
?>                
        </div>
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>   

    <script src="js/validate.js"></script>
    <script src="js/join.js"></script>
</body>
</html>
