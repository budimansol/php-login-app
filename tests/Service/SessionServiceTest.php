<?php

namespace Budimansol\PHP\MVC\Service;

use Budimansol\PHP\MVC\App\Domain\Sessions;
use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\Config\Database;
use PHPUnit\Framework\TestCase;

function setcookie(string $name, string $value){
    echo "$name: $value";
}

class SessionServiceTest extends TestCase {

    private SessionService $sessionService;
    private SessionsRepository $sessionRepository;
    private UserRepository $userRepository;
    
    public function setUp(): void {
        $this->sessionRepository = new SessionsRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);
        
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "budiman";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = "Password";
        $this->userRepository->create($user);
    }
    
    public function testCreate (){
        $session = $this->sessionService->create("budiman"); 
        $this->expectOutputRegex("[BUDIMAN-COOKIE: $session->id]");
        
        $result = $this->sessionRepository->getById($session->id);
        
        self::assertEquals("budiman", $result->user_id);
    }
    
    public function testDestroy(){
        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = "budiman";
        
        $this->sessionRepository->create($session);
        
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $this->sessionService->destroy();
        
        $this->expectOutputRegex("[BUDIMAN-COOKIE:]");
        
        $result = $this->sessionRepository->getById($session->id);
        self::assertNull($result);
    }
    
    public function testCurrent() {
        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = "budiman";
        
        $this->sessionRepository->create($session);
        
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $user = $this->sessionService->current();
        
        self::assertEquals($session->user_id, $user->id);
    }

}

?>