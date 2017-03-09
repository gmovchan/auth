<?php
namespace Application;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/core/Model.php';
//require __DIR__ . '/core/Route.php';
require __DIR__ . '/core/Controller.php';
require __DIR__ . '/core/View.php';
require __DIR__ . '/models/AuthModel.php';

use Application\Core\Route;

$route = new Route();
//var_dump(class_exists('Route'));
$route->start();