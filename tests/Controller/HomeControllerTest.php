<?php

namespace Budimansol\PHP\MVC\Controller;

use Budimansol\PHP\MVC\App\Domain\Sessions;
use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\Config\Database;
use Budimansol\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class HomeControllerTest extends TestCase {
    private HomeController $homeController;
    private UserRepository $userRepository;
    private SessionsRepository $sessionRepository;
    
    protected function setUp(): void
    {
        $this->homeController = new HomeController();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionsRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
    
    public function testGuest(){
        $this->homeController->index();

        $this->expectOutputRegex('[Login Management]');
    }
    
    public function testDashboard(){
        $user = new User();
        $user->id = "budiman";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = "Password";
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = "budiman";
        $this->sessionRepository->create($session);
        
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $this->homeController->index();
        $this->expectOutputRegex('[Hello Budiman]');
        $this->expectOutputRegex('[Profile]');
        $this->expectOutputRegex('[Password]');
    }
}

?>