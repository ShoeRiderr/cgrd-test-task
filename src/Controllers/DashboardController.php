<?php

namespace App\Controllers;

use App\Entity\User;
use App\Handler\Entity\EntityManager;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;

class DashboardController
{
    public function __construct()
    {
    }

    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function index()
    {
        echo "dashboard";
    }

    #[Route('/test', name: 'article-comment', methods: ['GET'])]
    public function comment()
    {
        echo "test";
    }
}
