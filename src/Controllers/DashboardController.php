<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Handler\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Service\PostService;

class DashboardController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function index()
    {
        var_dump($this->userRepository->fetchAll());
        echo "dashboard";
    }

    #[Route('/test', name: 'article-comment', methods: ['GET'])]
    public function comment()
    {
        echo "test";
    }
}
