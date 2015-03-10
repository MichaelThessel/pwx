<?php

namespace App\Controller;

class DefaultController
{
    protected $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function indexAction()
    {
        return $this->twig->render('index.twig');
    }
}
