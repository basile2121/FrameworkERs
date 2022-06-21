<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use DateTime;

class IndexController extends AbstractController
{
    #[Route(path: "/")]
    public function index(UtilisateursRepository $utilisateursRepository)
    {
        var_dump($utilisateursRepository->selectOneById(281));
    }


    /**
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\LoaderError
     */
    #[Route(path: "/base", name: "base", httpMethod: "GET")]
    public function base()
    {
        echo $this->twig->render('base.html.twig');
    }
}
