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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/evenement/create" , name: "admin_evenement_create",)]
    public function createEvenement()
    {
        echo $this->twig->render('admin/admin_home.html.twig');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/evenements" , name: "admin_evenements",)]
    public function evenements()
    {
        echo $this->twig->render('admin/evenements/admin_evenements.html.twig');
    }


    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/ecoles" , name: "admin_ecoles",)]
    public function ecoles(UtilisateursRepository $userRepository)
    {
        $filters = [];
        if (empty($filters)) {
            $users = $userRepository->selectAll();
        }
        echo $this->twig->render('admin/admin_home.html.twig');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/ecoles" , name: "admin_ecoles",)]
    public function ecolesFilter(Request $request,UtilisateursRepository $userRepository)
    {
        $users = $userRepository->selectAll();
        $users = $userRepository->selectAll();
        if (empty($filters)) {
            $users = $userRepository->selectAll();
        }
        echo $this->twig->render('admin/admin_home.html.twig');
    }
}