<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\PostDTO;
use App\Entity\Post;
use App\Enum\NotificationType;
use App\Exception\Routing\RouteNotFoundException;
use App\Handler\Controller\WebController;
use App\Handler\Routing\Attribute\Route;
use App\Repository\PostRepository;
use App\Service\PostService;
use App\Validation\PostValidation;
use Throwable;

class PostController extends WebController
{
    public function __construct(
        private PostService $postService,
        private PostRepository $postRepository,
        private PostValidation $postValidation
    ) {
        parent::__construct();
    }

    #[Route('/post', name: 'post_page', methods: ['GET'], authRequired: true)]
    public function index()
    {
        /**
         * @var array $posts
         */
        $posts = $this->postRepository->findAll()->getArray();

        $this->render("post/index.html.twig", [
            'posts' => $posts
        ]);
    }

    #[Route('/post', name: 'post_create', methods: ['POST'], authRequired: true)]
    public function store()
    {
        $postDTO = $this->postValidation->validate();

        if (!$postDTO) {
            $this->handleInvalidInputData();
        }

        $result = $this->postService->create($postDTO);

        $result ?
            $this->setNotification('News was successfull created!', NotificationType::SUCCESS) :
            $this->setNotification(self::ERROR_MESSAGE, NotificationType::ERROR);

        header("Location: " . $_ENV['BASE_URL'] . "/post", true, 200);
    }

    #[Route('/post/{id<\d+>}', name: 'post_show', methods: ['GET'], authRequired: true)]
    public function show(array $parameters)
    {
        $id = (int) $parameters['id'];

        $requestHeaders = getallheaders();

        /**
         * @var null|Post $post
         */
        $post = $this->postRepository->findById($id)
            ->getArray();

        if (empty($post)) {
            throw new RouteNotFoundException();
        }

        if (isset($requestHeaders['Content-Type']) && $requestHeaders['Content-Type'] === 'application/json') {
            echo json_encode($post);

            return;
        }

        echo "show post";
    }

    #[Route('/post/{id<\d+>}', name: 'post_update', methods: ['PUT', 'PATCH'], authRequired: true)]
    public function update(array $parameters)
    {
        $id = (int) $parameters['id'];

        $postDTO = $this->postValidation->validate();

        if (!$postDTO) {
            $this->handleInvalidInputData();
        }

        /**
         * @var null|Post $post
         */
        $post = $this->postRepository->findById($id)
            ->getOneEntityFromArray();

        $result = $this->postService->update($postDTO, $post);

        $result ?
            $this->setNotification('News was successfull changed!', NotificationType::SUCCESS) :
            $this->setNotification(self::ERROR_MESSAGE, NotificationType::ERROR);

        header("Location: " . $_ENV['BASE_URL'] . "/post", true, 200);
    }

    #[Route('/post/{id<\d+>}', name: 'post_delete', methods: ['DELETE'], authRequired: true)]
    public function delete(array $parameters)
    {
        try {
            $id = (int) $parameters['id'];

            /**
             * @var bool $result
             */
            $result = $this->postService->delete($id);

            $result ?
                $this->setNotification('News was deleted!', NotificationType::SUCCESS) :
                $this->setNotification(self::ERROR_MESSAGE, NotificationType::ERROR);


            header("Location: " . $_ENV['BASE_URL'] . "/post", true, 200);
        } catch (Throwable $e) {
            error_log($e->getMessage());

            $this->setNotification(self::ERROR_MESSAGE, NotificationType::ERROR);

            header("Location: " . $_ENV['BASE_URL'] . "/post", true, 422);
        }
    }
}
