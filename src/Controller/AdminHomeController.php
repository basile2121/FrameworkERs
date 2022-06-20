<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Repository\EcolesRepository;
use App\Repository\PromotionsRepository;
use App\Repository\RolesRepository;
use App\Repository\UserRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminHomeController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin" , name: "admin_home",)]
    public function index()
    {
        echo $this->twig->render('admin/admin_home.html.twig');
    } 
}