<?php

namespace App\Controller;

use App\Repository\EcolesRepository;
use App\Repository\PromotionsRepository;
use App\Entity\Ecoles;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use PHPUnit\Util\Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EcoleController extends AbstractController
{
    /**
     * Route admin pour lister toutes les écoles
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/ecoles", httpMethod: 'GET',  name: "admin_ecoles",)]
    public function ecoles(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');

        // Récupération des utilisateurs et des promotions pour les selects
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();

        echo $this->twig->render('admin/ecoles/admin_ecoles.html.twig', [
            'ecoles' => $ecoles,
            'impossible' => $session->get('impossible'),
            'promotions' => $promotions
        ]);

        // Suppresion des pop-ups
        $session->delete('impossible');
    }

    /**
     * Route de filtrage des écoles en GET
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/ecoles/filter", httpMethod: 'GET', name: "admin_ecoles_filter",)]
    public function ecolesFilter(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $promotions = $promotionsRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];

        // Récupérations des attributs get de l'url
        $filtre_name_ecole = $request->query->get('filtre_name_ecole');

        if ($filtre_name_ecole) {
            $filtres['filtre_name_ecole'] = $filtre_name_ecole;
            $conditions[] = 'nom_ecole LIKE ?';
            $parameters[] = '%'.$filtre_name_ecole."%";
        }

        // Filtrage
        $ecoles = $ecolesRepository->filter($conditions, $parameters);
        echo $this->twig->render('admin/ecoles/admin_ecoles.html.twig', [
            'ecoles' => $ecoles,
            'promotions' => $promotions,
            'filtres' => $filtres
        ]);
    }

    /**
     * Route d'affichage pour la création du formulaire de l'école
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/ecoles", name: "admin_create_ecole",)]
    public function createEcoles(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');

        // URL de redirection en fonction de là où on a choisi de créer l'école. Redirection vers cette adresse
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $urlRedirection = $_SERVER['HTTP_REFERER'];
        } else {
            $urlRedirection = '/admin/ecoles';
        }

        echo $this->twig->render('admin/ecoles/admin_form_create_ecole.html.twig', [
            'urlRedirection' => $urlRedirection
        ]);
    }

    /**
     * Route pour enregistrer l'école dans la BDD
     * @throws Exception
     */
    #[Route(path: "/admin/add/ecoles", httpMethod: 'POST', name: "admin_add_ecoles",)]
    public function addEcoles(EcolesRepository $ecolesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Creation de l'adresse
        $ecoles = new Ecoles();
        $ecoles->setNomEcole($_POST['nomEcole']);

        // Sauvegarde en BDD
        $ecolesRepository->save($ecoles);
        header('Location:' . $_POST['redirect_create_adresse_url']);
    }

    /**
     * Route d'affichage du formulaire de modification de l'école
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/ecoles", httpMethod: 'GET', name: "admin_edit_ecoles",)]
    public function editEcoles(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = $request->query->get('id');

        $ecole = $ecolesRepository->selectOneById($id);
        $promotions = $promotionsRepository->selectAll();

        echo $this->twig->render('admin/ecoles/admin_form_edit_ecole.html.twig', [
            'ecole' => $ecole,
            'promotions' => $promotions,
        ]);
    }


    /**
     * Route pour éditer l'école dans la BDD
     * @throws \Exception
     */
    #[Route(path: "/admin/update/ecoles", httpMethod: 'POST', name: "admin_update_ecoles",)]
    public function updateEcoles(EcolesRepository $ecolesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $ecole = $ecolesRepository->selectOneById(intval($_POST['id']));

        $ecole->setNomEcole($_POST['nomEcole']);
        $ecolesRepository->update($ecole);

        header('Location: /admin/ecoles');
    }

    /**
     * Route pour la suppresion d'une école
     * @param EcolesRepository $ecolesRepository
     * @param Session $session
     * @param UtilisateursRepository $utilisateursRepository
     */
    #[Route(path: "/admin/delete/ecoles", httpMethod: 'POST', name: "admin_delete_ecoles")]
    public function deleteEcoles(EcolesRepository $ecolesRepository, Session $session, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['id']);
        // Vérification des contraintes de l'école avant la suppresion
        $promotions = $ecolesRepository->verifContraintsPromotions($id);
        if ($promotions !== null) {
            // Pop-up pour informer de l'impossibilité de suppresion
            $session->set('impossible',"impossible");
        } else {
            // Suppresion
            $ecolesRepository->delete($id);
        }
        header('Location: /admin/ecoles');
    }
}