<?php

namespace App\Controller;

use DateTime;
use App\Session\Session;
use ReflectionException;
use App\Entity\Promotions;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Error\RuntimeError;
use App\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\RolesRepository;
use App\Repository\EcolesRepository;
use App\Repository\EvenementsRepository;
use App\Repository\PromotionsRepository;
use App\Repository\UtilisateursRepository;

class UtilisateurController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/utilisateurs", name: "admin_utilisateurs",)]
    public function utilisateurs(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository , Session $session)
    {
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

        $session->delete('utilisateursPOP');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws ReflectionException
     */
    #[Route(path: "/admin/utilisateurs/filter", httpMethod: 'POST', name: "admin_utilisateurs_filter",)]
    public function utilisateursFilter(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository)
    {
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();
        $roles = $rolesRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];

        if (!empty($_POST['filtre_lastName'])) {
            $filtres['filtre_lastName'] = $_POST['filtre_lastName'];
            $conditions[] = 'nom LIKE ?';
            $parameters[] = '%'.$_POST['filtre_lastName']."%";
        }

        if (!empty($_POST['filtre_firstName'])) {
            $filtres['filtre_firstName'] = $_POST['filtre_firstName'];
            $conditions[] = 'prenom LIKE ?';
            $parameters[] = '%'.$_POST['filtre_firstName']."%";
        }

        if (!empty($_POST['filtre_role'])) {
            $filtres['filtre_role'] = intval($_POST['filtre_role']);
            $conditions[] = 'id_role = ?';
            $parameters[] = intval($_POST['filtre_role']);
        }

        if (!empty($_POST['filtre_promotion'])) {
            $filtres['filtre_promotion'] = intval($_POST['filtre_promotion']);
            $conditions[] = 'id_promotion = ?';
            $parameters[] = intval($_POST['filtre_promotion']);
        }

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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/utilisateurs", httpMethod: 'POST', name: "admin_edit_utilisateurs",)]
    public function editUtilisateurs(PromotionsRepository $promotionsRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository)
    {
        $id = intval($_POST['id']);
        $utilisateur = $utilisateursRepository->selectOneById($id);
        $promotions = $promotionsRepository->selectAll();
        $roles = $rolesRepository->selectAll();

        echo $this->twig->render('admin/utilisateurs/admin_form_edit_utilisateur.html.twig', [
            'utilisateur' => $utilisateur,
            'promotions' => $promotions,
            'roles' => $roles,
        ]);
    }


    /**
     * @throws \Exception
     */
    #[Route(path: "/admin/update/utilisateurs", httpMethod: 'POST', name: "admin_update_utilisateurs",)]
    public function updateUtilisateurs(UtilisateursRepository $utilisateursRepository)
    {
        $user = $utilisateursRepository->selectOneById(intval($_POST['id']));

        $user->setNom($_POST['nom']);
        $user->setPrenom($_POST['prenom']);
        $user->setDateNaissance(new DateTime($_POST['dateNaissance']));
        $user->setMail($_POST['mail']);
        $user->setTelephone($_POST['telephone']);
        $user->setIdPromotion(intval($_POST['promotion']));
        $user->setIdRole(intval($_POST['role']));

        $utilisateursRepository->update($user);

        header('Location: /admin/utilisateurs');
    }


    #[Route(path: "/admin/delete/utilisateurs", httpMethod: 'POST', name: "admin_delete_utilisateurs")]
    public function deleteUtilisateurs(UtilisateursRepository $utilisateursRepository , Session $session)
    {
        $id = intval($_POST['idUtilisateur']);

        $evenementsCreatedByUser = $utilisateursRepository->verifContraintsEvenementCreate($id);
        $evenementsParticipeByUser = $utilisateursRepository->verifContraintsParticipeEvenement($id);
        if ($evenementsCreatedByUser !== null) {
            // TODO POP UP
            // Message pop-up Impossible de supprimer l'utilisateur car il a créer un  supprime pas
          
            $rp[0]="utilisateurs" ; $rp[1]='null';
            $session->set('utilisateursPOP',$rp);
            header('Location: /admin/utilisateurs');
            
        } else if ($evenementsParticipeByUser !== null) {
            // TODO POP UP
            // Message pop-up Impossible de supprimer l'utilisateur car il participe à un evenement supprime
          
            $rp[0]="ut";$rp[1]=$id;
            $session->set('utilisateursPOP',$rp);
            header('Location: /admin/utilisateurs');
        } else {
            $utilisateursRepository->delete($id);
            header('Location: /admin/utilisateurs');

        }
    }

    #[Route(path: "/admin/delete/utilisateurs/cascade", httpMethod: 'POST', name: "admin_delete_utilisateurs_cascade")]
    public function deleteEvenementsCascade(UtilisateursRepository $utilisateursRepository)
    {
        $id = intval($_POST['idUtilisateur']);
        $utilisateursRepository->deleteCascadeUtilisateur($id);
        header('Location: /admin/utilisateurs');
    }

    /**
     * Affiche profil utilisateur
     *
     * @param UtilisateursRepository $utilisateursRepository
     * @return void
     */
    #[Route(path: "/utilisateurs/profil", name: "profil_utilisateurs")]
    public function profilUtilisateur(UtilisateursRepository $utilisateursRepository, EcolesRepository $ecolesRepository, PromotionsRepository $promotionsRepository, Session $session)
    {
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
        $session->delete('successUpdate');
    }

    /**
     * Affiche page edition utilisateur
     *
     * @param UtilisateursRepository $utilisateursRepository
     * @return void
     */
    #[Route(path: "/utilisateurs/profil/edit", name: "edit_profil_utilisateurs")]
    public function editProfilUtilisateur(UtilisateursRepository $utilisateursRepository, EcolesRepository $ecolesRepository, PromotionsRepository $promotionsRepository)
    {
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
     * Edit les informations du profil
     *
     * @return void
     */
    #[Route(path: '/utilisateurs/profil/edit',httpMethod:"POST", name: "edit_user")]
    public function updateProfilUtilisateur(UtilisateursRepository $utilisateursRepository, RolesRepository $rolesRepository, Session $session, PromotionsRepository $promotionsRepository)
    {
               
                $id = $_SESSION['id'];
                $user = $utilisateursRepository->selectOneById($id);
                $user->setNom(trim($_POST["nom"]));
                $user->setPrenom(trim($_POST["prenom"]));
                $user->setDateNaissance(new DateTime($_POST['date']));
                $user->setIdPromotion(intval($_POST["promotions"]));
                $user->setTelephone(trim($_POST["telephone"]));
                $user->setMail(trim($_POST["email"]));
    
                $utilisateursRepository->update($user);
                $session->set('successUpdate', 'Vos informations ont bien été modifiées !');
                header("Location: http://localhost:8000/utilisateurs/profil");
    }
}