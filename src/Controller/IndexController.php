<?php

namespace App\Controller;

use App\Repository\EvenementsRepository;
use App\Repository\ParticipeRepository;
use App\Repository\UserRepository;


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
    public function index(UtilisateursRepository $utilisateursRepository, EvenementsRepository $evenementsRepository, Session $session)
    {
        $evenementsAVenir = $evenementsRepository->getEvenementAVenir();
        $evenementsProchain = $evenementsRepository->getEvenementProchain();
        //Résultat permettant de récupérer trois évenèments récemment ajouté
        $evenementsRecentAjoute = $evenementsRepository->filter([], [],'','created_at', 'DESC', 'LIMIT 3');

        if(!empty($_SESSION)){
            $user = $utilisateursRepository->selectOneById($_SESSION["id"]);

            echo $this->twig->render('home/home.html.twig', [
                'sessionSuccess' => $session->get('success'),
                'sessionId' => $session->get('id'),
                'connected' => $session->get('connected'),
                'evenementsAVenir' => $evenementsAVenir,
                'evenementsProchain' => $evenementsProchain,
                'evenementsRecentAjoute' => $evenementsRecentAjoute,
            ]);

            $session->delete('success');
            $session->delete('connected');
        } else {
            echo $this->twig->render('home/home.html.twig', [
                'evenementsAVenir' => $evenementsAVenir,
                'evenementsProchain' => $evenementsProchain,
                'evenementsRecentAjoute' => $evenementsRecentAjoute,
            ]);
        }
    }


    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route(path: "/base", name: "base", httpMethod: "GET")]
    public function base()
    {
        echo $this->twig->render('base.html.twig');
    }

    //   //Fonction permettant de récupérer trois évenèments dont la date est la plus proche dont le nombre de participants est le plus élevé
    //   #[Route(path: "/accueil/recentAjoutee", name: "recentAjoute")]
    // public function EvenementsProchainParticipant(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository)
    // { 
       
    //     $evenements = $evenementsRepository->getEvenementByParticipation();
        
    // }
}

