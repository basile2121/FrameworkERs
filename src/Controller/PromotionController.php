<?php

namespace App\Controller;

use App\Repository\EcolesRepository;
use App\Repository\PromotionsRepository;
use App\Entity\Promotions;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PromotionController extends AbstractController
{
    /**
     * Route admin pour lister toutes les promotions
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/promotions", name: "admin_promotions",)]
    public function promotions(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository , UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Récupération des promotions
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();

        echo $this->twig->render('admin/promotions/admin_promotions.html.twig', [
            'ecoles' => $ecoles,
            'promotions' => $promotions,
            'promotionpop'=>$session->get("promotionpop"),
        ]);

        // Suppresion des pop-ups
        $session->delete("promotionpop");
    }

    /**
     * Route de filtrage des adresses en GET
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/promotions/filter", httpMethod: 'GET', name: "admin_promotions_filter",)]
    public function promotionsFilter(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Select filtre
        $ecoles = $ecolesRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];

        // Récupérations des attributs get de l'url
        $filtre_name_promotion = $request->query->get('filtre_name_promotion');
        $filtre_ecole = $request->query->get('filtre_ecole');

        if ($filtre_name_promotion) {
            $filtres['filtre_name_promotion'] = $filtre_name_promotion;
            $conditions[] = 'libelle_promotion LIKE ?';
            $parameters[] = $filtre_name_promotion;
        }
        if ($filtre_ecole) {
            $filtres['filtre_ecole'] = $filtre_ecole;
            $conditions[] = 'id_ecole = ?';
            $parameters[] = $filtre_ecole;
        }

        // Filtrages
        $promotions = $promotionsRepository->filter($conditions, $parameters);
        echo $this->twig->render('admin/promotions/admin_promotions.html.twig', [
            'ecoles' => $ecoles,
            'promotions' => $promotions,
            'filtres' => $filtres
        ]);
    }

    /**
     * Route d'affichage pour la modification du formulaire des promotions
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/promotions", httpMethod: 'GET', name: "admin_edit_promotions",)]
    public function editPromotions(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Sélection de la promotion pour mettres les informations dans le formulaire
        $id = $request->query->get('id');
        $promotion = $promotionsRepository->selectOneById($id);
        $ecoles = $ecolesRepository->selectAll();

        echo $this->twig->render('admin/promotions/admin_form_edit_promotion.html.twig', [
            'ecoles' => $ecoles,
            'promotion' => $promotion,
        ]);
    }


    /**
     * Route pour éditer la promotion dans la BDD
     * @throws Exception
     */
    #[Route(path: "/admin/update/promotions", httpMethod: 'POST', name: "admin_update_promotions")]
    public function updatePromotions(PromotionsRepository $promotionsRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Selection de la promotion à modifier
        $promotion = $promotionsRepository->selectOneById(intval($_POST['id']));

        $promotion->setLibellePromotion($_POST['libellePromotion']);
        $promotion->setIdEcole(intval($_POST['idEcole']));

        // Sauvegarde en BDD
        $promotionsRepository->update($promotion);

        header('Location: /admin/promotions');
    }

    /**
     * Route d'affichage pour la création du formulaire de la promotion
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/promotions", name: "admin_create_promotions",)]
    public function createPromotions(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $promotions = $promotionsRepository->selectAll();
        $ecoles = $ecolesRepository->selectAll();
        // URL de redirection en fonction de là où on a choisi de créer la promotion. Redirection vers cette adresse
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $urlRedirection = $_SERVER['HTTP_REFERER'];
        } else {
            $urlRedirection = '/admin/promotions';
        }

        echo $this->twig->render('admin/promotions/admin_form_create_promotion.html.twig', [
            'promotions' => $promotions,
            'ecoles' =>$ecoles,
            'urlRedirection' => $urlRedirection
        ]);
    }

    /**
     * Route pour enregistrer la promotion dans la BDD
     * @throws Exception
     */
    #[Route(path: "/admin/add/promotions", httpMethod: 'POST', name: "admin_add_promotions",)]
    public function addEcoles(PromotionsRepository $promotionsRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Creation de la promotion
        $promotions = new Promotions();
        $promotions->setLibellePromotion($_POST['libellePromotion']);
        $promotions->setIdEcole(intval($_POST['idEcole']));
        
        $promotionsRepository->save($promotions);
        header('Location: /admin/promotions');
    }

    /**
     * Route pour la suppresion d'une promotion
     * @param PromotionsRepository $promotionsRepository
     * @param Session $session
     * @param UtilisateursRepository $utilisateursRepository
     */
    #[Route(path: "/admin/delete/promotions", httpMethod: 'POST', name: "admin_delete_promotions")]
    public function deletePromotions(PromotionsRepository $promotionsRepository, Session $session, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['idPromotion']);
        // Vérification des contraintes de la promotion avant la suppresion
        $utilisateursContraintsPromotions = $promotionsRepository->verifContraintsPromotions($id);
        if ($utilisateursContraintsPromotions !== null) {
            // Pop-up pour informer de l'impossibilité de suppresion
            $session->set('promotionpop',"promotionpop");
        } else {
            // Suppresion
            $promotionsRepository->delete($id);
        }
        header('Location: /admin/promotions');
    }
}