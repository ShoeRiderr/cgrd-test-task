<?php

namespace App\Controllers;

use App\Handler\Routing\Attribute\Route;
use App\Service\PostService;

class DashboardController
{
    public function __construct(private PostService $postService)
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
