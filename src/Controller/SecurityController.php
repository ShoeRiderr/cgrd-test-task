<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\LoginDTO;
use App\Enum\NotificationType;
use App\Exception\Security\BadCredentialsException;
use App\Handler\Controller\WebController;
use App\Handler\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Service\SecurityService;
use App\Validation\LoginValidation;
use Throwable;

class SecurityController extends WebController
{
    protected const INVALID_CREDENTIALS_MESSAGE = 'Wrong Login Data!';

    public function __construct(
        private UserRepository $userRepository,
        private SecurityService $securityService,
        private LoginValidation $loginValidation
    ) {
        parent::__construct();
    }

    #[Route('/', name: 'login_page', methods: ['GET'])]
    public function loginPage()
    {
        if (isUserLoggedIn()) {
            header("Location: " . $_ENV['BASE_URL'] . "/post", true, 301);
        }

        return $this->render("secret/login.html.twig");
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login()
    {
        $loginDTO = $this->loginValidation->validate();

        if (!$loginDTO) {
            $this->handleLoginInvalidCredentials();
        }

        try {
            $this->securityService->login($loginDTO);
        } catch (BadCredentialsException $e) {
            error_log($e->getMessage());

            $this->handleLoginInvalidCredentials();

            return;
        }

        header("Location: " . $_ENV['BASE_URL'] . "/post");
    }

    #[Route('/logout', name: 'logout', methods: ['GET', 'POST'])]
    public function logout(): void
    {
        if (isUserLoggedIn()) {
            unset($_SESSION['id'], $_SESSION['name']);

            session_destroy();

            header("Location: " . $_ENV['BASE_URL'] . "", true, 301);
        }
    }

    private function handleLoginInvalidCredentials(): void
    {
        $this->setNotification(self::INVALID_CREDENTIALS_MESSAGE, NotificationType::ERROR);

        header('Location: ' . $_ENV['BASE_URL'] . '', true, 422);
    }
}
