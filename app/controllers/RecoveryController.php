<?php

/*
 * FIXME: код в этом файле не будет работать, пока я не перепишу его в виде класса
 */

require __DIR__ . '/../bootstrap.php';

if (isset($_POST['send'])) {
    $reply = $auth->recovery_pass($_POST['login'], $_POST['mail']);
    if ($reply == 'good') {
        $content_view = '/auth/recoverySuccessful.php';
    } else {
        $error = $auth->getErrors();
        $content_view = '/auth/recoveryForm.php';
    }
} else {
    $content_view = '/auth/recoveryForm.php';
}

require __DIR__ . '/../../views/templates/authTemplate.php';