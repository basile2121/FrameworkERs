<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use DateTime;
use App\Session\Session;
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
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/utilisateurs", name: "admin_utilisateurs",)]
    public function utilisateurs(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository , Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
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
    #[Route(path: "/admin/utilisateurs/filter", httpMethod: 'GET', name: "admin_utilisateurs_filter",)]
    public function utilisateursFilter(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository,Session $session, Request $request)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();
        $roles = $rolesRepository->selectAll();

        $conditions = [];
        $parameters = [];
        $filtres = [];

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
    #[Route(path: "/admin/create/utilisateurs", name: "admin_create_utilisateurs",)]
    public function createUtilisateurs(PromotionsRepository $promotionsRepository, RolesRepository $rolesRepository,Session $session, UtilisateursRepository $utilisateursRepository, EcolesRepository $ecolesRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
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
     * @throws \Exception
     */
    #[Route(path: "/admin/add/utilisateurs", httpMethod: 'POST', name: "admin_add_utilisateurs",)]
    public function addUtilisateurs(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository,Session $session, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $authController = new AuthentificationController($this->twig);
        $verifRegister = $authController->_verifRegister($utilisateursRepository);

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
            $utilisateursRepository->save($user);

            header("Location: /admin/utilisateurs");
        }
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/edit/utilisateurs", httpMethod: 'GET', name: "admin_edit_utilisateurs",)]
    public function editUtilisateurs(PromotionsRepository $promotionsRepository, RolesRepository $rolesRepository,Session $session, UtilisateursRepository $utilisateursRepository, Request $request)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = $request->query->get('id');
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
    public function updateUtilisateurs(Session $session, UtilisateursRepository $utilisateursRepository)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
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


    /**
     * @throws ReflectionException
     */
    #[Route(path: "/admin/delete/utilisateurs", httpMethod: 'POST', name: "admin_delete_utilisateurs")]
    public function deleteUtilisateurs(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
        $id = intval($_POST['idUtilisateur']);

        $evenementsCreatedByUser = $utilisateursRepository->verifContraintsEvenementCreate($id);
        $evenementsParticipeByUser = $utilisateursRepository->verifContraintsParticipeEvenement($id);
        if ($evenementsCreatedByUser !== null) {
            $rp[0]="utilisateurs" ; $rp[1]='null';
            $session->set('utilisateursPOP',$rp);
        } else if ($evenementsParticipeByUser !== null) {
            $rp[0]="ut";$rp[1]=$id;
            $session->set('utilisateursPOP',$rp);
        } else {
            $utilisateursRepository->delete($id);
        }
        header('Location: /admin/utilisateurs');
    }

    #[Route(path: "/admin/delete/utilisateurs/cascade", httpMethod: 'POST', name: "admin_delete_utilisateurs_cascade")]
    public function deleteEvenementsCascade(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        $this->renderDeniedAcces($session, $utilisateursRepository, 'ADMIN');
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
     * @throws \Exception
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