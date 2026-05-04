<?php

namespace Budimansol\PHP\MVC\Service;

use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\App\Exception\ValidationException;
use Budimansol\PHP\MVC\App\Model\UserLoginRequest;
use Budimansol\PHP\MVC\App\Model\UserRegisterRequest;
use Budimansol\PHP\MVC\App\Model\UserUpdatePasswordRequest;
use Budimansol\PHP\MVC\App\Model\UserUpdateRequest;
use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\Config\Database;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase{
    private UserService $userService;
    private UserRepository $userRepository;
    private SessionsRepository $sessionRepository;
    
    protected function setUp(): void
    {
        $conn = Database::getConnection();
        $this->userRepository = new UserRepository($conn);
        $this->sessionRepository = new SessionsRepository($conn);
        $this->userService = new UserService($this->userRepository);
        
        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }
    
    public function testRegisterSuccess(){
        $request = new UserRegisterRequest();
        $request->id = "budiman";
        $request->name = "Budiman";
        $request->email = "budiman@mail.com";
        $request->password = "budiman123";
        
        $response = $this->userService->register($request);
        
        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertEquals($request->email, $response->user->email);
        self::assertTrue(password_verify($request->password, $response->user->password)); 
    }
    
    public function testRegisterFailed(){
        $this->expectException(ValidationException::class);
        
        $request = new UserRegisterRequest();
        $request->id = "";
        $request->name = "Budiman";
        $request->email = "budiman@mail.com";
        $request->password = "budiman123";

        $response = $this->userService->register($request);
    }
    
    public function testRegisterWeak(){
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "123";
        $request->name = "Budiman";
        $request->email = "budiman@mail.com";
        $request->password = "123";

        $response = $this->userService->register($request);
    }
    
    public function testDuplicate(){
        $user = new User();
        $user->id = "budiman";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = "budiman123";

        $this->userRepository->create($user);
        
        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = "budiman";
        $request->name = "Budiman";
        $request->email = "budiman@mail.com";
        $request->password = "budiman123";

        $response = $this->userService->register($request);
    }
    
    public function testLoginSuccess(){
        $user = new User();
        $user->id = "1";
        $user->name = "Name";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("budiman", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $request = new UserLoginRequest();
        $request->email = "budiman@mail.com";
        $request->password = "budiman";

        $response = $this->userService->login($request);
        self::assertEquals($request->email, $response->user->email);
        self::assertTrue(password_verify($request->password,$response->user->password));
    }
    
    public function testLoginNotFound(){
        $this->expectException(ValidationException::class);
        
        $request = new UserLoginRequest();
        $request->email = 'budiman@mail.com';
        $request->password = "Password";
        
        $this->userService->login($request);
    }
    
    public function testLoginWrong(){
    
        $user = new User();
        $user->id = "1";
        $user->name = "Name";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("budiman",PASSWORD_BCRYPT);
        $this->userRepository->create($user);
        
        $this->expectException(ValidationException::class);
        
        $request = new UserLoginRequest();
        $request->email = "budiman@mail.com";
        $request->password = "Kosong";
        
        $this->userService->login($request);
    }
    
    public function testUpdateSuccess(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("budiman", PASSWORD_BCRYPT);
        $this->userRepository->create($user);
        
        $request = new UserUpdateRequest();
        $request->id = "1";
        $request->name = "Alex";
        $request->email = "example@mail.com";
        $this->userService->updateProfile($request);
        $result = $this->userRepository->getById($request->id);
        
        $this->assertEquals($request->id, $result->id);
    }
    
    public function testUpdateValidationError(){
        $this->expectException(ValidationException::class);    
    
        $request = new UserUpdateRequest();
        $request->id = "";
        $request->name = "";
        $request->email = "";
        $this->userService->updateProfile($request);
    }
    
    public function testUpdateNotFound(){
        $this->expectException(ValidationException::class);

        $request = new UserUpdateRequest();
        $request->id = "tes";
        $request->name = "tes";
        $request->email = "tes";
        $this->userService->updateProfile($request);
    }
    
    public function testUpdatePasswordSuccess(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("budiman", PASSWORD_BCRYPT);
        $this->userRepository->create($user);
        
        $request = new UserUpdatePasswordRequest();
        $request->id = "1";
        $request->oldPassword = "budiman";
        $request->newPassword = "November1@";
        $this->userService->updatePasswordUser($request);
        
        $result = $this->userRepository->getById($user->id);
        
        $this->assertTrue(password_verify($request->newPassword, $result->password));
    }
    
    public function testUpdatePasswordValidationError(){
        $this->expectException(ValidationException::class);

        $request = new UserUpdatePasswordRequest();
        $request->id = "1";
        $request->oldPassword = "";
        $request->newPassword = "";
        $this->userService->updatePasswordUser($request);
    }
    
    public function testUpdatePasswordWrongOldPassword(){
        $this->expectException(ValidationException::class);

        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("budiman", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $request = new UserUpdatePasswordRequest();
        $request->id = "1";
        $request->oldPassword = "salah";
        $request->newPassword = "November1@";
        $this->userService->updatePasswordUser($request);
    }
    
    public function testUpdatePasswordNotFound(){
        $this->expectException(ValidationException::class);

        $request = new UserUpdatePasswordRequest();
        $request->id = "1";
        $request->oldPassword = "salah";
        $request->newPassword = "November1@";
        $this->userService->updatePasswordUser($request);
    }
}

?>