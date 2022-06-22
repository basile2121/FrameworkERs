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
    #[Route(path: "/evenement/participe", httpMethod: 'POST', name: "evenement_participe",)]
    public function participeEvenement(EvenementsRepository $evenementsRepository,StatutsRepository $statutsRepository, ParticipeRepository $participeRepository)
    {
        $id = intval($_POST['id']);
        $evenement = $evenementsRepository->selectOneById($id);
        $participes = $participeRepository->selectAll();


        $nbParticipantMax = $evenement->getNbParticipantsMax();
        $nbParticipants = $this->_getNbParticipants([], $participes, $id);
        $nbParticipant = count($nbParticipants[$id]);
        if ($nbParticipant < $nbParticipantMax) {
            // Ajout de la participation
            $participe = new Participe();
            $participe->setIdEvenement($id);
            $participe->setIdUtilisateur(intval($_SESSION['id']));
            $participeRepository->save($participe);


            $pourcent = (($nbParticipant + 1) / $nbParticipantMax) * 100;

            if ($pourcent > 80) {
                $statut = $evenement->selectOneByLibelle('Presque complet');
                $evenement->setStatuts($statut);
            } else if ($pourcent === 100) {
                $statut = $evenement->selectOneByLibelle('Complet');
                $evenement->setStatuts($statut);
            }
            $message = 'Participation pris en compte';
        } else {
            $message = 'Evenement complet impossible d\'y participer';
        }

        echo $this->twig->render('evenements/evenement.html.twig', [
            'evenement' => $evenement,
            'message' => $message,
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
    #[Route(path: "/admin/evenements/filter", httpMethod: 'POST', name: "admin_evenements_filter",)]
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
        $evenement->setIdUtilisateur(intval($_SESSION['id']));
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

    #[Route(path: "/mapshow", httpMethod: 'GET', name: "mapshow")]
    public function mapshow(){
        echo $this->twig->render('maps/maps_show.html.twig');
    }


    #[Route(path: "/map", httpMethod: 'POST', name: "map")]
    public function maps(){

            var_dump($_POST['addr']);
            $url = "https://api-adresse.data.gouv.fr/search/?q=8+bd+du+port";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);
            var_dump($resp);
    }
}