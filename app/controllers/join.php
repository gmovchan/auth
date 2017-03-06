<?php

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../views/header.php';


if (isset($_POST['send'])) {
    if ($auth->reg($_POST['login'], $_POST['password'], $_POST['password2'], $_POST['mail'])) {
        require_once __DIR__ . '/../../views/join_successful.php';
    } else {
        $error = $auth->getErrors();
        require_once __DIR__ . '/../../views/join_form.php';
    }
} else {
    require_once __DIR__ . '/../../views/join_form.php';
}

require __DIR__ . '/../../views/footer.php';