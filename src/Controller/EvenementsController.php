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
use PhpParser\Node\Stmt\TryCatch;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EvenementsController extends AbstractController
{

    /**
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

        $evenementsOrderByCategories = $this->_orderEvenementsInCategories($categoriesRepository, $evenementsRepository->selectAllEvenementNotPast());

        echo $this->twig->render('evenements/evenements.html.twig', [
            'evenementsOrderByCategories' => $evenementsOrderByCategories,
            'categories' => $categories,
            'statuts' => $statuts,
            'adresses' => $adresses,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/evenements/filter", httpMethod: 'GET', name: "evenements_filter",)]
    public function evenementsFilter(EvenementsRepository $evenementsRepository,CategoriesRepository $categoriesRepository, StatutsRepository $statutsRepository, AdressesRepository $adressesRepository, Request $request)
    {

        $categories = $categoriesRepository->selectAll();
        $statuts = $statutsRepository->selectAll();
        $adresses = $adressesRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];
        $query = 'JOIN statuts as s ON evenements.id_statut = s.id_statut ';
        $andWhereQuery = "s.libelle_statut != 'Passé'";

        // Filtres
        $request->query->get('id');
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

        if ($filtre_order_date) {
            $filtres['order_date'] = $filtre_order_date;
            
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query,'date' , $filtre_order_date, '', $andWhereQuery);
        }
        else {
            $evenements = $evenementsRepository->filter($conditions, $parameters, $query,'','','', $andWhereQuery);
        }

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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/evenement", httpMethod: 'GET', name: "evenement",)]
    public function evenement(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository, Request $request, Session $session)
    {
        $noConnectedMessage = null;
        $alreadyParticipe = null;

        $id = $request->query->get('id');
        $evenement = $evenementsRepository->selectOneById($id);
        $place = $evenementsRepository->nbPlaceDisponible($id);
        if (!empty($_SESSION['id'])) {
            $alreadyParticipe = $participeRepository->checkIfAlreadyParticipe($_SESSION['id'], $id);
        } else {
            $noConnectedMessage = 'Vous devez vous connecter pour vous inscrire à un évènement';
        }
        

        echo $this->twig->render('evenements/evenement.html.twig', [
            'evenement' => $evenement,
            'desinscription' => $session->get('desinscription'),
            'successParticiper' => $session->get('successParticiper'),
            'errorParticiper' => $session->get('errorParticiper'),
            'alreadyParticipe' => $alreadyParticipe,
            'noConnectedMessage' => $noConnectedMessage,
            'place' => $place
        ]);

        $session->delete('successParticiper');
        $session->delete('errorParticiper');
        $session->delete('desinscription');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/evenement/participe", name: "evenement_participe")]
    public function participeEvenement(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository ,Session $session, Request $request, StatutsRepository $statutsRepository)
    {
        $id = $request->query->get('idEvenement');

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
                $statut = $statutsRepository->selectOneByLibelle('Presque complet');
                $evenement->setStatuts($statut);
            } else if ($pourcent === 100) {
                $statut = $statutsRepository->selectOneByLibelle('Complet');
                $evenement->setStatuts($statut);
            }
            $session->set('successParticiper', 'Participation pris en compte');
        } else {
            $session->set('errorParticiper', 'Evenement complet impossible d\'y participer');
        }
        
        header("Location: /evenement?id=" . $id );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/evenement/desincription", name: "evenement_ne_plus_participe")]
    public function deleteParticipation(EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository ,Session $session, Request $request, StatutsRepository $statutsRepository)
    {
        $idUser = $_SESSION['id'];
        $idEvent= $request->query->get('idEvenement');
        $participeRepository->deleteUtilisateur($idUser, $idEvent);
        
        $session->set('desinscription', 'Vous êtes bien désinscrit de l\'évènement');
        header("Location: /evenement?id=" . $idEvent );
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
                                    ParticipeRepository $participeRepository,
                                    Session $session
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
    #[Route(path: "/admin/evenements/filter", httpMethod: 'POST', name: "admin_evenements_filter",)]
    public function adminEvenementsFilter(EvenementsRepository $evenementsRepository,
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
    public function addEvenements(EvenementsRepository $evenementsRepository , MediasRepository $mediasRepository)
    {
       
       
        // TODO WALID FILE UPLOAD
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
            $media= new Medias();
            $media->setNom(basename($image['name']));
            $media->setPath('events/' . basename($image['name']));
            $media->setType($image['type']);
            $mediasRepository->save($media);

            $idmedia= $mediasRepository->getLastId();

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

        header('Location: /admin/evenements');
          } else {
            echo "Erreur lors de l'upload";
          }
          
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
    public function updateEvenements(EvenementsRepository $evenementsRepository, MediasRepository $mediasRepository)
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

        header('Location: /admin/evenements');
    }


    /**
     * @throws ReflectionException
     */
    #[Route(path: "/admin/delete/evenements", httpMethod: 'POST', name: "admin_delete_evenements")]
    public function deleteEvenements(EvenementsRepository $evenementsRepository,Session $session)
    {
        $session->delete('popup');
        $id = intval($_POST['id']);
        $utilisateursParticipantEvenement = $evenementsRepository->verifContraintsUtilisateursParticipes($id);
        if ($utilisateursParticipantEvenement !== null) {
            // TODO POP UP
            // Message pop-up Impossible de l'eveneemnt car des utilisateurs y sont inscrits afficher les mails utilisateurs
            $rp[0]="participant";$rp[1]=$id;
            $session->set('popup',$rp);
            header('Location: /admin/evenements');
        } else {
            $evenementsRepository->delete($id);
            header('Location: /admin/evenements');
        }
    }


    #[Route(path: "/admin/delete/evenements/cascade", httpMethod: 'POST', name: "admin_delete_evenements_cascade")]
    public function deleteEvenementsCascade(EvenementsRepository $evenementsRepository)
    {
        $id = intval($_POST['id']);
        $evenementsRepository->deleteCascadeEvenementParticipe($id);
        header('Location: /admin/evenements');
    }



    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/evenements/list/utilisateurs", httpMethod: 'GET', name: "admin_evenements_list_utilisateurs",)]
    public function adminEvenementsListUtilisateurs(Request $request, EvenementsRepository $evenementsRepository, ParticipeRepository $participeRepository, UtilisateursRepository $utilisateursRepository)
    {
        $id = $request->query->get('id');
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

        try {
            $participeRepository->deleteUtilisateur($idUtilisateur, $idEvenement);
        } catch (\Throwable $th) {
            //throw $th;
        }

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

    private function _orderEvenementsInCategories(CategoriesRepository $categoriesRepository, $evenements) {
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
   
}