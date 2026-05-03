<?php

require __DIR__ . "/../vendor/autoload.php";

use Budimansol\PHP\MVC\App\Routes;
use Budimansol\PHP\MVC\Config\Database;
use Budimansol\PHP\MVC\Controller\HomeController;
use Budimansol\PHP\MVC\Controller\UserController;
use Budimansol\PHP\MVC\Middleware\MustLoginMiddleware;
use Budimansol\PHP\MVC\Middleware\MustNotLoginMiddleware;

Database::getConnection('prod');

// Home
Routes::add('GET', '/', HomeController::class, 'index');

//Register
Routes::add('GET', '/users/register', UserController::class, 'register', [MustNotLoginMiddleware::class]);
Routes::add('POST', '/users/register', UserController::class, 'postRegister', [MustNotLoginMiddleware::class]);
//Login
Routes::add('GET', '/users/login', UserController::class, 'login', [MustNotLoginMiddleware::class]);
Routes::add('POST', '/users/login', UserController::class, 'postLogin', [MustNotLoginMiddleware::class]);
//Logout
Routes::add('GET', '/users/logout', UserController::class, 'logout', [MustLoginMiddleware::class]);
//Update Profile
Routes::add('GET', '/users/profile', UserController::class, 'updateProfile', [MustLoginMiddleware::class]);
Routes::add('POST', '/users/profile', UserController::class, 'postUpdateProfile', [MustLoginMiddleware::class]);
//Update Password
Routes::add('GET', '/users/password', UserController::class, 'updatePassword', [MustLoginMiddleware::class]);
Routes::add('POST', '/users/password', UserController::class, 'postUpdatePassword', [MustLoginMiddleware::class]);


Routes::run();

?>