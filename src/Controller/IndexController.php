<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use DateTime;

class IndexController extends AbstractController
{
    #[Route(path: "/", name: "accueil")]
    public function index(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        if(!empty($_SESSION)){
            $user = $utilisateursRepository->selectOneById($_SESSION["id"]);

            echo $this->twig->render('home/home.html.twig', [
                'sessionSuccess' => $session->get('success'),
                'sessionId' => $session->get('id'),
                'connected' => $session->get('connected')
            ]);

            $session->delete('success');
            $session->delete('connected');
        } else {
            echo $this->twig->render('home/home.html.twig');
        }
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
