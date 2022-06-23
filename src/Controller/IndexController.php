<?php

namespace App\Controller;

use App\Repository\EvenementsRepository;
use App\Repository\StatutsRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use DateTime;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndexController extends AbstractController
{

  
    /**
     * @throws SyntaxError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/", name: "accueil")]
    public function index(UtilisateursRepository $utilisateursRepository, EvenementsRepository $evenementsRepository, Session $session, StatutsRepository $statusRepository)
    {
       $this->_setPastStatus($evenementsRepository, $statusRepository);
        $whiteNavbar = true;
        $evenementsAVenir = $evenementsRepository->getEvenementAVenir();
        $evenementsProchain = $evenementsRepository->getEvenementProchain();
        //Résultat permettant de récupérer trois évenèments récemment ajouté
        $conditions = [];
        $conditions[] = "s.libelle_statut != ?";
        $parameters = [];
        $parameters[] = 'Passé';
        $evenementsRecentAjoute = $evenementsRepository->filter($conditions, $parameters ,'JOIN statuts as s ON evenements.id_statut = s.id_statut','created_at', 'DESC', 'LIMIT 3');




        if(!empty($_SESSION)){
            $user = $utilisateursRepository->selectOneById($_SESSION["id"]);

            echo $this->twig->render('home/home.html.twig', [
                'sessionSuccess' => $session->get('success'),
                'sessionId' => $session->get('id'),
                'connected' => $session->get('connected'),
                'evenementsAVenir' => $evenementsAVenir,
                'evenementsProchain' => $evenementsProchain,
                'evenementsRecentAjoute' => $evenementsRecentAjoute,
                'whiteNavbar' => $whiteNavbar
            ]);

            $session->delete('success');
            $session->delete('connected');
        } else {
            echo $this->twig->render('home/home.html.twig', [
                'evenementsAVenir' => $evenementsAVenir,
                'evenementsProchain' => $evenementsProchain,
                'evenementsRecentAjoute' => $evenementsRecentAjoute,
                'whiteNavbar' => $whiteNavbar
            ]);
        }


    }


    //   //Fonction permettant de récupérer trois évenèments dont la date est la plus proche dont le nombre de participants est le plus élevé
    //   #[Route(path: "/accueil/recentAjoutee", name: "recentAjoute")]
    // public function EvenementsProchainParticipant(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository)
    // { 
       
    //     $evenements = $evenementsRepository->getEvenementByParticipation();
        
    // }
    #[Route(path: "/evenements/filter", name: "filtre_evenement_accueil", httpMethod:"POST")]
    public function evenementFilter(EvenementsRepository $evenementsRepository,StatutsRepository $statusRepository)
    {
        $this->_setPastStatus($evenementsRepository, $statusRepository);
        $evenementError= "";
        $conditions = [];
        $parameters = [];
        $filtres = [];
        $whiteNavbar = true;

        if (!empty($_POST['filter_titre'])) {
            $filtres['filter_titre'] = $_POST['filter_titre'];
            $conditions[] = 'titre LIKE ?';
            $parameters[] = '%'.$_POST['filter_titre']."%";
        } else {
            header('Location: http://localhost:8000/');
        }
        $evenements = $evenementsRepository->filter($conditions, $parameters);
        $evenementsAVenir = $evenementsRepository->getEvenementAVenir();
    if(count($evenements) == 0){
        $evenementError = "Aucun résultat ne correspond à votre recherche";
    }
        echo $this->twig->render('home/home.html.twig', [
            'evenementsFiltered' => $evenements,
            'filtres' => $filtres,
            "evenementError" => $evenementError,
            'whiteNavbar' => $whiteNavbar,
            "evenementsAVenir" =>$evenementsAVenir
        ]);
    }

    private function _setPastStatus(EvenementsRepository $evenementsRepository, StatutsRepository $statusRepository)
    {
        $statut = $statusRepository->selectOneByLibelle("Passé");
        $date = new DateTime();
        var_dump($date);
        $evenements = $evenementsRepository->selectAll();
        foreach($evenements as $event){
            if($event->getDate() <= $date && $event->getStatuts() != $statut){
                $event->setStatuts($statut[0]);
            }
        }
    }
}

