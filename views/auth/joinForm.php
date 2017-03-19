<div class="container">
    <form action="" method="post" class="form-signin" id="join-form">   
        <h2 class="form-signin-heading">Регистрация</h2>
        <?php
        if (isset($error)) {
            require 'errorAuth.php';
        }
        ?>
        <div class="form-group">
            <label for="name">Логин</label>
            <input id="join-name" type="text" class="form-control" name="login" placeholder="Логин" value="<?php echo htmlspecialchars($data['login'], ENT_QUOTES) ?>">
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
            <input id="join-mail" type="email" class="form-control" name="mail" placeholder="Email" value="<?php echo htmlspecialchars($data['mail'], ENT_QUOTES) ?>">
            <span id="join-mail-help" class="help-block" style="display: none">Help</span>
        </div>
        <input class="hidden" name="send" value="send">
        <button id="join-submit" type="submit" class="btn btn-default">Зарегистрироваться</button> или <a href="/../auth">войти</a><br />
    </form>    
</div>

<!-- Скрипты для AJAX валидации формы регистрации -->    
<script src="<?php echo '/../js/validate.js'; ?>"></script>
<script src="<?php echo '/../js/join.js'; ?>"></script>
