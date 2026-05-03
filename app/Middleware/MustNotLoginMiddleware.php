<?php

namespace Budimansol\PHP\MVC\Middleware;

use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\App\View;
use Budimansol\PHP\MVC\Config\Database;
use Budimansol\PHP\MVC\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{

    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionsRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    public function before(): void
    {
        $user = $this->sessionService->current();

        if ($user != null) {
            View::redirect("/");
        }
    }
}