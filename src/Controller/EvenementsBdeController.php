<?php

namespace App\Controller;

use App\Entity\Evenements;
use App\Entity\Medias;
use App\Entity\Participe;
use App\Repository\AdressesRepository;
use App\Repository\CategoriesRepository;
use App\Repository\EcolesRepository;
use App\Repository\EvenementsRepository;
use App\Repository\MediasRepository;
use App\Repository\ParticipeRepository;
use App\Repository\RolesRepository;
use App\Repository\StatutsRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use DateTime;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
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
                                    ParticipeRepository $participeRepository,
                                    Session $session,
                                    UtilisateursRepository $utilisateursRepository,
    )
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        $id = $_SESSION["id"];
        $evenements = $evenementsRepository->selectEvenementByUser($id);
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();
        $participes = $participeRepository->selectAll();


        echo $this->twig->render('bde/evenements/bde_evenements.html.twig', [
            'evenements' => $evenements,
            'categories' => $categories,
            'statuts' => $statuts,
            'adresses' => $adresses,
            'popup' => $session->get('popup'),
            'arrayParticipeUtilisateurs' => $this->_getNbParticipants($evenements, $participes)
        ]);
        $session->delete('popup');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/bde/evenements/filter", httpMethod: 'GET', name: "bde_evenements_filter",)]
    public function evenementsFilter(EvenementsRepository $evenementsRepository,
                                     CategoriesRepository $categoriesRepository,
                                     StatutsRepository $statutsRepository,
                                     AdressesRepository $adressesRepository,
                                     ParticipeRepository $participeRepository,
                                     Request $request, UtilisateursRepository $utilisateursRepository, Session $session
    )
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();
        $participes = $participeRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];
        $query = '';

        $filter_titre = $request->query->get('filter_titre');
        $filtre_categorie = $request->query->get('filtre_categorie');
        $filtre_statut = $request->query->get('filtre_statut');
        $filtre_city = $request->query->get('filtre_city');
        $filtre_cp = $request->query->get('filtre_cp');
        $order_date = $request->query->get('order_date');

        //Utilisateur BDE 
        $id = $_SESSION["id"];
        $conditions[] = 'evenements.id_utilisateur = ?';
        $parameters[] = $_SESSION["id"];

        if ($filter_titre) {
            $filtres['filter_titre'] = $filter_titre;
            $conditions[] = 'titre LIKE ?';
            $parameters[] = '%'.$filter_titre."%";
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
        if ($order_date) {
            $filtres['order_date'] = $order_date;
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query,'date' , $order_date);
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
                                   AdressesRepository $adressesRepository, UtilisateursRepository $utilisateursRepository, Session $session
    )
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
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
     * @throws \Exception
     */
    #[Route(path: "/bde/add/evenements", httpMethod: 'POST', name: "bde_add_evenements",)]
    public function addEvenements(EvenementsRepository $evenementsRepository, MediasRepository $mediasRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        if (!isset($_FILES['imageEvent'])) {
            echo "Erreur : pas d'image";
            return;
        }

        $image = $_FILES['imageEvent'];

        if (
            is_uploaded_file($image['tmp_name']) &&
            move_uploaded_file(
                $image['tmp_name'],__DIR__ . DIRECTORY_SEPARATOR . '../../public/events/' . basename($image['name'])
            )
        ) {
            $media = new Medias();
            $media->setNom(basename($image['name']));
            $media->setPath('events/' . basename($image['name']));
            $media->setType($image['type']);
            $mediasRepository->save($media);

            $idmedia = $mediasRepository->getLastId();

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
            $evenement->setIdMedia($idmedia);

            $evenementsRepository->save($evenement);

            header('Location: /bde/evenements');
        } else {
            echo "Erreur lors de l'upload";
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/bde/edit/evenements", httpMethod: 'GET', name: "bde_edit_evenements",)]
    public function editEvenements(EvenementsRepository $evenementsRepository,
                                   CategoriesRepository $categoriesRepository,
                                   StatutsRepository $statutsRepository,
                                   AdressesRepository $adressesRepository,
                                   ParticipeRepository $participeRepository,
                                   Request $request, UtilisateursRepository $utilisateursRepository, Session $session
    )
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        $id = $request->query->get('id');

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
    public function updateEvenements(EvenementsRepository $evenementsRepository, MediasRepository $mediasRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
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

        if (isset($_FILES['imageEvent'])) {

            $image = $_FILES['imageEvent'];
            if (
                is_uploaded_file($image['tmp_name']) &&
                move_uploaded_file(
                    $image['tmp_name'],__DIR__ . DIRECTORY_SEPARATOR . '../../public/events/' . basename($image['name'])
                )
            ) {
                $media= new Medias();
                $media->setNom(basename($image['name']));
                $media->setPath('events/' . basename($image['name']));
                $media->setType($image['type']);
                $mediasRepository->save($media);

                $idmedia= $mediasRepository->getLastId();

                $evenement->setIdMedia($idmedia);
            }
        }

        $evenementsRepository->update($evenement);

        header('Location: /bde/evenements');
    }


    /**
     * @throws ReflectionException
     */
    #[Route(path: "/bde/delete/evenements", httpMethod: 'POST', name: "bde_delete_evenements")]
    public function deleteEvenements( EvenementsRepository $evenementsRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        $id = intval($_POST['id']);
        $utilisateursParticipantEvenement = $evenementsRepository->verifContraintsUtilisateursParticipes($id);
        if ($utilisateursParticipantEvenement !== null) {
            $rp[0]= "participantBde";
            $rp[1]= $id;
            $session->set('popup', $rp);
        } else {
            $evenementsRepository->delete($id);
        }
        header('Location: /bde/evenements');
    }

    #[Route(path: "/bde/delete/evenements/cascade", httpMethod: 'POST', name: "bde_delete_evenements_cascade")]
    public function deleteEvenementsCascade(EvenementsRepository $evenementsRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        $id = intval($_POST['id']);
        $evenementsRepository->deleteCascadeEvenementParticipe($id);
        header('Location: /bde/evenements');
    }



    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/bde/evenements/list/utilisateurs", httpMethod: 'GET', name: "bde_evenements_list_utilisateurs",)]
    public function adminEvenementsListUtilisateurs(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository, UtilisateursRepository $utilisateursRepository, Request $request, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        $id = $request->query->get('id');
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

    #[Route(path: "/bde/delete/evenement/utilisateur", httpMethod: 'POST', name: "bde_delete_evenement_utilisateur")]
    public function deleteUtilisateur(ParticipeRepository $participeRepository, UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'BDE');
        $idUtilisateur = intval($_POST['idUtilisateur']);
        $idEvenement = intval($_POST['idEvenement']);

        $participeRepository->deleteUtilisateur($idUtilisateur, $idEvenement);

        header('Location: /bde/evenements');
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