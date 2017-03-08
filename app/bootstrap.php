<?php

require __DIR__ . '/core/Model.php';
require __DIR__ . '/core/Route.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/core/View.php';
require __DIR__ . '/models/AuthModel.php';

$route = new Route();
$route->start();