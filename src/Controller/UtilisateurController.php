<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use DateTime;
use App\Session\Session;
use Exception;
use ReflectionException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;
use App\Routing\Attribute\Route;
use App\Repository\RolesRepository;
use App\Repository\EcolesRepository;
use App\Repository\PromotionsRepository;
use App\Repository\UtilisateursRepository;

class UtilisateurController extends AbstractController
{

    /**
     * Affiche profil utilisateur
     * @param UtilisateursRepository $utilisateursRepository
     * @param EcolesRepository $ecolesRepository
     * @param PromotionsRepository $promotionsRepository
     * @param Session $session
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route(path: "/utilisateurs/profil", name: "profil_utilisateurs")]
    public function profilUtilisateur(UtilisateursRepository $utilisateursRepository, EcolesRepository $ecolesRepository, PromotionsRepository $promotionsRepository, Session $session)
    {
        // Récupération des informations utilisateurs
        $id = $_SESSION['id'];
        $user = $utilisateursRepository->selectOneById($id);
        $idPromotion = $user->getPromotions()->getIdPromotion();
        $promotion = $promotionsRepository->selectOneById($idPromotion);

        $ecole = $ecolesRepository->selectOneById($idPromotion);

        echo $this->twig->render('utilisateur/profil_user.html.twig', [
            'user' => $user,
            'ecole' => $ecole,
            'promotion' => $promotion,
            'successUpdate' => $session->get('successUpdate')
        ]);

        // Suppresion message de modification succes
        $session->delete('successUpdate');
    }

    /**
     * Affiche page edition utilisateur
     * @param UtilisateursRepository $utilisateursRepository
     * @param EcolesRepository $ecolesRepository
     * @param PromotionsRepository $promotionsRepository
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route(path: "/utilisateurs/profil/edit", name: "edit_profil_utilisateurs")]
    public function editProfilUtilisateur(UtilisateursRepository $utilisateursRepository, EcolesRepository $ecolesRepository, PromotionsRepository $promotionsRepository)
    {
        // Récupération des informations utilisateurs
        $id = $_SESSION['id'];
        $user = $utilisateursRepository->selectOneById($id);
        $idPromotion = $user->getPromotions()->getIdPromotion();
        $promotion = $promotionsRepository->selectOneById($idPromotion);
        $ecole = $ecolesRepository->selectOneById($idPromotion);

        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();

        echo $this->twig->render('utilisateur/profil_edit_user.html.twig', [
            'user' => $user,
            'ecole' => $ecole,
            'promotion' => $promotion,
            'ecoles' => $ecoles,
            'promotions' => $promotions
        ]);
    }

    /**
     * Update des informations du profil
     * @return void
     * @throws Exception
     */
    #[Route(path: '/utilisateurs/profil/edit',httpMethod:"POST", name: "edit_user")]
    public function updateProfilUtilisateur(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        // Récupération de l'utilisateur
        $id = $_SESSION['id'];
        $user = $utilisateursRepository->selectOneById($id);
        // Ajout des modifications
        $user->setNom(trim($_POST["nom"]));
        $user->setPrenom(trim($_POST["prenom"]));
        $user->setDateNaissance(new DateTime($_POST['date']));
        $user->setIdPromotion(intval($_POST["promotions"]));
        $user->setTelephone(trim($_POST["telephone"]));
        $user->setMail(trim($_POST["email"]));

        // Mise à jour BDD
        $utilisateursRepository->update($user);
        // Message de succes
        $session->set('successUpdate', 'Vos informations ont bien été modifiées !');
        header("Location: http://localhost:8000/utilisateurs/profil");
    }

