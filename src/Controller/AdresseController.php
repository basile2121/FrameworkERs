<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Repository\AdressesRepository;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use DateTime;
use Exception;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdresseController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/adresses", name: "admin_adresses",)]
    public function adresses(AdressesRepository $adressesRepository)
    {
        $adresses = $adressesRepository->selectAll();

        echo $this->twig->render('admin/adresses/admin_adresse.html.twig', [
            'adresses' => $adresses,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/adresses/filter", httpMethod: 'POST', name: "admin_adresses_filter",)]
    public function adressesFilter(AdressesRepository $adressesRepository)
    {
        $conditions = [];
        $parameters = [];
        $filtres = [];

        if (!empty($_POST['filter_city'])) {
            $filtres['filter_city'] = $_POST['filter_city'];
            $conditions[] = 'ville_libelle LIKE ?';
            $parameters[] = '%'.$_POST['filter_city']."%";
        }

        if (!empty($_POST['filter_cp'])) {
            $filtres['filter_cp'] = $_POST['filter_cp'];
            $conditions[] = 'cp_ville LIKE ?';
            $parameters[] = '%'.$_POST['filter_cp']."%";
        }

        $adresses = $adressesRepository->filter($conditions, $parameters);
        echo $this->twig->render('admin/adresses/admin_adresse.html.twig', [
            'adresses' => $adresses,
            'filtres' => $filtres
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/adresse", name: "admin_create_adresses",)]
    public function createAdresses(AdressesRepository $adressesRepository)
    {
        $adresses = $adressesRepository->selectAll();
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $urlRedirection = $_SERVER['HTTP_REFERER'];
        } else {
            $urlRedirection = '/admin/adresses';
        }

        echo $this->twig->render('admin/adresses/admin_form_create_adresse.html.twig', [
            'adresses' => $adresses,
            'urlRedirection' => $urlRedirection
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError|Exception
     */
    #[Route(path: "/admin/add/adresses", httpMethod: 'POST', name: "admin_add_adresses",)]
    public function addAdresses(AdressesRepository $adressesRepository)
    {
        $adresse = new Adresses();
        $adresse->setLibelleAdresse($_POST['adresse']);
        $adresse->setCpVille($_POST['codePostal']);
        $adresse->setVilleLibelle($_POST['ville']);
        $adresse->setCoordonneLatitude($_POST['coordonneeLatitude']);
        $adresse->setCoordonneeLongitude($_POST['coordonneeLongitude']);


        $adressesRepository->save($adresse);
        header('Location: '. $_POST['redirect_create_adresse_url']);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/adresses", httpMethod: 'POST', name: "admin_edit_adresses",)]
    public function editAdresses(AdressesRepository $adressesRepository)
    {
        $id = intval($_POST['id']);
        $adresse = $adressesRepository->selectOneById($id);

        echo $this->twig->render('admin/adresses/admin_form_edit_adresse.html.twig', [
            'adresse' => $adresse,
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route(path: "/admin/update/adresses", httpMethod: 'POST', name: "admin_update_adresses",)]
    public function updateAdresses(AdressesRepository $adressesRepository)
    {
        $adresse = $adressesRepository->selectOneById(intval($_POST['id']));

        $adresse->setLibelleAdresse($_POST['adresse']);
        $adresse->setCpVille($_POST['codePostal']);
        $adresse->setVilleLibelle($_POST['ville']);
        $adresse->setCoordonneLatitude($_POST['coordonneeLatitude']);
        $adresse->setCoordonneeLongitude($_POST['coordonneeLongitude']);


        $adressesRepository->update($adresse);

        header('Location: /admin/adresses');
    }


    #[Route(path: "/admin/delete/adresses", httpMethod: 'POST', name: "admin_delete_adresses")]
    public function deleteAdresses(AdressesRepository $adressesRepository)
    {
        $id = intval($_POST['id']);
        $adressesRepository->delete($id);
        header('Location: /admin/adresses');
    }
}