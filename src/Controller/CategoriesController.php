<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\CategoriesRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CategoriesController extends AbstractController
{
    /**
     * Route admin pour lister toutes les categories
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/categories", name: "admin_categories",)]
    public function categories(CategoriesRepository $categoriesRepository, Session $session, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');

        // Récupération des catégories
        $categories = $categoriesRepository->selectAll();

        echo $this->twig->render('admin/categories/admin_categorie.html.twig', [
            'categories' => $categories,
            'cateforipop' => $session->get('cateforipop'),
        ]);

        // Suppresion des pop-ups
        $session->delete('cateforipop');
    }

    /**
     * Route de filtrage des categories en GET
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/categories/filter", httpMethod: 'GET', name: "admin_categories_filter",)]
    public function categoriesFilter(CategoriesRepository $categoriesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $conditions = [];
        $parameters = [];
        $filtres = [];

        // Récupérations des attributs get de l'url
        $filter_libelle = $request->query->get('filter_libelle');

        if ($filter_libelle) {
            $filtres['filter_libelle'] = $filter_libelle;
            $conditions[] = 'libelle_categorie LIKE ?';
            $parameters[] = '%'.$filter_libelle."%";
        }

        // Filtrages
        $categories = $categoriesRepository->filter($conditions, $parameters);
        echo $this->twig->render('admin/categories/admin_categorie.html.twig', [
            'categories' => $categories,
            'filtres' => $filtres
        ]);
    }

    /**
     * Route d'affichage pour la création du formulaire des categories
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/categorie", name: "admin_create_categories",)]
    public function createCategorie(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // URL de redirection en fonction de là où on a choisi de créer la categorie. Redirection vers cette adresse
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $urlRedirection = $_SERVER['HTTP_REFERER'];
        } else {
            $urlRedirection = '/admin/categories';
        }

        echo $this->twig->render('admin/categories/admin_form_create_categorie.html.twig', [
            'urlRedirection' => $urlRedirection
        ]);
    }

    /**
     * @throws Exception
     * Route pour enregistrer la categorie dans la BDD
     */
    #[Route(path: "/admin/add/categories", httpMethod: 'POST', name: "admin_add_categories",)]
    public function addCategorie(CategoriesRepository $categoriesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Creation de la categorie
        $categorie = new Categories();
        $categorie->setLibelleCategorie($_POST['categorie']);

        // Sauvegarde en BDD
        $categoriesRepository->save($categorie);
        header('Location: '. $_POST['redirect_create_categorie_url']);
    }

    /**
     * Route d'affichage du formulaire de modification des categories
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/categories", httpMethod: 'GET', name: "admin_edit_categories")]
    public function editCategorie(CategoriesRepository $categoriesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Sélection de la categorie pour mettres les informations dans le formulaire
        $id = $request->query->get('id');
        $categorie = $categoriesRepository->selectOneById($id);

        echo $this->twig->render('admin/categories/admin_form_edit_categorie.html.twig', [
            'categorie' => $categorie,
        ]);
    }


    /**
     * Route pour éditer la categorie dans la BDD
     * @throws Exception
     */
    #[Route(path: "/admin/update/categories", httpMethod: 'POST', name: "admin_update_categories",)]
    public function updateCategorie(CategoriesRepository $categoriesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Modification de la categorie
        $categorie = $categoriesRepository->selectOneById(intval($_POST['id']));
        $categorie->setLibelleCategorie($_POST['categorie']);

        // Sauvegarde en BDD
        $categoriesRepository->update($categorie);
        header('Location: /admin/categories');
    }

    /**
     * Route pour la suppresion d'une categorie
     * @param CategoriesRepository $categoriesRepository
     * @param UtilisateursRepository $utilisateursRepository
     * @param Session $session
     */
    #[Route(path: "/admin/delete/categories", httpMethod: 'POST', name: "admin_delete_categories")]
    public function deleteCategorie(CategoriesRepository $categoriesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['id']);

        // Vérification des contraintes de la categorie avant la suppresion
        $evenementsWithCategorie = $categoriesRepository->verifContraintsEvenementCategories($id);
        if ($evenementsWithCategorie !== null) {
            // Pop-up pour informer de l'impossibilité de suppresion
            $session->set("cateforipop","cateforipop");
        } else {
            // Suppresion
            $categoriesRepository->delete($id);
        }
        header('Location: /admin/categories');
    }
}