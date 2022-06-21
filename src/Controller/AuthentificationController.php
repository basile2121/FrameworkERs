<?php

namespace App\Controller;

use DateTime;
use App\Session\Session;
use App\Entity\Promotions;
use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManager;
use App\Routing\Attribute\Route;
use App\Controller\AbstractController;
use App\Repository\EcolesRepository;
use App\Repository\PromotionsRepository;
use App\Repository\UtilisateursRepository;
use App\Repository\RolesRepository;

class AuthentificationController extends AbstractController
{

    /**
     * Affiche le formulaire de connexion
     *
     * @return void
     */
    #[Route(path: "/login")]
    public function getLogin(Session $session)
    {
        echo $this->twig->render("authentification/login.html.twig", [
            'notLogged' => $session->get('notLogged'),
            'successRegister' => $session->get('successRegister')
        ]);
        $session->delete('notLogged');
        $session->delete('successRegister');
    }

    /**
     * Permet de se connecter en vérifiant les identifiants
     * Récupère infos de l'user connecté + redirection
     * Gestion erreur mauvais identifiants 
     *
     * @param UtilisateursRepository $utilisateursRepository
     * @param Session $session
     * @return void
     */
    #[Route(path: "/login", httpMethod: "POST", name: "login")]
    public function postLogin(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $user = $utilisateursRepository->selectOneByEmail($_POST["email"]);
            
            //Si aucun résultat => Mauvais email =>  Message erreur 
            if ($user == null) {
                $session->set('errorEmail', 'Cet email est lié à aucun compte');
                echo $this->twig->render('authentification/login.html.twig', [
                    'badEmail' => $session->get('errorEmail')
                ]);
                $session->delete('errorEmail');
            } else {
                
                //si password_verify() retourne true => création session + redirection page + message succès
                
                if (password_verify($_POST['password'], $user->getPassword())) {

                    
                    $session->set('id',$user->getIdUtilisateur());
                    $session->set('success', 'Vous êtes connecté ');
                    header("Location: http://localhost:8000/");
                    exit();
                   
                }
                //si password_verify() retourne false => mauvais mdp => message erreur
                else {
                    $session->set('errorPw', 'Mauvais mot de passe');
                    echo $this->twig->render('authentification/login.html.twig', [
                        'badPassword' => $session->get('errorPw')
                    ]);
                    $session->delete('errorPw');
                }
            }
        }
    }

    /**
     * Permet de se déconnecter
     * Suppression des données de $_SESSION
     *
     * @return void
     */
    #[Route(path: '/logout', name: "logout")]
    public function logout()
    {
        session_start();
        session_destroy();
        
        header("Location: http://localhost:8000/");
        exit();
    }

    /**
     * Affiche page registration
     *
     * @return void
     */
    #[Route(path: '/register', name: "register")]
    public function register(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository)
    {
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();
        echo $this->twig->render("authentification/register.html.twig", [
            'promotions' =>$promotions,
            'ecoles' => $ecoles
        ]);
    }

        /**
     * Récupération JSON promotions en fonction de l'école sélectionnée
     *
     * @return void
     */
    #[Route(path: '/json/promotions/{id}' , httpMethod:"GET",name: "json_promotions")]
    public function ecoleJson(PromotionsRepository $promotionsRepository, int $id)
    {
      
      $result = $promotionsRepository->findOneById($id);
      
      echo json_encode($result);
    }

    

    /**
     * Affiche page registration
     *
     * @return void
     */
    #[Route(path: '/register',httpMethod:"POST", name: "addUser")]
    public function addUser(UtilisateursRepository $utilisateursRepository, RolesRepository $rolesRepository, Session $session, PromotionsRepository $promotionsRepository)
    {
        $verifRegister = $this->_verifRegister($utilisateursRepository);
        
        if ($verifRegister !== true ) {
            $promotions = $promotionsRepository->selectAll();
            echo $this->twig->render("authentification/register.html.twig", [
                'promotions' => $promotions,
                'errors' => $verifRegister
            ]);
        } else {
                $role = $rolesRepository->selectOneByLibelle('UTILISATEUR');
    
                $user = new Utilisateurs();
                $user->setNom(trim($_POST["nom"]));
                $user->setPrenom(trim($_POST["prenom"]));
                $user->setDateNaissance(new DateTime($_POST['date_naissance']));
                $user->setIdPromotion(intval($_POST["promotions"]));
                $user->setTelephone(trim($_POST["telephone"]));
                $user->setDateInscription(new DateTime());
                $user->setMail(trim($_POST["email"]));
                $user->setPassword(trim($_POST['password']));
                $user->setIdRole($role->getIdRole());
    
                $utilisateursRepository->save($user);
                $session->set('successRegister', 'Votre compte a bien été créé, veuillez vous connecter');
                header("Location: http://localhost:8000/login");
               
        }

    }
    /**
     * Vérifier si le password respecte certaines conditions
     * 
     *
     * @param [type] $password
     * @return void
     */
    private function _verifPassword($password, $passwordConfirm)
    {   

        $errors = [];
        if($password != $passwordConfirm){
            $errors[] = 'Vos mots de passe ne correspondent pas !';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit faire au moins 8 caractères !';
        }
        if (!preg_match("#[0-9]+#", $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre !";
        }
        if (!preg_match("#[a-z]+#", $password)) {
            $errors[]= "Le mot de passe doit au moins contenir une lettre !";
        }
        if (!preg_match("#[A-Z]+#", $password)) {
            $errors[]= "Le mot de passe doit contenir au moins une majuscule !";
        }
        if (!preg_match("#\W#", $password)) {
            $errors[]= "Le mot de passe doit contenir au moins un symbole !";
        }
        if (empty($errors)){
            return true;
        } else {
            return $errors;
        }
    }

    /**
     * Vérifier que les champs sont tous renseignés
     *
     * @return error 
     */
    private function _verifIfEmpty(){
        
        if(empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['date_naissance']) || empty($_POST['promotions']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['password_confirm'])){
            $error = 'Veuillez renseigner tous les champs !';
        }

        if(isset($error)){
            return $error;
        } else {
            return true;
        }
    }

    private function _verifMail($mail){
        //Vérifier formmat du mail renseigné
        if(filter_var($mail, FILTER_VALIDATE_EMAIL)){
            return true;
        } else {
            return 'Le format du mail est invalide !';
        }
    }

    private function _verifRegister(UtilisateursRepository $utilisateursRepository)
    {
        $errors = [];
        $verifPassword = $this->_verifPassword($_POST["password"], $_POST["password_confirm"]);
        if ($verifPassword !== true) {
            $errors["password"] = $verifPassword;
        }

        $verifEmpty = $this->_verifIfEmpty();
        if ($verifEmpty !== true){
            $errors["empty"] = $verifEmpty;
        }

        $verifMail = $this->_verifMail($_POST["email"]);
        if($verifMail !== true){
            $errors["mail"] = $verifMail;
        }

         //Vérifier que l'adresse ne soit pas déjà utilisée
         $emailExist = $utilisateursRepository->selectOneByEmail($_POST["email"]);
         if (isset($emailExist)) {
             $errors["mail_existant"] = "L'adresse mail est déjà utilisée !";
         }
         
         if(empty($errors)) {
             return true;
         }
        return $errors;
        
    }
}