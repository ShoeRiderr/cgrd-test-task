<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
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

    #[Route('/post', name: 'post_page', methods: ['GET'], authRequired: true)]
    public function index()
    {
        /**
         * @var null|Post[] $post
         */
        $posts = $this->postRepository->findAll();

        return $this->render("post/index.html.twig");
    }

    #[Route('/post', name: 'post_create', methods: ['POST'], authRequired: true)]
    public function store()
    {
        echo "create post";
    }

    #[Route('/post/{id<\d+>}', name: 'post_show', methods: ['GET'], authRequired: true)]
    public function show(array $parameters)
    {
        $id = (int) $parameters['id'];

        /**
         * @var null|Post $post
         */
        $post = $this->postRepository->findById($id);

        echo "show post";
    }

    #[Route('/post/{id<\d+>}', name: 'post_update', methods: ['PUT', 'PATCH'], authRequired: true)]
    public function update(array $parameters)
    {
        $id = (int) $parameters['id'];

        /**
         * @var null|Post $post
         */
        $post = $this->postRepository->findById($id);

        echo "update post";
    }

    #[Route('/post/{id<\d+>}', name: 'post_delete', methods: ['DELETE'], authRequired: true)]
    public function delete(array $parameters)
    {
        $id = (int) $parameters['id'];

        /**
         * @var null|Post $post
         */
        $post = $this->postRepository->findById($id);

        echo "delete post";
    }
}
