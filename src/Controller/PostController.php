<?php

declare(strict_types=1);

namespace App\Controller;

use App\Handler\Controller\WebController;
use App\Handler\Routing\Attribute\Route;
use App\Repository\PostRepository;
use App\Service\PostService;

class PostController extends WebController
{
    public function __construct(
        private PostService $postService,
        private PostRepository $postRepository,
    ) {
        parent::__construct();
    }

    #[Route('/post', name: 'post_page', methods: ['GET'])]
    public function index()
    {
        return $this->render("post/index.html.twig");
    }

    #[Route('/post', name: 'post_create', methods: ['POST'])]
    public function store()
    {
        echo "create post";
    }

    #[Route('/post/{id<\d+>}', name: 'post_show', methods: ['GET'])]
    public function show(array $parameters)
    {
        $id = (int) $parameters['id'];
        $post = $this->postRepository->findById($id);
        var_dump($post);

        echo "show post";
    }

    #[Route('/post/{id<\d+>}', name: 'post_update', methods: ['PUT', 'PATCH'])]
    public function update(array $parameters)
    {
        $id = (int) $parameters['id'];
        $post = $this->postRepository->findById($id);

        echo "update post";
    }

    #[Route('/post/{id<\d+>}', name: 'post_delete', methods: ['DELETE'])]
    public function delete(array $parameters)
    {
        $id = (int) $parameters['id'];
        $post = $this->postRepository->findById($id);

        echo "delete post";
    }
}
