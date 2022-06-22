<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Entity\Participe;
use App\Repository\AdressesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\EcolesRepository;
use App\Repository\EvenementsRepository;
use App\Repository\ParticipeRepository;
use App\Repository\RolesRepository;
use App\Repository\StatutsRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use DateTime;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EvenementsBdeController extends AbstractController
{

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/bde/evenements", name: "bde_evenements",)]
    public function adminEvenements(EvenementsRepository $evenementsRepository,
                                    CategoriesRepository $categoriesRepository,
                                    StatutsRepository $statutsRepository,
                                    AdressesRepository $adressesRepository,
                                    ParticipeRepository $participeRepository
    )
    {
        $id = $_SESSION["id"];
        $evenements = $evenementsRepository->selectEvenementByUser($id);
        // $evenements = $evenementsRepository->selectAll('date', 'DESC');
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();
        $participes = $participeRepository->selectAll();


        echo $this->twig->render('bde/evenements/bde_evenements.html.twig', [
            'evenements' => $evenements,
            'categories' => $categories,
            'statuts' => $statuts,
            'adresses' => $adresses,
            'arrayParticipeUtilisateurs' => $this->_getNbParticipants($evenements, $participes)
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/bde/evenements/filter", httpMethod: 'POST', name: "bde_evenements_filter",)]
    public function evenementsFilter(EvenementsRepository $evenementsRepository,
                                     CategoriesRepository $categoriesRepository,
                                     StatutsRepository $statutsRepository,
                                     AdressesRepository $adressesRepository,
                                     ParticipeRepository $participeRepository
    )
    {
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();
        $participes = $participeRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];
        $query = '';

        //Utilisateur BDE 
        $id = $_SESSION["id"];
        $conditions[] = 'evenements.id_utilisateur = ?';
        $parameters[] = $_SESSION["id"];

        if (!empty($_POST['filter_titre'])) {
            $filtres['filter_titre'] = $_POST['filter_titre'];
            $conditions[] = 'titre LIKE ?';
            $parameters[] = '%'.$_POST['filter_titre']."%";
        }
        if (!empty($_POST['filtre_categorie'])) {
            $filtres['filtre_categorie'] = intval($_POST['filtre_categorie']);
            $conditions[] = 'id_categorie = ?';
            $parameters[] = intval($_POST['filtre_categorie']);
        }

        if (!empty($_POST['filtre_statut'])) {
            $filtres['filtre_statut'] = intval($_POST['filtre_statut']);
            $conditions[] = 'id_statut = ?';
            $parameters[] = intval($_POST['filtre_statut']);
        }

        if (!empty($_POST['filtre_city']) || !empty($_POST['filtre_cp'])) {
            $query = 'JOIN adresses ON adresses.id_adresse = evenements.id_adresse';
        }

        if (!empty($_POST['filtre_city'])) {
            $filtres['filtre_city'] = $_POST['filtre_city'];
            $conditions[] = 'adresses.ville_libelle LIKE ?';
            $parameters[] = '%'.$_POST['filtre_city']."%";
        }

        if (!empty($_POST['filtre_cp'])) {
            $filtres['filtre_cp'] = $_POST['filtre_cp'];
            $conditions[] = 'adresses.cp_ville LIKE ?';
            $parameters[] = '%'.$_POST['filtre_cp']."%";
        }
        if (!empty($_POST['order_date'])) {
            $filtres['order_date'] = $_POST['order_date'];
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query,'date' , $_POST['order_date']);
        } 
         else {
            
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query);
        }
        
       

        echo $this->twig->render('bde/evenements/bde_evenements.html.twig', [
            'evenements' => $evenements,
            'categories' => $categories,
            'statuts' => $statuts,
            'adresses' => $adresses,
            'filtres' => $filtres,
            'user/bde' => $id,
            'arrayParticipeUtilisateurs' => $this->_getNbParticipants($evenements, $participes)
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/bde/create/evenements", name: "bde_create_evenements",)]
    public function createEvenements(CategoriesRepository $categoriesRepository,
                                   StatutsRepository $statutsRepository,
                                   AdressesRepository $adressesRepository,
                                   ParticipeRepository $participeRepository
    )
    {
        $adresses = $adressesRepository->selectAll();
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();

        echo $this->twig->render('bde/evenements/bde_form_create_evenement.html.twig', [
            'adresses' => $adresses,
            'categories' => $categories,
            'statuts' => $statuts,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/bde/add/evenements/", httpMethod: 'POST', name: "bde_add_evenements",)]
    public function addEvenements(EvenementsRepository $evenementsRepository)
    {
        $evenement = new Evenements();
        $evenement->setTitre($_POST['evenementTitle']);
        $evenement->setSousTitre($_POST['evenementSubTitle']);
        $evenement->setDate(new DateTime($_POST['evenementDate']));
        $evenement->setNbParticipantsMax($_POST['nbParticipantMax']);
        $evenement->setPrix($_POST['prix']);
        $evenement->setDescription($_POST['description']);
        $evenement->setIdCategorie(($_POST['categorieSelect']));
        $evenement->setIdStatut(intval($_POST['statutSelect']));
        $evenement->setIdAdresse(intval($_POST['adresseSelect']));
        $evenement->setCreatedAt(new DateTime());
        $evenement->setUpdatedAt(new DateTime());
        $evenement->setIdUtilisateur(intval($_SESSION['id']));
        // TODO WALID FILE UPLOAD
        $evenement->setIdMedia(1);

        $evenementsRepository->save($evenement);

        header('Location: /bde/evenements');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/bde/edit/evenements", httpMethod: 'POST', name: "bde_edit_evenements",)]
    public function editEvenements(EvenementsRepository $evenementsRepository,
                                   CategoriesRepository $categoriesRepository,
                                   StatutsRepository $statutsRepository,
                                   AdressesRepository $adressesRepository,
                                   ParticipeRepository $participeRepository
    )
    {
        $id = intval($_POST['id']);
        $evenement = $evenementsRepository->selectOneById($id);
        $adresses = $adressesRepository->selectAll();
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $participes = $participeRepository->selectAll();

        echo $this->twig->render('bde/evenements/bde_form_edit_evenement.html.twig', [
            'evenement' => $evenement,
            'adresses' => $adresses,
            'categories' => $categories,
            'statuts' => $statuts,
            'arrayParticipeUtilisateurs' => $this->_getNbParticipants([], $participes, $id)
        ]);
    }


    /**
     * @throws \Exception
     */
    #[Route(path: "/bde/update/evenements", httpMethod: 'POST', name: "bde_update_evenements",)]
    public function updateEvenements(EvenementsRepository $evenementsRepository)
    {
        $evenement = $evenementsRepository->selectOneById(intval($_POST['id']));

        $evenement->setTitre($_POST['evenementTitle']);
        $evenement->setSousTitre($_POST['evenementSubTitle']);
        $evenement->setDate(new DateTime($_POST['evenementDate']));
        $evenement->setNbParticipantsMax($_POST['nbParticipantMax']);
        $evenement->setPrix($_POST['prix']);
        $evenement->setDescription($_POST['description']);
        $evenement->setIdCategorie(($_POST['categorieSelect']));
        $evenement->setIdStatut(intval($_POST['statutSelect']));
        $evenement->setIdAdresse(intval($_POST['adresseSelect']));
        $evenement->setUpdatedAt(new DateTime());

        $evenementsRepository->update($evenement);

        header('Location: /bde/evenements');
    }


    #[Route(path: "/bde/delete/evenements", httpMethod: 'POST', name: "bde_delete_evenements")]
    public function deleteEvenements( EvenementsRepository $evenementsRepository)
    {
        $id = intval($_POST['id']);
        $evenementsRepository->delete($id);
        header('Location: /bde/evenements');
    }



    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/bde/evenements/list/utilisateurs", httpMethod: 'POST', name: "bde_evenements_list_utilisateurs",)]
    public function adminEvenementsListUtilisateurs(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository, UtilisateursRepository $utilisateursRepository)
    {
        $id = intval($_POST['id']);
        $evenement = $evenementsRepository->selectOneById($id);
        $participes = $participeRepository->selectAll();

        $arrayParticipeUtilisateurs = $this->_getNbParticipants([], $participes, $id);
        $utilisateurs = [];
        foreach ($arrayParticipeUtilisateurs[$id] as $utilisateurId) {
            array_push($utilisateurs, $utilisateursRepository->selectOneById($utilisateurId));
        }

        echo $this->twig->render('bde/evenements/bde_list_utilisateurs.html.twig', [
            'evenement' => $evenement,
            'utilisateurs' => $utilisateurs,
            'arrayParticipeUtilisateurs' => $arrayParticipeUtilisateurs
        ]);
    }

    #[Route(path: "bde/delete/evenement/utilisateur", httpMethod: 'POST', name: "bde_delete_evenement_utilisateur")]
    public function deleteUtilisateur(ParticipeRepository $participeRepository)
    {
        $idUtilisateur = intval($_POST['idUtilisateur']);
        $idEvenement = intval($_POST['idEvenement']);

        $participeRepository->deleteUtilisateur($idUtilisateur, $idEvenement);

        header('Location: /bde/evenements');
    }

    //  /**
    //  * Permet d'afficher les evenements que l'utilisateur connecté à créer 
    //  * S'il a le rôle : /bde 
    //  *
    //  * @param EvenementsRepository $evenementsRepository
    //  * @param CategoriesRepository $categoriesRepository
    //  * @param StatutsRepository $statutsRepository
    //  * @param AdressesRepository $adressesRepository
    //  * @param ParticipeRepository $participeRepository
    //  * @return void
    //  */
    // #[Route(path: "/admin/evenements/utilisateurBde", httpMethod: 'GET', name: "admin_evenement_utilisateur_bde")]
    // public function gestionEvenementByCreateur(EvenementsRepository $evenementsRepository,
    // CategoriesRepository $categoriesRepository,
    // StatutsRepository $statutsRepository,
    // AdressesRepository $adressesRepository,
    // ParticipeRepository $participeRepository)
    // {
    //     $id = $_SESSION["id"];
    //     $evenements = $evenementsRepository->selectEvenementByUser($id);
    //     $categories = $categoriesRepository->selectAll();
    //     $statuts = $statutsRepository->selectAll();
    //     $adresses = $adressesRepository->selectAll();
    //     $participes = $participeRepository->selectAll();


    //     echo $this->twig->render('admin/evenements/admin_evenements.html.twig', [
    //         'evenements' => $evenements,
    //         'categories' => $categories,
    //         'statuts' => $statuts,
    //         'user/bde' => $id,
    //         'adresses' => $adresses,
    //         'arrayParticipeUtilisateurs' => $this->_getNbParticipants($evenements, $participes)
    //     ]);
    // }


    private function _getNbParticipants(array $evenements, array $participes, int $evenementId = null): array {
        $arrayParticipeUtilisateurs = [];
        if ($evenementId === null) {
            foreach ($evenements as $evenement) {
                $arrayParticipeUtilisateurs[$evenement->getIdEvenement()] = [];
            }
            foreach ($participes as $participe) {
                if (isset($arrayParticipeUtilisateurs[$participe->getEvenements()->getIdEvenement()])) {
                    array_push($arrayParticipeUtilisateurs[$participe->getEvenements()->getIdEvenement()], $participe->getUtilisateurs()->getIdUtilisateur());
                }
            }
        } else {
            $arrayParticipeUtilisateurs[$evenementId] = [];
            foreach ($participes as $participe) {
                if ($participe->getEvenements()->getIdEvenement() === $evenementId) {
                    array_push($arrayParticipeUtilisateurs[$evenementId], $participe->getUtilisateurs()->getIdUtilisateur());
                }
            }
        }

        return $arrayParticipeUtilisateurs;
    }
   
}