<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Entity\Medias;
use App\Entity\Participe;
use App\Repository\AdressesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\EvenementsRepository;
use App\Repository\MediasRepository;
use App\Repository\ParticipeRepository;
use App\Repository\StatutsRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use DateTime;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EvenementsController extends AbstractController
{

    /**
     * Route pour afficher tout les évenements par catégories
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/evenements", httpMethod: 'GET', name: "evenements",)]
    public function evenements(EvenementsRepository $evenementsRepository, CategoriesRepository $categoriesRepository, StatutsRepository $statutsRepository, AdressesRepository $adressesRepository)
    {
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();

        // Met en ordres les evenements par catégorie / Uniquement les évenement avec statuts non passé
        $evenementsOrderByCategories = $this->_orderEvenementsInCategories($categoriesRepository, $evenementsRepository->selectAllEvenementNotPast());

        echo $this->twig->render('evenements/evenements.html.twig', [
            'evenementsOrderByCategories' => $evenementsOrderByCategories,
            'categories' => $categories,
            'statuts' => $statuts,
            'adresses' => $adresses,
        ]);
    }

    /**
     * Route de filtrage des evenements en GET
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/evenements/filter", httpMethod: 'GET', name: "evenements_filter",)]
    public function evenementsFilter(EvenementsRepository $evenementsRepository,CategoriesRepository $categoriesRepository, StatutsRepository $statutsRepository, AdressesRepository $adressesRepository, Request $request)
    {
        // Données pour les selects des filtres
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];
        // Query pour ne pas afficher les évenements avec le statut passé
        $query = 'JOIN statuts as s ON evenements.id_statut = s.id_statut ';
        $andWhereQuery = "s.libelle_statut != 'Passé'";

        // Récupérations des attributs get de l'url
        $filtre_titre = $request->query->get('filter_titre');
        $filtre_statut = $request->query->get('filtre_statut');
        $filtre_city = $request->query->get('filtre_city');
        $filtre_cp = $request->query->get('filtre_cp');
        $filtre_categorie = $request->query->get('filtre_categorie');
        $filtre_order_date = $request->query->get('order_date');

        if ($filtre_titre) {
            $filtres['filter_titre'] = $filtre_titre;
            $conditions[] = 'titre LIKE ?';
            $parameters[] = '%'.$filtre_titre."%";
        }

        if ($filtre_statut) {
            $filtres['filtre_statut'] = $filtre_statut;
            $conditions[] = 'id_statut = ?';
            $parameters[] = $filtre_statut;
        }

        if ($filtre_categorie) {
            $filtres['filtre_categorie'] = $filtre_categorie;
            $conditions[] = 'id_categorie = ?';
            $parameters[] = $filtre_categorie;
        }

        if ($filtre_city || $filtre_cp) {
            $query .= ' JOIN adresses ON adresses.id_adresse = evenements.id_adresse';
        }

        if ($filtre_city) {
            $filtres['filtre_city'] =$filtre_city;
            $conditions[] = 'adresses.ville_libelle LIKE ?';
            $parameters[] = '%'.$filtre_city."%";
        }

        if ($filtre_cp) {
            $filtres['filtre_cp'] = $filtre_cp;
            $conditions[] = 'adresses.cp_ville LIKE ?';
            $parameters[] = '%'.$filtre_cp."%";
        }
        // Filtrage, si date il faut changer le order
        if ($filtre_order_date) {
            $filtres['order_date'] = $filtre_order_date;
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query,'date' , $filtre_order_date, '', $andWhereQuery);
        }
        else {
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query,'','','', $andWhereQuery);
        }

        // On remet les évenements par categorie après le filtrage
        $evenementsOrderByCategories = $this->_orderEvenementsInCategories($categoriesRepository, $evenements);
        echo $this->twig->render('evenements/evenements.html.twig', [
            'evenementsOrderByCategories' => $evenementsOrderByCategories,
            'filtres' => $filtres,
            'categories' => $categories,
            'statuts' => $statuts,
            'adresses' => $adresses,
        ]);
    }

    /**
     * Voir un evenement
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/evenement", httpMethod: 'GET', name: "evenement",)]
    public function evenement(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository, Request $request, Session $session)
    {
        // Selection de l'evenement
        $id = $request->query->get('id');
        $evenement = $evenementsRepository->selectOneById($id);

        // Récuperation du nombre de participant actuel et le nombre de participant maximum accepté
        $arrayParticipe = $this->getNbParticipants($id, $evenement, $participeRepository);
        $nbParticipant = $arrayParticipe[0];
        $nbParticipantMax = $arrayParticipe[1];

        // Messages
        $completEvent = "";
        $noConnectedMessage = null;
        $alreadyParticipe = null;

        // Nombre de place disponible
        $place = $evenementsRepository->nbPlaceDisponible($id);
        // Check la participation
        if (!empty($_SESSION['id'])) {
            $alreadyParticipe = $participeRepository->checkIfAlreadyParticipe($_SESSION['id'], $id);
        } else {
            $noConnectedMessage = 'Vous devez vous connecter pour vous inscrire à un évènement';
        }

        // Check si l'evenement est complet
        if ($nbParticipant  >= $nbParticipantMax) {
            $completEvent = "complet";
        }

        echo $this->twig->render('evenements/evenement.html.twig', [
            'evenement' => $evenement,
            'desinscription' => $session->get('desinscription'),
            'successParticiper' => $session->get('successParticiper'),
            'errorParticiper' => $session->get('errorParticiper'),
            'alreadyParticipe' => $alreadyParticipe,
            'noConnectedMessage' => $noConnectedMessage,
            'place' => $place,
            'completEvent' => $completEvent
        ]);

        // Suppresion des messages
        $session->delete('successParticiper');
        $session->delete('errorParticiper');
        $session->delete('desinscription');
    }

    /**
     * Participer à un évenement, met à jour le statut
     * @throws ReflectionException
     */
    #[Route(path: "/evenement/participe", name: "evenement_participe")]
    public function participeEvenement(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository ,Session $session, Request $request, StatutsRepository $statutsRepository)
    {
        // Récupération de l'evenement
        $id = $request->query->get('idEvenement');
        $evenement = $evenementsRepository->selectOneById($id);

        // Récuperation du nombre de participant actuel et le nombre de participant maximum accepté
        $arrayParticipe = $this->getNbParticipants($id, $evenement, $participeRepository);
        $nbParticipant = $arrayParticipe[0];
        $nbParticipantMax = $arrayParticipe[1];

        if ($nbParticipant < $nbParticipantMax) {
            // Ajout de la participation
            $this->_addUtilisateurEvenement($id, $participeRepository);
            // Mise à jour du statut
            $this->_changeStatutEvenement($nbParticipant + 1, $nbParticipantMax, $evenement, $statutsRepository, $evenementsRepository);

            // Message
            $session->set('successParticiper', 'Participation pris en compte');
        } else {
            $session->set('errorParticiper', 'Evenement complet impossible d\'y participer');
        }

        header("Location: /evenement?id=" . $id );
    }

    /**
     * Ce desincrire d'un évenement, mets à jour le statut
     * @throws ReflectionException
     */
    #[Route(path: "/evenement/desincription", name: "evenement_ne_plus_participe")]
    public function deleteParticipation(ParticipeRepository $participeRepository, EvenementsRepository $evenementsRepository, StatutsRepository $statutsRepository, Session $session, Request $request)
    {
        $idUser = $_SESSION['id'];
        $idEvent= $request->query->get('idEvenement');
        $participeRepository->deleteUtilisateur($idUser, $idEvent);
        $evenement = $evenementsRepository->selectOneById($idEvent);

        // Récuperation du nombre de participant actuel et le nombre de participant maximum accepté
        $arrayParticipe = $this->getNbParticipants($idEvent, $evenement, $participeRepository);
        // Mise à jour du statut
        $this->_changeStatutEvenement($arrayParticipe[0], $arrayParticipe[1], $evenement, $statutsRepository, $evenementsRepository);
        // Message
        $session->set('desinscription', 'Vous êtes bien désinscrit de l\'évènement');
        header("Location: /evenement?id=" . $idEvent );
    }



    /**
     *  Route admin pour lister tous les évenements
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/evenements", name: "admin_evenements",)]
    public function adminEvenements(EvenementsRepository $evenementsRepository,
                                    CategoriesRepository $categoriesRepository,
                                    StatutsRepository $statutsRepository,
                                    AdressesRepository $adressesRepository,
                                    ParticipeRepository $participeRepository,
                                    Session $session, UtilisateursRepository $utilisateursRepository
    )
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Selections des evenements
        $evenements = $evenementsRepository->selectAll('date', 'DESC');

        // Données pour le filtrage
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();
        $participes = $participeRepository->selectAll();


        echo $this->twig->render('admin/evenements/admin_evenements.html.twig', [
            'evenements' => $evenements,
            'categories' => $categories,
            'statuts' => $statuts,
            'adresses' => $adresses,
            'popup' => $session->get('popup'),
            'arrayParticipeUtilisateurs' => $this->_getNbParticipants($evenements, $participes)
        ]);

        // Suppresion pop-up
        $session->delete('popup');
    }

    /**
     * Filtrage des evenements en GET
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/evenements/filter", httpMethod: 'GET', name: "admin_evenements_filter",)]
    public function adminEvenementsFilter(EvenementsRepository $evenementsRepository,
                                          CategoriesRepository $categoriesRepository,
                                          StatutsRepository $statutsRepository,
                                          AdressesRepository $adressesRepository,
                                          ParticipeRepository $participeRepository,
                                          Request $request, UtilisateursRepository $utilisateursRepository, Session $session
    )
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');

        // Pour les selects du filtrage
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();
        $participes = $participeRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];
        $query = '';

        // Récupérations des attributs get de l'url
        $filtre_titre = $request->query->get('filter_titre');
        $filtre_statut = $request->query->get('filtre_statut');
        $filtre_city = $request->query->get('filtre_city');
        $filtre_cp = $request->query->get('filtre_cp');
        $filtre_categorie = $request->query->get('filtre_categorie');
        $filtre_order_date = $request->query->get('order_date');

        if ($filtre_titre) {
            $filtres['filter_titre'] = $filtre_titre;
            $conditions[] = 'titre LIKE ?';
            $parameters[] = '%'.$filtre_titre."%";
        }

        if ($filtre_categorie) {
            $filtres['filtre_categorie'] = $filtre_categorie;
            $conditions[] = 'id_categorie = ?';
            $parameters[] = $filtre_categorie;
        }

        if ($filtre_statut) {
            $filtres['filtre_statut'] = $filtre_statut;
            $conditions[] = 'id_statut = ?';
            $parameters[] = $filtre_statut;
        }

        // Ajout de la jointure si filtre sur la table adresse
        if ($filtre_city || $filtre_cp) {
            $query = 'JOIN adresses ON adresses.id_adresse = evenements.id_adresse';
        }

        if ($filtre_city) {
            $filtres['filtre_city'] = $filtre_city;
            $conditions[] = 'adresses.ville_libelle LIKE ?';
            $parameters[] = '%'.$filtre_city."%";
        }

        if ($filtre_cp) {
            $filtres['filtre_cp'] = $filtre_cp;
            $conditions[] = 'adresses.cp_ville LIKE ?';
            $parameters[] = '%'.$filtre_cp."%";
        }
        // Filtrage, si date il faut changer le order
        if ($filtre_order_date) {
            $filtres['order_date'] = $filtre_order_date;
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query,'date' , $filtre_order_date);
        }
        else {
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
     * Route pour afficher le formulaire de création d'évenement
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/evenements", name: "admin_create_evenements",)]
    public function createEvenements(CategoriesRepository $categoriesRepository,
                                     StatutsRepository $statutsRepository,
                                     AdressesRepository $adressesRepository,
                                     UtilisateursRepository $utilisateursRepository, Session $session
    )
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Pour les selects
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
     * Ajout de l'evenement en BDD
     * @throws Exception
     */
    #[Route(path: "/admin/add/evenements", httpMethod: 'POST', name: "admin_add_evenements",)]
    public function addEvenements(EvenementsRepository $evenementsRepository , MediasRepository $mediasRepository,StatutsRepository $statutsRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Si pas d'image
        if (!isset($_FILES['imageEvent'])) {
            echo "Erreur : pas d'image";
            return;
        }

        // Récupération de l'image
        $image = $_FILES['imageEvent'];

        // Enregistrement de l'image dans le dossier
        if (
            is_uploaded_file($image['tmp_name']) &&
            move_uploaded_file(
                $image['tmp_name'],__DIR__ . DIRECTORY_SEPARATOR . '../../public/events/' . basename($image['name'])
            )
        ) {
            // Ajout du media en BDD
            $media= new Medias();
            $media->setNom(basename($image['name']));
            $media->setPath('events/' . basename($image['name']));
            $media->setType($image['type']);
            $mediasRepository->save($media);

            // Récupération du dernier média ajouter, donc celui importer dans ceux formulaire
            $idmedia = $mediasRepository->getLastId();
            // Statut par défaut de l'événement
            $statut = $statutsRepository->selectOneByLibelle('A venir');

            // Creation de l'evenement en BDDs
            $evenement = new Evenements();
            $evenement->setTitre($_POST['evenementTitle']);
            $evenement->setSousTitre($_POST['evenementSubTitle']);
            $evenement->setDate(new DateTime($_POST['evenementDate']));
            $evenement->setNbParticipantsMax($_POST['nbParticipantMax']);
            $evenement->setPrix($_POST['prix']);
            $evenement->setDescription($_POST['description']);
            $evenement->setIdCategorie(($_POST['categorieSelect']));
            $evenement->setIdStatut($statut[0]->getIdStatut());
            $evenement->setIdAdresse(intval($_POST['adresseSelect']));
            $evenement->setCreatedAt(new DateTime());
            $evenement->setUpdatedAt(new DateTime());
            $evenement->setIdUtilisateur(intval($_SESSION['id']));
            $evenement->setIdMedia($idmedia);

            $evenementsRepository->save($evenement);

            header('Location: /admin/evenements');
        } else {
            echo "Erreur lors de l'upload";
        }

    }

    /**
     * Route pour afficher le formulaire d'édition de l'évenement
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/evenements", httpMethod: 'GET', name: "admin_edit_evenements",)]
    public function editEvenements(EvenementsRepository $evenementsRepository,
                                   CategoriesRepository $categoriesRepository,
                                   StatutsRepository $statutsRepository,
                                   AdressesRepository $adressesRepository,
                                   ParticipeRepository $participeRepository,
                                   Request $request, UtilisateursRepository $utilisateursRepository, Session $session
    )
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Selection de l'évenement à mettre à jour
        $id = $request->query->get('id');
        $evenement = $evenementsRepository->selectOneById($id);

        // Selects
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
     * Route pour enregistrer l'evenement dans la BDD
     * @throws Exception
     */
    #[Route(path: "/admin/update/evenements", httpMethod: 'POST', name: "admin_update_evenements",)]
    public function updateEvenements(EvenementsRepository $evenementsRepository, MediasRepository $mediasRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Selection de l'evenement
        $evenement = $evenementsRepository->selectOneById(intval($_POST['id']));

        // Mise à jour de l'évenement
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

        // Gestion de l'image
        if (isset($_FILES['imageEvent'])) {

            $image = $_FILES['imageEvent'];
            if (
                is_uploaded_file($image['tmp_name']) &&
                move_uploaded_file(
                    $image['tmp_name'],__DIR__ . DIRECTORY_SEPARATOR . '../../public/events/' . basename($image['name'])
                )
            ) {
                // Ajout du medias et sauvegarde dans l'evenement
                $media= new Medias();
                $media->setNom(basename($image['name']));
                $media->setPath('events/' . basename($image['name']));
                $media->setType($image['type']);
                $mediasRepository->save($media);

                $idmedia= $mediasRepository->getLastId();

                $evenement->setIdMedia($idmedia);
            }
        }
        // Mise à jour BDD
        $evenementsRepository->update($evenement);

        header('Location: /admin/evenements');
    }


    /**
     * Route pour la suppresion d'un evenement
     * @throws ReflectionException
     */
    #[Route(path: "/admin/delete/evenements", httpMethod: 'POST', name: "admin_delete_evenements")]
    public function deleteEvenements(EvenementsRepository $evenementsRepository,Session $session, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $session->delete('popup');
        $id = intval($_POST['id']);
        // Vérification des contraintes de l'adresse avant la suppresion
        $utilisateursParticipantEvenement = $evenementsRepository->verifContraintsUtilisateursParticipes($id);
        if ($utilisateursParticipantEvenement !== null) {
            // Pop-up pour informer qu'il y a des utilisateurs qui participe à l'évenement, mais possible de forcer la suppresion
            $rp[0] = "participant";
            $rp[1] = $id;
            $session->set('popup',$rp);
        } else {
            // Suppresion
            $evenementsRepository->delete($id);
        }
        header('Location: /admin/evenements');
    }

    /**
     * Supprimer l'evenements et toutes les participations à l'evenements
     * @param EvenementsRepository $evenementsRepository
     * @param UtilisateursRepository $utilisateursRepository
     * @param Session $session
     */
    #[Route(path: "/admin/delete/evenements/cascade", httpMethod: 'POST', name: "admin_delete_evenements_cascade")]
    public function deleteEvenementsCascade(EvenementsRepository $evenementsRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['id']);
        $evenementsRepository->deleteCascadeEvenementParticipe($id);
        header('Location: /admin/evenements');
    }


    /**
     * Route pour afficher les utilisateurs participant à l'évenement
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/evenements/list/utilisateurs", httpMethod: 'GET', name: "admin_evenements_list_utilisateurs",)]
    public function adminEvenementsListUtilisateurs(Request $request, EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Selection de l'evenement
        $id = $request->query->get('id');
        $evenement = $evenementsRepository->selectOneById($id);
        $participes = $participeRepository->selectAll();

        // Récupération des utilisateurs participant à l'évenement
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

    /**
     * Supprimer la participation de l'utilisateur à l'évenement
     * @param ParticipeRepository $participeRepository
     * @param UtilisateursRepository $utilisateursRepository
     * @param Session $session
     */
    #[Route(path: "/admin/delete/evenement/utilisateur", httpMethod: 'POST', name: "admin_delete_evenement_utilisateur")]
    public function deleteUtilisateur(ParticipeRepository $participeRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $idUtilisateur = intval($_POST['idUtilisateur']);
        $idEvenement = intval($_POST['idEvenement']);
        $participeRepository->deleteUtilisateur($idUtilisateur, $idEvenement);
        header('Location: /admin/evenements');
    }


    /**
     * Récupération du nombre de participant à l'évenement
     * Tableau avec les id des utilisateurs qui particpe aux evenements avec comme ket l'id evenement
     * @param array $evenements
     * @param array $participes
     * @param int|null $evenementId
     * @return array
     */
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

    /**
     * Retourne un tableau contenant des évenements avec comme index la catégorie à laquelles les évenements appartiennent
     * @param CategoriesRepository $categoriesRepository
     * @param $evenements
     * @return array
     */
    private function _orderEvenementsInCategories(CategoriesRepository $categoriesRepository, $evenements): array
    {
        $evenementsCategories = [];
        $categories = $categoriesRepository->selectAll();
        foreach ($categories as $category) {
            $evenementsCategories[$category->getLibelleCategorie()] = [];
        }
        foreach ($categories as $category) {
            foreach ($evenements as $evenement) {
                if ($evenement->getIdCategorie() === $category->getIdCategorie()) {
                    array_push($evenementsCategories[$category->getLibelleCategorie()], $evenement);
                }
            }
        }
        return $evenementsCategories;
    }

    /**
     * Ajout de la participation
     * @param int $id
     * @param ParticipeRepository $participeRepository
     */
    private function _addUtilisateurEvenement(int $id,ParticipeRepository $participeRepository ) {
        $participe = new Participe();
        $participe->setIdEvenement($id);
        $participe->setIdUtilisateur(intval($_SESSION['id']));
        $participeRepository->save($participe);
    }

    /**
     * Changer le statut de l'evenement lors d'une participation ou d'une désincription
     * @param int $nbParticipant
     * @param int $nbParticipantMax
     * @param Evenements $evenement
     * @param StatutsRepository $statutsRepository
     * @param EvenementsRepository $evenementsRepository
     * @throws ReflectionException
     */
    private function _changeStatutEvenement(int $nbParticipant, int $nbParticipantMax, Evenements $evenement, StatutsRepository $statutsRepository, EvenementsRepository $evenementsRepository) {
        $pourcent = ($nbParticipant / $nbParticipantMax) * 100;

        if ($pourcent >= 0 && $pourcent < 80) {
            $statut = $statutsRepository->selectOneByLibelle('A venir');
            $evenement->setIdStatut($statut[0]->getIdStatut());
        } else if ($pourcent >= 80 && $pourcent < 100) {
            $statut = $statutsRepository->selectOneByLibelle('Presque complet');
            $evenement->setIdStatut($statut[0]->getIdStatut());
        } else if ($pourcent === 100) {
            $statut = $statutsRepository->selectOneByLibelle('Complet');
            $evenement->setIdStatut($statut[0]->getIdStatut());
        }
        $evenementsRepository->update($evenement);
    }

    /**
     * Récupérer le nombre de participant et le nombre de capacité max de l'évenement
     * Retourne un Array avec :
     *      - index 0 : nombre de participant
     *      - index 1 : capacité max
     * @param int $id
     * @param Evenements $evenement
     * @param ParticipeRepository $participeRepository
     * @return array
     */
    private function getNbParticipants(int $id, Evenements $evenement,ParticipeRepository $participeRepository): array{
        $participes = $participeRepository->selectAll();
        $nbParticipantMax = $evenement->getNbParticipantsMax();
        $nbParticipantArray = $this->_getNbParticipants([], $participes, $id);
        $nbParticipant = count($nbParticipantArray[$id]);

        return [$nbParticipant,$nbParticipantMax];
    }
}