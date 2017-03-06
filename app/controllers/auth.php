<?php

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../views/header.php';

//Авторизация
if (isset($_POST['send'])) {
    if (!$auth->authentication()) {
        $error = $auth->getErrors();
        var_dump($error);
    }
}

//выход
if (isset($_GET['exit'])) {
    $auth->exit_user();
}

if ($auth->authorization()) {
    require __DIR__ . '/../../views/auth_successful.php';
} else {
    require __DIR__ . '/../../views/auth_form.php';
}

require __DIR__ . '/../../views/footer.php';


