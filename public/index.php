<?php

require __DIR__ . "/../vendor/autoload.php";

use Budimansol\PHP\MVC\App\Routes;
use Budimansol\PHP\MVC\Controller\HomeController;
use Budimansol\PHP\MVC\Controller\ProductController;
use Budimansol\PHP\MVC\Middleware\AuthMiddleware;

Routes::add('GET', '/product/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)', ProductController::class, 'categories');

Routes::add('GET', '/', HomeController::class, 'index');
Routes::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Routes::add('GET', '/world', HomeController::class, 'world', [AuthMiddleware::class]);
Routes::add('GET', '/about', HomeController::class, 'about');

Routes::add('GET', '/login', HomeController::class, 'login');
Routes::run();

?>