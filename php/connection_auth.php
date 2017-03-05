<?php
require $_SERVER['DOCUMENT_ROOT'].'/php/auth.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/mysql.php';
$db = new Mysql();
$db->connect($_SERVER['DOCUMENT_ROOT'].'/config.ini', 'vagrant');
$auth = new Auth($db);


