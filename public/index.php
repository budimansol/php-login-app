<?php

require __DIR__ . "/../vendor/autoload.php";

use Budimansol\PHP\MVC\App\Routes;
use Budimansol\PHP\MVC\Controller\HomeController;

Routes::add('GET', '/', HomeController::class, 'index');
Routes::run();

?>