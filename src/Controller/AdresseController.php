<?php

namespace App\Controller;

use App\Entity\Adresses;
use App\Repository\AdressesRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
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
    public function adresses(AdressesRepository $adressesRepository, Session $session)
    {
        $adresses = $adressesRepository->selectAll();
       

        echo $this->twig->render('admin/adresses/admin_adresse.html.twig', [
            'adresses' => $adresses,
            'Adressepop'=>$session->get('Adressepop'),
        ]);

        $session->delete('Adressepop');
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

        $reponse = $this->_getCoordonneeMaps($_POST['adresse'],$_POST['codePostal']);
        $adresse->setCoordonneLatitude($reponse[1]);
        $adresse->setCoordonneeLongitude($reponse[0]);

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

        $reponse = $this->_getCoordonneeMaps($_POST['adresse'],$_POST['codePostal']);
        $adresse->setCoordonneLatitude($reponse[1]);
        $adresse->setCoordonneeLongitude($reponse[0]);

        $adressesRepository->update($adresse);

        header('Location: /admin/adresses');
    }


    #[Route(path: "/admin/delete/adresses", httpMethod: 'POST', name: "admin_delete_adresses")]
    public function deleteAdresses(AdressesRepository $adressesRepository, Session $session)
    {
        $id = intval($_POST['idAdresse']);
        $evenementsCreatedByUser = $adressesRepository->verifContraintsAdresse($id);
        if ($evenementsCreatedByUser !== null) {
            // TODO POP UP
            // Message pop-up Impossible de supprimer l'adresse car elle est utiliser dans un evenement
            $session->set('Adressepop',"Adressepop");
            header('Location: /admin/adresses'); 
        } else {
            $adressesRepository->delete($id);
            header('Location: /admin/adresses'); 
        } 
    }

    
    private function _getCoordonneeMaps($adresse,$code){
        $url =  "https://api-adresse.data.gouv.fr/search/?q=".urlencode($adresse).'&postcode='.$code;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $resp= json_decode($resp, true);
        return $resp['features'][0]['geometry']['coordinates'];
    }   
}