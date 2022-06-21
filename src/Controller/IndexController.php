<?php

namespace App\Controller;

use App\Repository\EvenementsRepository;
use App\Repository\ParticipeRepository;
use App\Repository\UserRepository;


use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;

use DateTime;

class IndexController extends AbstractController
{
   
    
    public function bilal(UtilisateursRepository $utilisateursRepository, EvenementsRepository $evenementsRepository)
    {
        
        $evenementsAVenir = $evenementsRepository->getEvenementAVenir();
        $evenementsProchain = $evenementsRepository->getEvenementProchain();
        //Résultat permettant de récupérer trois évenèments récemment ajouté
        $evenementsRecentAjoute = $evenementsRepository->filter([], [],'','created_at', 'DESC', 'LIMIT 3');


        
    }

    //   //Fonction permettant de récupérer trois évenèments dont la date est la plus proche dont le nombre de participants est le plus élevé
    //   #[Route(path: "/accueil/recentAjoutee", name: "recentAjoute")]
    // public function EvenementsProchainParticipant(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository)
    // { 
       
    //     $evenements = $evenementsRepository->getEvenementByParticipation();
        
    // }
}