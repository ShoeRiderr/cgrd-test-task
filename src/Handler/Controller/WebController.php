<?php

declare(strict_types = 1);

namespace App\Handler\Controller;

use App\App;
use Twig\Environment as TwigEnvironment;

abstract class WebController
{
    protected TwigEnvironment $twig;

    public function __construct()
    {
        $this->twig = App::twig();
    }

    protected function render(string $view, array $params = []): string
    {
        $result = $this->twig->render($view, $params);

        echo $result;

        return $result;
    }
}