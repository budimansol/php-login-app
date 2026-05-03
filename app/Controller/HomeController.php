<?php

namespace Budimansol\PHP\MVC\Controller;

use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\App\View;
use Budimansol\PHP\MVC\Config\Database;
use Budimansol\PHP\MVC\Service\SessionService;

class HomeController{

    private SessionService $sessionService;
    
    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionsRepository($connection);
        $userRepository = new UserRepository($connection);
        
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function index(): void{
        
        $user = $this->sessionService->current();
        
        if ($user == null){
            View::render('Home/index', [
                'title' => 'PHP Login'
            ]);
        } else {
            View::render('Home/dashboard', [
                'title' => 'PHP Dashboard',
                'user' => [
                    "name" => $user->name
                    ]
            ]);
        }
        
    }
}

?>