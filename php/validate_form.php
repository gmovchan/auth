<?php
    require 'mysql.php';
    require 'module.php';

    $db = new Mysql();
    $db->connect('../config.ini', 'vagrant');
    
    $auth = new Auth($db);
    
    $data = $_GET["data"];
    $type = $_GET["type"];
    
    echo $auth->check_input($data, $type);
?>
