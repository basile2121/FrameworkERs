<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Repository\AdressesRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdresseController extends AbstractController
{
    /**
     * Route admin pour lister toutes les adresses
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/adresses", name: "admin_adresses",)]
    public function adresses(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Récupération des adresses
        $adresses = $adressesRepository->selectAll();

        echo $this->twig->render('admin/adresses/admin_adresse.html.twig', [
            'adresses' => $adresses,
            'Adressepop'=>$session->get('Adressepop'),
        ]);

        // Suppresion des pop-ups
        $session->delete('Adressepop');
    }

    /**
     * Route de filtrage des adresses en GET
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/adresses/filter", httpMethod: 'GET', name: "admin_adresses_filter",)]
    public function adressesFilter(AdressesRepository $adressesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $conditions = [];
        $parameters = [];
        $filtres = [];

        // Récupérations des attributs get de l'url
        $filter_city = $request->query->get('filter_city');
        $filter_cp = $request->query->get('filter_cp');

        if ($filter_city) {
            $filtres['filter_city'] = $filter_city;
            $conditions[] = 'ville_libelle LIKE ?';
            $parameters[] = '%'.$filter_city."%";
        }

        if ($filter_cp) {
            $filtres['filter_cp'] = $filter_cp;
            $conditions[] = 'cp_ville LIKE ?';
            $parameters[] = '%'.$filter_cp."%";
        }

        // Filtrages
        $adresses = $adressesRepository->filter($conditions, $parameters);
        echo $this->twig->render('admin/adresses/admin_adresse.html.twig', [
            'adresses' => $adresses,
            'filtres' => $filtres
        ]);
    }

    /**
     * Route d'affichage pour la création du formulaire des adresses
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/adresse", name: "admin_create_adresses",)]
    public function createAdresse(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        // URL de redirection en fonction de là où on a choisi de créer l'adresse. Redirection vers cette adresse
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $urlRedirection = $_SERVER['HTTP_REFERER'];
        } else {
            $urlRedirection = '/admin/adresses';
        }

        echo $this->twig->render('admin/adresses/admin_form_create_adresse.html.twig', [
            'urlRedirection' => $urlRedirection
        ]);
    }

    /**
     * Route pour enregistrer l'adresse dans la BDD
     * @throws Exception
     */
    #[Route(path: "/admin/add/adresses", httpMethod: 'POST', name: "admin_add_adresses",)]
    public function addAdresses(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');

        // Creation de l'adresse
        $adresse = new Adresses();
        $adresse->setLibelleAdresse($_POST['adresse']);
        $adresse->setCpVille($_POST['codePostal']);
        $adresse->setVilleLibelle($_POST['ville']);

        // Récupération des coordonnées via l'api gouvernemental en fonction de l'adresse et du code postal
        $reponse = $this->_getCoordonneeMaps($_POST['adresse'],$_POST['codePostal']);
        $adresse->setCoordonneLatitude($reponse[1]);
        $adresse->setCoordonneeLongitude($reponse[0]);

        // Sauvegarde en BDD
        $adressesRepository->save($adresse);
        header('Location: '. $_POST['redirect_create_adresse_url']);
    }

    /**
     * Route d'affichage du formulaire de modification des adresses
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/adresses", httpMethod: 'GET', name: "admin_edit_adresses",)]
    public function editAdresse(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session, Request $request)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');

        // Sélection de l'adresse pour mettres les informations dans le formulaire
        $id = $request->query->get('id');
        $adresse = $adressesRepository->selectOneById($id);

        echo $this->twig->render('admin/adresses/admin_form_edit_adresse.html.twig', [
            'adresse' => $adresse,
        ]);
    }


    /**
     * Route pour éditer l'adresse dans la BDD
     * @throws Exception
     * @param AdressesRepository $adressesRepository
     * @param UtilisateursRepository $utilisateursRepository
     * @param Session $session
     */
    #[Route(path: "/admin/update/adresses", httpMethod: 'POST', name: "admin_update_adresses",)]
    public function updateAdresse(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');

        // Selection de l'adresse à modifier
        $adresse = $adressesRepository->selectOneById(intval($_POST['id']));
        $adresse->setLibelleAdresse($_POST['adresse']);
        $adresse->setCpVille($_POST['codePostal']);
        $adresse->setVilleLibelle($_POST['ville']);

        // Récupération des coordonnées via l'api gouvernemental en fonction de l'adresse et du code postal
        $reponse = $this->_getCoordonneeMaps($_POST['adresse'],$_POST['codePostal']);
        $adresse->setCoordonneLatitude($reponse[1]);
        $adresse->setCoordonneeLongitude($reponse[0]);

        // Sauvegarde en BDD
        $adressesRepository->update($adresse);

        header('Location: /admin/adresses');
    }


    /**
     * Route pour la suppresion d'une adresse
     * @param AdressesRepository $adressesRepository
     * @param UtilisateursRepository $utilisateursRepository
     * @param Session $session
     */
    #[Route(path: "/admin/delete/adresses", httpMethod: 'POST', name: "admin_delete_adresses")]
    public function deleteAdresse(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['idAdresse']);
        // Vérification des contraintes de l'adresse avant la suppresion
        $evenementsCreatedByUser = $adressesRepository->verifContraintsAdresse($id);
        if ($evenementsCreatedByUser !== null) {
            // Pop-up pour informer de l'impossibilité de suppresion
            $session->set('Adressepop',"Adressepop");
        } else {
            // Suppresion
            $adressesRepository->delete($id);
        }
        header('Location: /admin/adresses');
    }

    /**
     * Récupération des coordonnées via une adresse grâce à l'api gouvernementale
     * @param $adresse
     * @param $code
     * @return mixed
     */
    private function _getCoordonneeMaps($adresse,$code): mixed
    {
        $url =  "https://api-adresse.data.gouv.fr/search/?q=".urlencode($adresse).'&postcode='.$code;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $resp= json_decode($resp, true);

        return $resp['features'][0]['geometry']['coordinates'];
    }   
}