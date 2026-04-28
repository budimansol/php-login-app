<?php

namespace Budimansol\PHP\MVC\Controller;

use Budimansol\PHP\MVC\App\View;

class HomeController{
    function index(): void{
        $model = [
            'title' => 'PHP Login',
        ];
        View::render('Home/index', $model);
    }
}

?>