<?php

namespace Budimansol\PHP\MVC\Controller;

use Budimansol\PHP\MVC\App\Domain\Sessions;
use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\Config\Database;
use Budimansol\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class UserControllerTest extends TestCase
{
    private UserController $userController;
    private UserRepository $userRepository;
    private SessionsRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->userController = new UserController();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionsRepository(Database::getConnection());

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
        putenv("mode=test");
    }

    public function testRegister()
    {
        $this->userController->register();

        $this->expectOutputRegex('[Register New User]');
        $this->expectOutputRegex('[ID]');
        $this->expectOutputRegex('[Name]');
        $this->expectOutputRegex('[Email]');
        $this->expectOutputRegex('[Password]');
        $this->expectOutputRegex('[Register]');
    }

    public function testPostRegisterSuccess()
    {

        $_POST['id'] = '1';
        $_POST['name'] = 'budiman';
        $_POST['email'] = 'budiman@mail.com';
        $_POST['password'] = 'budiman';
        $this->userController->postRegister();
        $this->expectOutputString('Location: /users/login');
    }

    public function testPostRegisterFailed()
    {
        $_POST['id'] = '1';
        $_POST['name'] = 'budiman';
        $_POST['email'] = '';
        $_POST['password'] = 'budiman';

        $this->userController->postRegister();
        $this->expectOutputRegex('[Register New User]');
        $this->expectOutputRegex('[ID]');
        $this->expectOutputRegex('[Name]');
        $this->expectOutputRegex('[Email]');
        $this->expectOutputRegex('[Password]');
        $this->expectOutputRegex('[Register]');
        $this->expectOutputRegex('[Email can not Blank]');
    }

    public function testPostRegisterDuplicate()
    {

        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = "password";

        $this->userRepository->create($user);

        $_POST['id'] = '1';
        $_POST['name'] = 'budiman';
        $_POST['email'] = 'budiman@mail.com';
        $_POST['password'] = 'budiman';

        $this->userController->postRegister();
        $this->expectOutputRegex('[Register New User]');
        $this->expectOutputRegex('[ID]');
        $this->expectOutputRegex('[Name]');
        $this->expectOutputRegex('[Email]');
        $this->expectOutputRegex('[Password]');
        $this->expectOutputRegex('[Register]');
        $this->expectOutputRegex('[User ID is Exist]');
    }

    public function testLogin()
    {
        $this->userController->login();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Email]");
        $this->expectOutputRegex("[Password]");
    }

    public function testLoginSuccess()
    {
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);

        $this->userRepository->create($user);

        $_POST['email'] = "budiman@mail.com";
        $_POST['password'] = "password";

        $this->userController->postLogin();
        $this->expectOutputString('Location: /');
    }

    public function testValidationException()
    {

        $_POST['email'] = "";
        $_POST['password'] = "";

        $this->userController->postLogin();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Email]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Email can not Blank]");
    }

    public function testUserNotFound()
    {
        $_POST['email'] = "abc@mail.com";
        $_POST['password'] = "abcdefgh";

        $this->userController->postLogin();

        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Email]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Email or Password is Wrong]");
    }

    public function testLoginWrongPassword()
    {
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);

        $this->userRepository->create($user);

        $_POST['email'] = "budiman@mail.com";
        $_POST['password'] = "baudi";

        $this->userController->postLogin();
        $this->expectOutputRegex("[Login User]");
        $this->expectOutputRegex("[Email]");
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Email or Password is Wrong]");
    }
    
    public function testLogout(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->create($user);
        
        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $this->userController->logout();
        
        $this->expectOutputRegex("[Location: /]");
        $this->expectOutputRegex("[BUDIMAN-COOKIE: ]");
    }
    
    public function testUpdateProfile(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $this->userController->updateProfile();
        
        $this->expectOutputRegex("[Profile]");
        $this->expectOutputRegex("[ID]");
        $this->expectOutputRegex("[1]");
        $this->expectOutputRegex("[Name]");
        $this->expectOutputRegex("[Budiman]");
        $this->expectOutputRegex("[Email]");
        $this->expectOutputRegex("[budiman@mail.com]");
    }
    
    public function testPostUpdateProfile(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['name'] = "Budimansol";
        $_POST['email'] = "budimansol@example.com";
        $this->userController->postUpdateProfile();

        $this->expectOutputRegex("[Location: /]");

        $result = $this->userRepository->getById($user->id);
        self::assertEquals($_POST['name'], $result->name);
        self::assertEquals($_POST['email'], $result->email);
    }
    
    public function testPostUpdateProfileValidationError(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['name'] = "";
        $_POST['email'] = "";
        $this->userController->postUpdateProfile();
        $this->expectOutputRegex("[Profile]");
        $this->expectOutputRegex("[ID]");
        $this->expectOutputRegex("[1]");
        $this->expectOutputRegex("[Name]");
        $this->expectOutputRegex("[Budiman]");
        $this->expectOutputRegex("[Email]");
        $this->expectOutputRegex("[budiman@mail.com]");
        $this->expectOutputRegex("[Name can not Blank]");
    }
    
    public function testUpdatePassword(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $this->userController->updatePassword();
        
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[1]");
    }
    
    public function testUpdatePasswordSuccess(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;
        
        $_POST['oldPassword'] = "password";
        $_POST['newPassword'] = "procadet";

        $this->userController->postUpdatePassword();
        
        $this->expectOutputRegex("[Location: /]");

        $result = $this->userRepository->getById($user->id);
        self::assertTrue(password_verify($_POST['newPassword'], $result->password));
    }
    
    public function testUpdatePasswordValidateError(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['oldPassword'] = "";
        $_POST['newPassword'] = "";

        $this->userController->postUpdatePassword();
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[1]");
        $this->expectOutputRegex("[Old Password can not Blank] ");        
    }
    
    public function testUpdatePasswordWrongOldPassword(){
        $user = new User();
        $user->id = "1";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = password_hash("password", PASSWORD_BCRYPT);
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);
        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $_POST['oldPassword'] = "budimansol";
        $_POST['newPassword'] = "budimin";

        $this->userController->postUpdatePassword();
        $this->expectOutputRegex("[Password]");
        $this->expectOutputRegex("[Id]");
        $this->expectOutputRegex("[1]");
        $this->expectOutputRegex("[Old Password is Wrong] ");
    }
        
}