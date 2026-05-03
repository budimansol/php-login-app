<?php

namespace Budimansol\PHP\MVC\Controller;

use Budimansol\PHP\MVC\App\Exception\ValidationException;
use Budimansol\PHP\MVC\App\Model\UserLoginRequest;
use Budimansol\PHP\MVC\App\Model\UserRegisterRequest;
use Budimansol\PHP\MVC\App\Model\UserUpdatePasswordRequest;
use Budimansol\PHP\MVC\App\Model\UserUpdateRequest;
use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\App\View;
use Budimansol\PHP\MVC\Config\Database;
use Budimansol\PHP\MVC\Service\SessionService;
use Budimansol\PHP\MVC\Service\UserService;
use Exception;

class UserController {
    
    private UserService $userService;
    private SessionService $sessionService;
    
    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
        
        $sessionRepository = new SessionsRepository($connection);
        $this->sessionService = new SessionService ($sessionRepository, $userRepository);
    }

    public function register(){
        View::render('Users/register', [
            "title" => "Register New User"
        ]);
    }
    
    public function postRegister(){
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];
        
        try {
            $this->userService->register($request);
            View::redirect("/users/login");
            
        } catch (ValidationException $exception){
            View::render('Users/register', [
                "title" => "Register New User",
                "error" => $exception->getMessage()
            ]);
        }
    }
    
    public function login(){
        View::render('/Users/login', [
            "title" => "Login User"
        ]);
    }
    
    public function postLogin(){
        $request = new UserLoginRequest();
        $request->email = $_POST['email'];
        $request->password = $_POST['password'];
        
        try{
            $response = $this->userService->login($request);
            $this->sessionService->create($response->user->id);
            View::redirect("/");
        } catch (ValidationException $exception) {
            View::render('Users/login', [
                "title" => "Login User",
                "error" => $exception->getMessage()
            ]);
        }
    }
    
    public function logout(){
        $this->sessionService->destroy();
        View::redirect('/');
    }
    
    public function updateProfile(){
        $user = $this->sessionService->current();
        View::render('/Users/profile', [
            "title" => "Update Profile",
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email
            ]
        ]);
    }
    
    public function postUpdateProfile(){
        $user = $this->sessionService->current();
        $request = new UserUpdateRequest();
        $request->id = $user->id;
        $request->name = $_POST['name'];
        $request->email = $_POST['email'];
        
        try {
            $this->userService->updateProfile($request);
            View::redirect('/');
        } catch (Exception $exception) {
            View::render('Users/profile', [
                "title" => "Update Profile",
                "error" => $exception->getMessage(),
                "user" => [
                    "id" => $user->id,
                    "name" =>  $user->name,
                    "email" => $user->email
                ]
            ]);
        }
    }
    
    public function updatePassword(){
        $user = $this->sessionService->current();
        View::render('Users/password',[
            "user" => [
                "id" => $user->id 
            ]
        ]);
    }
    
    public function postUpdatePassword(){
        $user = $this->sessionService->current();
        $request = new UserUpdatePasswordRequest();
        $request->id = $user->id;
        $request->oldPassword = $_POST['oldPassword'];
        $request->newPassword = $_POST['newPassword'];

        try {
            $this->userService->updatePasswordUser($request);
            View::redirect('/');
        } catch (Exception $exception) {
            View::render('Users/password', [
                "title" => "Update Password",
                "error" => $exception->getMessage(),
                "user" => [
                    "id" => $user->id,
                ]
            ]);
        }
    }

}

?>