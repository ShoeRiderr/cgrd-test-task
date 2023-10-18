<?php

declare(strict_types=1);

namespace App\Handler\Controller;

use App\App;
use App\Enum\NotificationType;
use Twig\Environment as TwigEnvironment;

abstract class WebController
{
    protected const ERROR_MESSAGE = 'Something went wrong! Try later';
    protected const INVALID_INPUT_MESSAGE = 'All fields are required!';

    protected TwigEnvironment $twig;

    public function __construct()
    {
        $this->twig = App::twig();
    }

    /**
     * Renders twig template
     */
    protected function render(string $view, array $params = []): string
    {
        $flashMessage = $_SESSION['flashMessage'] ?? null;
        $flashMessageType = $_SESSION['flashMessageType'] ?? null;

        unset($_SESSION['flashMessage']);
        unset($_SESSION['flashMessageType']);

        if ($flashMessage && $flashMessageType) {
            $params['flashMessage'] = $flashMessage;
            $params['flashMessageType'] = $flashMessageType;
        }

        $result = $this->twig->render($view, $params);

        echo $result;

        return $result;
    }

    /**
     * Set message type and content which will be showed on the page as notification
     */
    protected function setFlashMessage(string $message, NotificationType $type)
    {
        $_SESSION['flashMessage'] = $message;
        $_SESSION['flashMessageType'] = $type->value;
    }

    /**
     * Action for invalid request data
     */
    protected function handleInvalidInputData()
    {
        $this->setFlashMessage(self::INVALID_INPUT_MESSAGE, NotificationType::ERROR);

        header("Location: " . $_ENV['BASE_URL'] . "/post", true, 422);
    }
}
