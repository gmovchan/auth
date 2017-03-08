<?php

/*
 * FIXME: код в этом файле не будет работать, пока я не перепишу его в виде класса
 */

require __DIR__ . '/../bootstrap.php';

if (isset($_POST['send'])) {
    if ($auth->reg($_POST['login'], $_POST['password'], $_POST['password2'], $_POST['mail'])) {
        $content_view = '/auth/joinSuccessful.php';
    } else {
        $error = $auth->getErrors();
        $content_view = '/auth/joinForm.php';
    }
} else {
    $content_view = '/auth/joinForm.php';
}

require __DIR__ . '/../../views/templates/authTemplate.php';