<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\LoginDTO;
use App\Exception\Security\BadCredentialsException;
use App\Handler\Controller\WebController;
use App\Handler\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Service\SecurityService;
use Throwable;

class SecurityController extends WebController
{
    public function __construct(
        private UserRepository $userRepository,
        private SecurityService $securityService
    ) {
        parent::__construct();
    }

    #[Route('/', name: 'login_page', methods: ['GET'])]
    public function loginPage()
    {
        if (is_user_logged_in()) {
            header("Location: " . $_ENV['BASE_URL'] . "/post", true, 301);
        }

        return $this->render("secret/login.html.twig");
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login()
    {
        try {
            $loginDTO = new LoginDTO(
                $_POST['name'],
                $_POST['password'],
            );
        } catch (Throwable $e) {
            error_log($e->getMessage());

            header("Location: " . $_ENV['BASE_URL'] . "", true, 422);
        }

        try {
            $this->securityService->login($loginDTO);
        } catch (BadCredentialsException) {
            error_log($e->getMessage());

            header('Location: ' . $_ENV['BASE_URL'] . '', true, 301);

            return;
        }

        $_SESSION['notification_message'] = 'dupa';
        $_SESSION['notification_message_type'] = 'success';
        header("Location: " . $_ENV['BASE_URL'] . "/post");
    }

    #[Route('/logout', name: 'logout', methods: ['GET', 'POST'])]
    public function logout(): void
    {
        if (is_user_logged_in()) {
            unset($_SESSION['id'], $_SESSION['name']);

            session_destroy();

            header("Location: " . $_ENV['BASE_URL'] . "", true, 301);
        }
    }
}
