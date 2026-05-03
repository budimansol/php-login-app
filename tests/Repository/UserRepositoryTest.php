<?php

namespace Budimansol\PHP\MVC\App\Repository;

use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\Config\Database;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase{

    private UserRepository $userRepository;
    private SessionsRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionsRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }
    
    public function testCreateSuccess(): void {
        $user = new User();
        $user->id = 'budiman';
        $user->name = 'Budiman';
        $user->email = 'budiman@mail.com';
        $user->password = 'password';
        
        $this->userRepository->create($user);
        $result= $this->userRepository->getById($user->id); 
        
        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->email, $result->email);
        self::assertEquals($user->password, $result->password);
    }
    
    public function testGetByIdNull(): void {
        $user = $this->userRepository->getById("budiman");
        self::assertNull($user);
    }
    
    public function testUpdate(): void {
        $user = new User();
        $user->id = 'budiman';
        $user->name = 'Budiman';
        $user->email = 'budiman@mail.com';
        $user->password = 'password';

        $this->userRepository->create($user);
        
        $user->name = "Solikin";
        $this->userRepository->update($user);

        $result = $this->userRepository->getById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->email, $result->email);
        self::assertEquals($user->password, $result->password);
    }
}

?>