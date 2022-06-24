<?php

namespace App\Controller;

use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminHomeController extends AbstractController
{
    /**
     * Route de connexion pour les administrateurs et les membres du BDE
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin" , name: "admin_home",)]
    public function index(Session $session, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        echo $this->twig->render('admin/admin_home.html.twig');
    } 
}