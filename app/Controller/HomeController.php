<?php

namespace Budimansol\PHP\MVC\Controller;

use Budimansol\PHP\MVC\App\View;

class HomeController{
    function index(): void{
        $model = [
            'title' => 'PHP OOP',
            'content' => 'PHP OOP Budimansolikin'
        ];
        View::render('Home/index', $model);
    }
    
    function hello(): void {
        echo "HomeController.hello()";
    }
    
    function world(): void {
        echo "HomeController.world()";
    }
    
    function about(): void{
        echo "HomeController.about()";
    }
    
    function login(): void {
        $model = [
            'title' => 'Login',
            'username' => 'Loginlah',
            'password' => 'Loginlah'
        ];
        
        View::render('Home/login', $model);
    }
}

?>