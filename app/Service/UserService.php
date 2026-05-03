<?php

namespace Budimansol\PHP\MVC\Service;

use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\App\Exception\ValidationException;
use Budimansol\PHP\MVC\App\Model\UserLoginRequest;
use Budimansol\PHP\MVC\App\Model\UserLoginResponse;
use Budimansol\PHP\MVC\App\Model\UserRegisterRequest;
use Budimansol\PHP\MVC\App\Model\UserRegisterResponse;
use Budimansol\PHP\MVC\App\Model\UserUpdatePasswordRequest;
use Budimansol\PHP\MVC\App\Model\UserUpdatePasswordResponse;
use Budimansol\PHP\MVC\App\Model\UserUpdateRequest;
use Budimansol\PHP\MVC\App\Model\UserUpdateResponse;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\Config\Database;
use Exception;

class UserService {
    
    private UserRepository $repository;
    
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function register (UserRegisterRequest $request): UserRegisterResponse{
        $this->validateRegisterRequest($request);
        
        try{
            Database::beginTransaction();
            $user = $this->repository->getById($request->id);
            if ($user != null) {
                throw new ValidationException("User ID is Exist");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->repository->create($user);

            $response = new UserRegisterResponse();
            $response->user = $user;
            Database::commitTransaction();
            return $response;
        } catch (Exception $exception) {
            Database::rollBackTransaction();
            throw $exception;
        }
        
    }
    
    private function validateRegisterRequest(UserRegisterRequest $request){
        if($request->id == null || trim($request->id) == "" ){
            throw new ValidationException("ID can not Blank");
        } else if ($request->name == null || trim($request->name) == ""){
            throw new ValidationException("Name can not Blank");
        } else if ($request->email == null || trim($request->email) == ""){
            throw new ValidationException("Email can not Blank");
        } else if ($request->password == null || trim($request->password) == ""){
            throw new ValidationException("Password can not Blank");
        }
    }
    
    public function login (UserLoginRequest $request): UserLoginResponse {
        $this->validateLoginRequest($request);
        $user = $this->repository->getByEmail($request->email);
        if($user == null){
            throw new ValidationException("Email or Password is Wrong");
        }
        
        if(password_verify($request->password, $user->password)){
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        } else {
            throw new ValidationException("Email or Password is Wrong");
        }
        
    }
    
    private function validateLoginRequest(UserLoginRequest $request) {
        if ($request->email == null || trim($request->email) == "") {
            throw new ValidationException("Email can not Blank");
        } else if ($request->password == null || trim($request->password) == "") {
            throw new ValidationException("Password can not Blank");
        }
    }
    
    public function updateProfile(UserUpdateRequest $request): UserUpdateResponse{
        $this->validateUpdateRequest($request);
        
        try {
            Database::beginTransaction();
            $user = $this->repository->getById($request->id);
            if ($user == null){
                throw new ValidationException("User not Found");
            }
            
            $user->name = $request->name;
            $user->email = $request->email;
            
            $this->repository->update($user);
            Database::commitTransaction();
            
            $response = new UserUpdateResponse();
            $response->user = $user;
            return $response; 
            
        } catch(Exception $exception) {
            Database::rollBackTransaction();
            throw $exception;
        }
        
    }
    
    public function validateUpdateRequest(UserUpdateRequest $request){
        if ($request->id == null || trim($request->id) == "") {
            throw new ValidationException("ID can not Blank");
        } else if ($request->name == null || trim($request->name) == "") {
            throw new ValidationException("Name can not Blank");
        } else if ($request->email == null || trim($request->email) == "") {
            throw new ValidationException("Email can not Blank");
        }
    }
    
    public function updatePasswordUser(UserUpdatePasswordRequest $request) : UserUpdatePasswordResponse{
        $this->validateUpdatePasswordRequest($request);
        try{
            Database::beginTransaction();
            $user = $this->repository->getById($request->id);
            if ($user == null){
                throw new ValidationException("User Not Found");
            }
            
            if (!password_verify($request->oldPassword, $user->password)){
                throw new ValidationException("Old Password is Wrong");
            }
            
            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->repository->update($user);
            Database::commitTransaction();
            $response = new UserUpdatePasswordResponse();
            $response->user = $user;
            return $response;
        } catch (Exception $exception){
            Database::rollBackTransaction();
            throw $exception;
        }
    }
    
    public function validateUpdatePasswordRequest(UserUpdatePasswordRequest $request){
        if ($request->id == null || trim($request->id) == "") {
            throw new ValidationException("ID can not Blank");
        } else if ($request->oldPassword == null || trim($request->oldPassword) == "") {
            throw new ValidationException("Old Password can not Blank");
        } else if ($request->newPassword == null || trim($request->newPassword) == "") {
            throw new ValidationException("New Password can not Blank");
        }
    }
}

?>