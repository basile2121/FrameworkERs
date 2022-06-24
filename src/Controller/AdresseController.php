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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/adresses", name: "admin_adresses",)]
    public function adresses(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
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
    #[Route(path: "/admin/adresses/filter", httpMethod: 'GET', name: "admin_adresses_filter",)]
    public function adressesFilter(AdressesRepository $adressesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $conditions = [];
        $parameters = [];
        $filtres = [];

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
    public function createAdresses(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
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
    public function addAdresses(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
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
    #[Route(path: "/admin/edit/adresses", httpMethod: 'GET', name: "admin_edit_adresses",)]
    public function editAdresses(AdressesRepository $adressesRepository, Request $request, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = $request->query->get('id');
        $adresse = $adressesRepository->selectOneById($id);

        echo $this->twig->render('admin/adresses/admin_form_edit_adresse.html.twig', [
            'adresse' => $adresse,
        ]);
    }


    /**
     * @throws Exception
     */
    #[Route(path: "/admin/update/adresses", httpMethod: 'POST', name: "admin_update_adresses",)]
    public function updateAdresses(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
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
    public function deleteAdresses(AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['idAdresse']);
        $evenementsCreatedByUser = $adressesRepository->verifContraintsAdresse($id);
        if ($evenementsCreatedByUser !== null) {
            $session->set('Adressepop',"Adressepop");
        } else {
            $adressesRepository->delete($id);
        }
        header('Location: /admin/adresses');
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