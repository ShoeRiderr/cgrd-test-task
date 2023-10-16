<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Handler\Controller\WebController;
use App\Handler\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Service\PostService;

class SecurityController extends WebController
{
    public function __construct(private UserRepository $userRepository)
    {
        parent::__construct();
    }

    #[Route('/', name: 'login_page', methods: ['GET'])]
    public function loginPage()
    {
        return $this->render("secret/login.html.twig");
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login()
    {
        header("Location: " . $_ENV['BASE_URL'] . "/post", true, 301);
        echo "login";
    }
}
