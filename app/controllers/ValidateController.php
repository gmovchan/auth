<?php

/*
 * FIXME: код в этом файле не будет работать, пока я не перепишу его в виде класса
 */

require __DIR__ . '/../bootstrap.php';

$data = $_GET["data"];
$type = $_GET["type"];

echo $auth->check_input($data, $type);
