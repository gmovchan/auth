<?php

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../views/header.php';

if (isset($_POST['send'])) {
    $reply = $auth->recovery_pass($_POST['login'], $_POST['mail']);
    if ($reply == 'good') {
        require __DIR__ . '/../../views/recovery_successful.php';
    } else {
        $error = $auth->getErrors();
        require __DIR__ . '/../../views/recovery_form.php';
    }
} else {
    require __DIR__ . '/../../views/recovery_form.php';
}
