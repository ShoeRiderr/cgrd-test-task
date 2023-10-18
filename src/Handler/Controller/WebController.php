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

    protected function setNotification(string $message, NotificationType $type)
    {
        $_SESSION['flashMessage'] = $message;
        $_SESSION['flashMessageType'] = $type->value;
    }

    protected function handleInvalidInputData()
    {
        $this->setNotification(self::INVALID_INPUT_MESSAGE, NotificationType::ERROR);
        header("Location: " . $_ENV['BASE_URL'] . "/post", true, 422);
    }
}
