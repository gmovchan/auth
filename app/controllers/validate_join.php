<?php

require __DIR__ . '/../bootstrap.php';

$data = $_GET["data"];
$type = $_GET["type"];

echo $auth->check_input($data, $type);
