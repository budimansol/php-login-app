<?php

namespace Budimansol\PHP\MVC\Service;

use Budimansol\PHP\MVC\App\Domain\Sessions;
use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;

class SessionService {
    
    public static string $COOKIE_NAME = "BUDIMAN-COOKIE";

    private SessionsRepository $sessionRepository;
    private UserRepository $userRepository;
    
    public function __construct(SessionsRepository $sessionRepository, UserRepository $userRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->userRepository = $userRepository;
    }
    
    public function create(string $user_id): Sessions {
        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user_id;
        
        $this->sessionRepository->create($session);
        
        setcookie(self::$COOKIE_NAME, $session->id, time() + (60 * 60 * 7), "/" );
        return $session;
    }
    
    public function destroy() {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        $this->sessionRepository->deleteById($sessionId);
        
        setcookie(self::$COOKIE_NAME, '', 1, '/');
    }
    
    public function current() : ?User {
        $sessionId = $_COOKIE[self::$COOKIE_NAME] ?? '';
        
        $session = $this->sessionRepository->getById($sessionId);
        if ($session == null) {
            return null;
        }
        
        return $this->userRepository->getById($session->user_id);
    }

}

?>