    /**
     * Route admin pour lister toutes les adresses
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/utilisateurs", name: "admin_utilisateurs",)]
    public function utilisateurs(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository , Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Récupération des utilisateurs et des informations pour les selects
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();
        $roles = $rolesRepository->selectAll();
        $utilisateurs = $utilisateursRepository->selectAll();

        echo $this->twig->render('admin/utilisateurs/admin_utilisateurs.html.twig', [
            'ecoles' => $ecoles,
            'roles' => $roles,
            'promotions' => $promotions,
            'utilisateurs' => $utilisateurs,
            'utilisateursPOP'=> $session->get('utilisateursPOP'),
        ]);

        // Suppresion des pop-ups
        $session->delete('utilisateursPOP');
    }

    /**
     * Route de filtrage des adresses en GET
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/utilisateurs/filter", httpMethod: 'GET', name: "admin_utilisateurs_filter",)]
    public function utilisateursFilter(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository,Session $session, Request $request)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');

        // Données pour les selects
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();
        $roles = $rolesRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];

        // Récupérations des attributs get de l'url
        $filtre_lastName = $request->query->get('filtre_lastName');
        $filtre_firstName = $request->query->get('filtre_firstName');
        $filtre_role = $request->query->get('filtre_role');
        $filtre_promotion = $request->query->get('filtre_promotion');

        if ($filtre_lastName) {
            $filtres['filtre_lastName'] = $filtre_lastName;
            $conditions[] = 'nom LIKE ?';
            $parameters[] = '%'.$filtre_lastName."%";
        }

        if ($filtre_firstName) {
            $filtres['filtre_firstName'] = $filtre_firstName;
            $conditions[] = 'prenom LIKE ?';
            $parameters[] = '%'.$filtre_firstName."%";
        }

        if ($filtre_role) {
            $filtres['filtre_role'] = $filtre_role;
            $conditions[] = 'id_role = ?';
            $parameters[] = $filtre_role;
        }

        if ($filtre_promotion) {
            $filtres['filtre_promotion'] = $filtre_promotion;
            $conditions[] = 'id_promotion = ?';
            $parameters[] = $filtre_promotion;
        }

        // Filtrage
        $utilisateurs = $utilisateursRepository->filter($conditions, $parameters);
        echo $this->twig->render('admin/utilisateurs/admin_utilisateurs.html.twig', [
            'ecoles' => $ecoles,
            'roles' => $roles,
            'promotions' => $promotions,
            'utilisateurs' => $utilisateurs,
            'filtres' => $filtres
        ]);
    }

    /**
     * Route d'affichage pour la création du formulaire des utilisateurs
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/create/utilisateurs", name: "admin_create_utilisateurs",)]
    public function createUtilisateurs(PromotionsRepository $promotionsRepository, RolesRepository $rolesRepository,Session $session, UtilisateursRepository $utilisateursRepository, EcolesRepository $ecolesRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Données pour les selects
        $roles = $rolesRepository->selectAll();
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();

        echo $this->twig->render('admin/utilisateurs/admin_form_create_utilisateur.html.twig', [
            'ecoles' => $ecoles,
            'promotions' => $promotions,
            'roles' => $roles,
        ]);
    }


    /**
     * Route pour enregistrer l'utilisateur dans la BDD
     * On peut lui attribuer directemeent un rôle contrairement à l'authentification
     * @throws Exception
     */
    #[Route(path: "/admin/add/utilisateurs", httpMethod: 'POST', name: "admin_add_utilisateurs",)]
    public function addUtilisateurs(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository,Session $session, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Récupération des méthodes de vérifications du controller d'authentification
        $authController = new AuthentificationController($this->twig);
        $verifRegister = $authController->_verifRegister($utilisateursRepository);

        // Si il y a des erreurs on redirige sur la page
        if ($verifRegister !== true ) {
            $promotions = $promotionsRepository->selectAll();
            $roles = $rolesRepository->selectAll();
            $ecoles = $ecolesRepository->selectAll();
            echo $this->twig->render("admin/utilisateurs/admin_form_create_utilisateur.html.twig", [
                'ecoles' => $ecoles,
                'promotions' => $promotions,
                'roles' => $roles,
                'errors' => $verifRegister
            ]);
        } else {
            // Création de l'utilisateur
            $user = new Utilisateurs();
            $user->setNom(trim($_POST["nom"]));
            $user->setPrenom(trim($_POST["prenom"]));
            $user->setDateNaissance(new DateTime($_POST['date_naissance']));
            $user->setIdPromotion(intval($_POST["promotions"]));
            $user->setTelephone(trim($_POST["telephone"]));
            $user->setDateInscription(new DateTime());
            $user->setMail(trim($_POST["email"]));
            $user->setPassword(trim($_POST['password']));
            $user->setIdRole(intval($_POST["roles"]));

            // Sauvegarde en BDD
            $utilisateursRepository->save($user);

            header("Location: /admin/utilisateurs");
        }
    }

