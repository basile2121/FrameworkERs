<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Entity\Categories;
use App\Repository\AdressesRepository;
use App\Repository\CategoriesRepository;
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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/categories", name: "admin_categories",)]
    public function categories(CategoriesRepository $categoriesRepository, Session $session)
    {
        $categories = $categoriesRepository->selectAll();

        echo $this->twig->render('admin/categories/admin_categorie.html.twig', [
            'categories' => $categories,
            'cateforipop' => $session->get('cateforipop'),
        ]);
        $session->delete('cateforipop');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/categories/filter", httpMethod: 'GET', name: "admin_categories_filter",)]
    public function categoriesFilter(CategoriesRepository $categoriesRepository, Request $request)
    {
        $conditions = [];
        $parameters = [];
        $filtres = [];

        $filter_libelle = $request->query->get('filter_libelle');

        if ($filter_libelle) {
            $filtres['filter_libelle'] = $filter_libelle;
            $conditions[] = 'libelle_categorie LIKE ?';
            $parameters[] = '%'.$filter_libelle."%";
        }

        $categories = $categoriesRepository->filter($conditions, $parameters);
        echo $this->twig->render('admin/categories/admin_categorie.html.twig', [
            'categories' => $categories,
            'filtres' => $filtres
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/categorie", name: "admin_create_categories",)]
    public function createCategorie()
    {
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
     */
    #[Route(path: "/admin/add/categories", httpMethod: 'POST', name: "admin_add_categories",)]
    public function addCategorie(CategoriesRepository $categoriesRepository)
    {
        $categorie = new Categories();
        $categorie->setLibelleCategorie($_POST['categorie']);

        $categoriesRepository->save($categorie);
        header('Location: '. $_POST['redirect_create_categorie_url']);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/categories", httpMethod: 'GET', name: "admin_edit_categories",)]
    public function editCategorie(CategoriesRepository $categoriesRepository, Request $request)
    {
        $id = $request->query->get('id');
        $categorie = $categoriesRepository->selectOneById($id);

        echo $this->twig->render('admin/categories/admin_form_edit_categorie.html.twig', [
            'categorie' => $categorie,
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route(path: "/admin/update/categories", httpMethod: 'POST', name: "admin_update_categories",)]
    public function updateCategorie(CategoriesRepository $categoriesRepository)
    {
        $categorie = $categoriesRepository->selectOneById(intval($_POST['id']));
        $categorie->setLibelleCategorie($_POST['categorie']);

        $categoriesRepository->update($categorie);

        header('Location: /admin/categories');
    }


    #[Route(path: "/admin/delete/categories", httpMethod: 'POST', name: "admin_delete_categories")]
    public function deleteCategorie(CategoriesRepository $categoriesRepository ,Session $session)
    {
        $id = intval($_POST['id']);

        $evenementsWithCategorie = $categoriesRepository->verifContraintsEvenementCategories($id);
       
        if ($evenementsWithCategorie !== null) {
            // TODO POP UP
            // Message pop-up Impossible de supprimer la caregorie appartient 
            
            $session->set("cateforipop","cateforipop");
            header('Location: /admin/categories');
            
        } else {   
            $categoriesRepository->delete($id);
            header('Location: /admin/categories');
        }
    }
}