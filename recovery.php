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
    require 'module.php';
    $db = new Mysql();
    $db->connect('config.ini', 'vagrant');
    $reg = new auth($db);  //~ Создаем новый объект класса

    function print_form($error = false) {
        $form = '
                    <form action="" method="post" class="form-signin">   
                        <h2 class="form-signin-heading">Восстановление пароля</h2>
                        ';
        
        if ($error) {
            $form .= $error;
        }
        
        $form .= '
                        <div class="form-group">
                          <label for="name">Логин</label>
                          <input type="text" class="form-control" name="login" placeholder="Логин" value="'.@$_POST['login'].'">
                        </div>
                        <div class="form-group">
                            <label for="mail">Email</label>
                            <input type="email" class="form-control" name="mail" placeholder="Email" value="'.@$_POST['mail'].'">
                        </div>
                        <input class="hidden" name="send" value="send">
                        <button type="submit" class="btn btn-default">Восстановить</button> или <a href="index.php">войти</a><br />
                    </form>    
                    ';
        return $form;
    }
    
    if (isset($_POST['send'])) {
        $reply = $reg->recovery_pass($_POST['login'], $_POST['mail']);
        if ($reply == 'good') {
            'Новый пароль был выслан вам на почту';
        } else {
            echo print_form($reply);
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
</body>
</html>