    /**
     * Route d'affichage du formulaire de modification des utilisateurs
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/utilisateurs", httpMethod: 'GET', name: "admin_edit_utilisateurs",)]
    public function editUtilisateurs(PromotionsRepository $promotionsRepository, RolesRepository $rolesRepository,Session $session, UtilisateursRepository $utilisateursRepository, Request $request)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = $request->query->get('id');
        // Récupération de l'utilisateur à éditer pour remplir le formulaire
        $utilisateur = $utilisateursRepository->selectOneById($id);

        // Données pour les select
        $promotions = $promotionsRepository->selectAll();
        $roles = $rolesRepository->selectAll();

        echo $this->twig->render('admin/utilisateurs/admin_form_edit_utilisateur.html.twig', [
            'utilisateur' => $utilisateur,
            'promotions' => $promotions,
            'roles' => $roles,
        ]);
    }


    /**
     * Route pour éditer l'adresse dans la BDD
     * @throws Exception
     */
    #[Route(path: "/admin/update/utilisateurs", httpMethod: 'POST', name: "admin_update_utilisateurs",)]
    public function updateUtilisateurs(Session $session, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        // Selection de l'utilisateur à modifier
        $user = $utilisateursRepository->selectOneById(intval($_POST['id']));

        // Modifications
        $user->setNom($_POST['nom']);
        $user->setPrenom($_POST['prenom']);
        $user->setDateNaissance(new DateTime($_POST['dateNaissance']));
        $user->setMail($_POST['mail']);
        $user->setTelephone($_POST['telephone']);
        $user->setIdPromotion(intval($_POST['promotion']));
        $user->setIdRole(intval($_POST['role']));

        // Sauvegarde en BDD
        $utilisateursRepository->update($user);

        header('Location: /admin/utilisateurs');
    }


    /**
     * Route pour la suppresion d'un utilisateur
     * @throws ReflectionException
     */
    #[Route(path: "/admin/delete/utilisateurs", httpMethod: 'POST', name: "admin_delete_utilisateurs")]
    public function deleteUtilisateurs(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['idUtilisateur']);

        // Vérification des contraintes de l'utilisateur avant la suppresion
        $evenementsCreatedByUser = $utilisateursRepository->verifContraintsEvenementCreate($id);
        $evenementsParticipeByUser = $utilisateursRepository->verifContraintsParticipeEvenement($id);
        if ($evenementsCreatedByUser !== null) {
            // Pop-up pour informer de l'impossibilité de suppresion car l'utilisateur à créer un évenement
            $rp[0] = "utilisateurs";
            $rp[1] = 'null';
            $session->set('utilisateursPOP',$rp);
        } else if ($evenementsParticipeByUser !== null) {
            // Pop-up pour informer que l'utilisateur particpe à des évenements, mais possible de supprimer
            $rp[0] = "ut";
            $rp[1] = $id;
            $session->set('utilisateursPOP',$rp);
        } else {
            // Suppresion direct
            $utilisateursRepository->delete($id);
        }
        header('Location: /admin/utilisateurs');
    }

    /**
     * Suppresion en cascade de l'utilisateur (Suppresions des participations de l'utilisateur aux evénements)
     * @param UtilisateursRepository $utilisateursRepository
     * @param Session $session
     */
    #[Route(path: "/admin/delete/utilisateurs/cascade", httpMethod: 'POST', name: "admin_delete_utilisateurs_cascade")]
    public function deleteEvenementsCascade(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['idUtilisateur']);
        $utilisateursRepository->deleteCascadeUtilisateur($id);
        header('Location: /admin/utilisateurs');
    }
}