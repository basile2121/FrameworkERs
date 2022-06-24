<?php

namespace App\Controller;

use App\Repository\EvenementsRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
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
        $whiteNavbar = true;
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


    /**
     * @throws SyntaxError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/home/evenements/filter", name: "filtre_evenement_accueil", httpMethod:"GET")]
    public function evenementFilter(EvenementsRepository $evenementsRepository, Request $request)
    {
        $evenementError= "";
        $conditions = [];
        $parameters = [];
        $filtres = [];
        $whiteNavbar = true;

        $filtre_titre = $request->query->get('filter_titre');

        if ($filtre_titre) {
            $filtres['filter_titre'] = $filtre_titre;
            $conditions[] = 'titre LIKE ?';
            $parameters[] = '%'.$filtre_titre."%";
        } else {
            header('Location: /');
        }

        $evenements = $evenementsRepository->filter($conditions, $parameters);
        $evenementsAVenir = $evenementsRepository->getEvenementAVenir();

        if(count($evenements) == 0) {
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
}

