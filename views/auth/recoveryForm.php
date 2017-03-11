<div class="container">
    <form action="" method="post" class="form-signin">   
        <h2 class="form-signin-heading">Восстановление пароля</h2>
        <?php
        if (isset($error)) {
            require 'auth_error.php';
        }
        ?>
        <div class="form-group">
            <label for="name">Логин</label>
            <input type="text" class="form-control" name="login" placeholder="Логин" value="<?php echo htmlspecialchars($data['login'], ENT_QUOTES) ?>">
        </div>
        <div class="form-group">
            <label for="mail">Email</label>
            <input type="email" class="form-control" name="mail" placeholder="Email" value="<?php echo htmlspecialchars($data['mail'], ENT_QUOTES) ?>">
        </div>
        <input class="hidden" name="send" value="send">
        <button type="submit" class="btn btn-default">Восстановить</button> или <a href="auth.php">войти</a><br />
    </form>    

</div>
