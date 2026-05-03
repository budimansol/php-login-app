<?php

namespace Budimansol\PHP\MVC\App\Repository;

use Budimansol\PHP\MVC\App\Domain\Sessions;
use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\Config\Database;
use PHPUnit\Framework\TestCase;

class SessionsRepositoryTest extends TestCase {
    private SessionsRepository $sessionRepository;
    private UserRepository $userRepository;
    protected function setUp(): void
    {
        $this->sessionRepository = new SessionsRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
        
        $user = new User();
        $user->id = "budiman";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = "Password";
        $this->userRepository->create($user);
        
    }
    
    public function testCreateSuccess() : void {
        
        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = "budiman";
        
        $this->sessionRepository->create($session);
        
        $result = $this->sessionRepository->getById($session->id);
        
        self::assertEquals($result->id, $session->id);
        self::assertEquals($result->user_id, $session->user_id);
    }
    
    public function testDeleteByIdSuccess(): void {
        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = "budiman";

        $this->sessionRepository->create($session);

        $result = $this->sessionRepository->getById($session->id);

        self::assertEquals($result->id, $session->id);
        self::assertEquals($result->user_id, $session->user_id);
        
        $result = $this->sessionRepository->deleteById($session->id);
        self::assertNull($result);
    }
    
    public function testFindNotFound(): void {
        $result = $this->sessionRepository->deleteById("notfound");
        self::assertNull($result);
    }
}

?>