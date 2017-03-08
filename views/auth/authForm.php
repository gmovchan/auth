<div class="container">

    <form action="" method="post" class="form-signin">
        <h2 class="form-signin-heading">Пожалуйста войдите</h2>
        <?php
        if (isset($error)) {
            require 'auth_error.php';
        }
        ?>
        <div class="form-group">
            <label for="name">Логин</label>
            <input type="text" class="form-control" name="login" placeholder="Логин" value="<?php @$_POST['login'] ?>">
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" class="form-control" placeholder="Пароль" name="password">
        </div>
        <input class="hidden" name="send" value="send">
        <button type="submit" class="btn btn-default">Войти</button> или <a href="JoinController.php">зарегистрироваться</a>
    </form>    
</div>