<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Repository\AdressesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\EcolesRepository;
use App\Repository\EvenementsRepository;
use App\Repository\ParticipeRepository;
use App\Repository\RolesRepository;
use App\Repository\StatutsRepository;
use App\Repository\UserRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use DateTime;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EvenementsController extends AbstractController
{

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/evenement", httpMethod: 'POST', name: "evenement",)]
    public function evenements(EvenementsRepository $evenementsRepository,CategoriesRepository $categoriesRepository, StatutsRepository $statutsRepository, AdressesRepository $adressesRepository, ParticipeRepository $participeRepository)
    {
        $id = intval($_POST['id']);
        $evenement = $evenementsRepository->selectOneById($id);

        echo $this->twig->render('evenements/evenement.html.twig', [
            'evenement' => $evenement,
        ]);
    }



    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/evenements", name: "admin_evenements",)]
    public function adminEvenements(EvenementsRepository $evenementsRepository,
                                    CategoriesRepository $categoriesRepository,
                                    StatutsRepository $statutsRepository,
                                    AdressesRepository $adressesRepository,
                                    ParticipeRepository $participeRepository
    )
    {
        $evenements = $evenementsRepository->selectAll('date', 'DESC');
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();
        $participes = $participeRepository->selectAll();


        echo $this->twig->render('admin/evenements/admin_evenements.html.twig', [
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
    #[Route(path: "/admin/evenements/filter", httpMethod: 'GET', name: "admin_evenements_filter",)]
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

        if (!empty($_GET['filter_titre'])) {
            $filtres['filter_titre'] = $_GET['filter_titre'];
            $conditions[] = 'titre LIKE ?';
            $parameters[] = '%'.$_GET['filter_titre']."%";
        }
        if (!empty($_GET['filtre_categorie'])) {
            $filtres['filtre_categorie'] = intval($_GET['filtre_categorie']);
            $conditions[] = 'id_categorie = ?';
            $parameters[] = intval($_GET['filtre_categorie']);
        }

        if (!empty($_GET['filtre_statut'])) {
            $filtres['filtre_statut'] = intval($_GET['filtre_statut']);
            $conditions[] = 'id_statut = ?';
            $parameters[] = intval($_GET['filtre_statut']);
        }

        if (!empty($_GET['filtre_city']) || !empty($_GET['filtre_cp'])) {
            $query = 'JOIN adresses ON adresses.id_adresse = evenements.id_adresse';
        }

        if (!empty($_GET['filtre_city'])) {
            $filtres['filtre_city'] = $_GET['filtre_city'];
            $conditions[] = 'adresses.ville_libelle LIKE ?';
            $parameters[] = '%'.$_GET['filtre_city']."%";
        }

        if (!empty($_GET['filtre_cp'])) {
            $filtres['filtre_cp'] = $_GET['filtre_cp'];
            $conditions[] = 'adresses.cp_ville LIKE ?';
            $parameters[] = '%'.$_GET['filtre_cp']."%";
        }

        if (!empty($_GET['order_date'])) {
            $filtres['order_date'] = $_GET['order_date'];
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query,'date' , $_GET['order_date']);
        } else {
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query);
        }

        echo $this->twig->render('admin/evenements/admin_evenements.html.twig', [
            'evenements' => $evenements,
            'categories' => $categories,
            'statuts' => $statuts,
            'adresses' => $adresses,
            'filtres' => $filtres,
            'arrayParticipeUtilisateurs' => $this->_getNbParticipants($evenements, $participes)
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/evenements", name: "admin_create_evenements",)]
    public function createEvenements(CategoriesRepository $categoriesRepository,
                                   StatutsRepository $statutsRepository,
                                   AdressesRepository $adressesRepository,
                                   ParticipeRepository $participeRepository
    )
    {
        $adresses = $adressesRepository->selectAll();
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();

        echo $this->twig->render('admin/evenements/admin_form_create_evenement.html.twig', [
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
    #[Route(path: "/admin/add/evenements", httpMethod: 'POST', name: "admin_add_evenements",)]
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
        // TODO Fabien SESSION
        $evenement->setIdUtilisateur(293);
        // TODO WALID FILE UPLOAD
        $evenement->setIdMedia(1);

        $evenementsRepository->save($evenement);

        header('Location: /admin/evenements');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/evenements", httpMethod: 'POST', name: "admin_edit_evenements",)]
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

        echo $this->twig->render('admin/evenements/admin_form_edit_evenement.html.twig', [
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
    #[Route(path: "/admin/update/evenements", httpMethod: 'POST', name: "admin_update_evenements",)]
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

        header('Location: /admin/evenements');
    }


    #[Route(path: "/admin/delete/evenements", httpMethod: 'POST', name: "admin_delete_evenements")]
    public function deleteEvenements(EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository)
    {
        $id = intval($_POST['id']);
        $utilisateursRepository->delete($id);
        header('Location: /admin/evenements');
    }



    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/evenements/list/utilisateurs", httpMethod: 'POST', name: "admin_evenements_list_utilisateurs",)]
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

        echo $this->twig->render('admin/evenements/admin_list_utilisateurs.html.twig', [
            'evenement' => $evenement,
            'utilisateurs' => $utilisateurs,
            'arrayParticipeUtilisateurs' => $arrayParticipeUtilisateurs
        ]);
    }

    #[Route(path: "/admin/delete/evenement/utilisateur", httpMethod: 'POST', name: "admin_delete_evenement_utilisateur")]
    public function deleteUtilisateur(ParticipeRepository $participeRepository)
    {
        $idUtilisateur = intval($_POST['idUtilisateur']);
        $idEvenement = intval($_POST['idEvenement']);

        $participeRepository->deleteUtilisateur($idUtilisateur, $idEvenement);

        header('Location: /admin/evenements');
    }


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