<?php

namespace App\Controller;

use App\Repository\EcolesRepository;
use App\Repository\PromotionsRepository;
use App\Entity\Promotions;
use App\Routing\Attribute\Route;
use App\Session\Session;
use Exception;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PromotionController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/promotions", name: "admin_promotions",)]
    public function promotions(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository ,Session $session)
    {
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();

        echo $this->twig->render('admin/promotions/admin_promotions.html.twig', [
            'ecoles' => $ecoles,
            'promotions' => $promotions,
            'promotionpop'=>$session->get("promotionpop"),
        ]);
        $session->delete("promotionpop");
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/promotions/filter", httpMethod: 'POST', name: "admin_promotions_filter",)]
    public function promotionsFilter(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository)
    {
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];

        if (!empty($_POST['filtre_name_promotion'])) {
            $filtres['filtre_name_promotion'] = $_POST['filtre_name_promotion'];
            $conditions[] = 'libelle_promotion LIKE ?';
            $parameters[] = $_POST['filtre_name_promotion'];
        }
        if (!empty($_POST['filtre_ecole'])) {
            $filtres['filtre_ecole'] = intval($_POST['filtre_ecole']);
            $conditions[] = 'id_ecole = ?';
            $parameters[] = intval($_POST['filtre_ecole']);
        }

        $promotions = $promotionsRepository->filter($conditions, $parameters);
        echo $this->twig->render('admin/promotions/admin_promotions.html.twig', [
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
    #[Route(path: "/admin/edit/promotions", httpMethod: 'POST', name: "admin_edit_promotions",)]
    public function editPromotions(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository)
    {
        $id = intval($_POST['id']);
        $promotion = $promotionsRepository->selectOneById($id);
        $ecoles = $ecolesRepository->selectAll();

        echo $this->twig->render('admin/promotions/admin_form_edit_promotion.html.twig', [
            'ecoles' => $ecoles,
            'promotion' => $promotion,
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route(path: "/admin/update/promotions", httpMethod: 'POST', name: "admin_update_promotions")]
    public function updatePromotions(PromotionsRepository $promotionsRepository)
    {
        $promotion = $promotionsRepository->selectOneById(intval($_POST['id']));

        $promotion->setLibellePromotion($_POST['libellePromotion']);
        $promotion->setIdEcole(intval($_POST['idEcole']));
        $promotionsRepository->update($promotion);

        header('Location: /admin/promotions');
    }


    #[Route(path: "/admin/delete/promotions", httpMethod: 'POST', name: "admin_delete_promotions")]
    public function deletePromotions(PromotionsRepository $promotionsRepository, Session $session)
    {
        $id = intval($_POST['idPromotion']);
        $utilisateursContraintsPromotions = $promotionsRepository->verifContraintsPromotions($id);

        if ($utilisateursContraintsPromotions !== null) {
            // TODO POP UP
            // Message pop-up Impossible de supprimer la promotion car des utilisateurs y sont habilite
            $session->set('promotionpop',"promotionpop");
            header('Location: /admin/promotions'); 
        } else {
            $promotionsRepository->delete($id);
            header('Location: /admin/promotions');
        } 
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/promotions", name: "admin_create_promotions",)]
    public function createPromotions(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository)
    {
        $promotions = $promotionsRepository->selectAll();
        $ecoles = $ecolesRepository->selectAll();
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
     * @throws Exception
     */
    #[Route(path: "/admin/add/promotions", httpMethod: 'POST', name: "admin_add_promotions",)]
    public function addEcoles(PromotionsRepository $promotionsRepository)
    {
        $promotions = new Promotions();
        $promotions->setLibellePromotion($_POST['libellePromotion']);
        $promotions->setIdEcole(intval($_POST['idEcole']));
        
        $promotionsRepository->save($promotions);
        header('Location: /admin/promotions');
    }
}