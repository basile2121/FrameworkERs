<?php

namespace App\Controller;

use App\Repository\EcolesRepository;
use App\Repository\PromotionsRepository;
use App\Repository\RolesRepository;
use App\Repository\UserRepository;
use App\Repository\UtilisateursRepository;
use App\Entity\Ecoles;
use App\Routing\Attribute\Route;
use DateTime;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EcoleController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/ecoles", name: "admin_ecoles",)]
    public function ecoles(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository)
    {
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();
       

        echo $this->twig->render('admin/ecoles/admin_ecoles.html.twig', [
            'ecoles' => $ecoles,
            'promotions' => $promotions,
            
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/ecoles/filter", httpMethod: 'POST', name: "admin_ecoles_filter",)]
    public function ecolesFilter(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository)
    {
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();
        

        $conditions = [];
        $parameters = [];
        $filtres = [];

        if (!empty($_POST['filtre_Name'])) {
            $filtres['filtre_Name'] = $_POST['filtre_Name'];
            $conditions[] = 'nom_ecole LIKE ?';
            $parameters[] = '%'.$_POST['filtre_Name']."%";
        }


        $ecoles = $ecolesRepository->filterEcole($conditions, $parameters);
        echo $this->twig->render('admin/ecoles/admin_ecoles.html.twig', [
            'ecoles' => $ecoles,
            'promotions' => $promotions,
            'filtres' => $filtres
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/ecoles", httpMethod: 'POST', name: "admin_edit_ecoles",)]
    public function editEcoles(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository)
    {
        $id = intval($_POST['id']);
        $ecole = $ecolesRepository->selectOneById($id);
        $promotions = $promotionsRepository->selectAll();
        

        echo $this->twig->render('admin/ecoles/admin_form_edit_ecole.html.twig', [
            'ecole' => $ecole,
            'promotions' => $promotions,
            
        ]);
    }


    /**
     * @throws \Exception
     */
    #[Route(path: "/admin/update/ecoles", httpMethod: 'POST', name: "admin_update_ecoles",)]
    public function updateEcoles(EcolesRepository $ecolesRepository)
    {
        $ecole = $ecolesRepository->selectOneById(intval($_POST['id']));

        $ecole->setNomEcole($_POST['nomEcole']);
        $ecolesRepository->update($ecole);

        header('Location: /admin/ecoles');
    }


    #[Route(path: "/admin/delete/ecoles", httpMethod: 'POST', name: "admin_delete_ecoles")]
    public function deleteEcoles(EcolesRepository $ecolesRepository, PromotionsRepository $promotionsRepository)
    {
        $id = intval($_POST['id']);
        $ecolesRepository->delete($id);
        header('Location: /admin/ecoles');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/ecoles", name: "admin_create_ecole",)]
    public function createEcoles(EcolesRepository $ecolesRepository)
    {
        $ecoles = $ecolesRepository->selectAll();
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $urlRedirection = $_SERVER['HTTP_REFERER'];
        } else {
            $urlRedirection = '/admin/ecoles';
        }

        echo $this->twig->render('admin/ecoles/admin_form_create_ecole.html.twig', [
            'ecoles' => $ecoles,
            'urlRedirection' => $urlRedirection
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError|Exception
     */
    #[Route(path: "/admin/add/ecoles", httpMethod: 'POST', name: "admin_add_ecoles",)]
    public function addEcoles(EcolesRepository $ecolesRepository)
    {
        $ecoles = new Ecoles();
        $ecoles->setNomEcole($_POST['nomEcole']);
        
        $ecolesRepository->save($ecoles);
        header('Location:' . $_POST['redirect_create_adresse_url']);
    }
}