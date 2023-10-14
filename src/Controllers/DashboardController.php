<?php

namespace App\Controllers;

use App\Routing\Attribute\Route;

class DashboardController
{
    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function index()
    {
        echo "dashboard";
    }

    #[Route('/test', name: 'article-comment')]
    public function comment()
    {
        echo "test";
    }
}
