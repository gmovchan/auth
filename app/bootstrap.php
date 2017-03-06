<?php

require __DIR__ . '/models/auth.php';
require __DIR__ . '/models/mysql.php';

$db = new Mysql();
$db->connect(__DIR__ . '/configs/app.ini', 'vagrant');
$auth = new Auth($db);

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