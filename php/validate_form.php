<?php
    require $_SERVER['DOCUMENT_ROOT'].'/php/connection_auth.php';
    
    $data = $_GET["data"];
    $type = $_GET["type"];
    
    echo $auth->check_input($data, $type);
?>
