<?php

namespace Budimansol\PHP\MVC\Middleware;

use Budimansol\PHP\MVC\App\Domain\Sessions;
use Budimansol\PHP\MVC\App\Domain\User;
use Budimansol\PHP\MVC\App\Repository\SessionsRepository;
use Budimansol\PHP\MVC\App\Repository\UserRepository;
use Budimansol\PHP\MVC\Config\Database;
use Budimansol\PHP\MVC\Service\SessionService;
use PHPUnit\Framework\TestCase;

class MustNotLoginMiddlewareTest extends TestCase
{
    private MustNotLoginMiddleware $middleware;
    private UserRepository $userRepository;
    private SessionsRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionRepository = new SessionsRepository(Database::getConnection());
        $this->middleware = new MustNotLoginMiddleware();
        putenv("mode=test");

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();
    }

    public function testBeforeGuest(): void
    {
        $this->middleware->before();
        $this->expectOutputString("");
    }

    public function testBeforeLoginUser(): void
    {
        $user = new User();
        $user->id = "budiman";
        $user->name = "Budiman";
        $user->email = "budiman@mail.com";
        $user->password = "Password";
        $this->userRepository->create($user);

        $session = new Sessions();
        $session->id = uniqid();
        $session->user_id = $user->id;
        $this->sessionRepository->create($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->middleware->before();
        $this->expectOutputRegex("");
    }
}