<?php

require __DIR__ . '/core/Model.php';
require __DIR__ . '/core/Route.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/core/View.php';
require __DIR__ . '/models/AuthModel.php';

//$auth = new AuthModel(__DIR__ . '/configs/app.ini', 'vagrant');
$route = new Route();
$route->start();
/*
if (isset($_SESSION['id_user']) and isset($_SESSION['login_user'])) {
    $sessionIdUser = $_SESSION['id_user'];
    $sessionCodeUser = $_SESSION['login_user'];
}
if (isset($_COOKIE['id_user']) and isset($_COOKIE['login_user'])) {
    $cockieIdUser = $_COOKIE['id_user'];
    $cockieCodeUser = $_COOKIE['code_user'];
}
 * 
 */