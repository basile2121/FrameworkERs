<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use App\Repository\EcolesRepository;
use App\Repository\PromotionsRepository;
use App\Repository\RolesRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use App\Session\Session;
use DateTime;
use Exception;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AuthentificationController extends AbstractController
{


    /**
     * Affiche le formulaire de connexion
     * @return void
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
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
     * @throws LoaderError
     *
     * @throws SyntaxError
     * @throws ReflectionException
     * @throws RuntimeError
     */
    #[Route(path: "/login", httpMethod: "POST", name: "login")]
    public function postLogin(UtilisateursRepository $utilisateursRepository, Session $session)
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $user = $utilisateursRepository->selectOneByEmail($_POST["email"]);

            // Si aucun résultat => Mauvais email =>  Message erreur
            if ($user == null) {
                $session->set('errorEmail', 'Cet email est lié à aucun compte');
                echo $this->twig->render('authentification/login.html.twig', [
                    'badEmail' => $session->get('errorEmail')
                ]);
                $session->delete('errorEmail');
            } else {
                // Si password_verify() retourne true => création session + redirection page + message succès
                if (password_verify($_POST['password'], $user->getPassword())) {
                    $session->set('id', $user->getIdUtilisateur());
                    $session->set('nom', $user->getNom());
                    $session->set('prenom', $user->getPrenom());
                    $session->set('success', 'Vous êtes connecté ');
                    header("Location: http://localhost:8000/");
                    exit();
                }
                // Si password_verify() retourne false => mauvais mdp => message erreur
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
     * @return void
     */
    #[Route(path: '/logout', name: "logout")]
    public function logout(): void
    {
        session_start();
        session_destroy();

        header("Location: http://localhost:8000/");
        exit();
    }

    /**
     * Affiche page registration
     * @param PromotionsRepository $promotionsRepository
     * @param EcolesRepository $ecolesRepository
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @return void
     */
    #[Route(path: '/register', name: "register")]
    public function register(PromotionsRepository $promotionsRepository, EcolesRepository $ecolesRepository)
    {
        // Récupération des ecoles et promotions pour les afficher dans les selects
        $ecoles = $ecolesRepository->selectAll();
        $promotions = $promotionsRepository->selectAll();
        echo $this->twig->render("authentification/register.html.twig", [
            'promotions' => $promotions,
            'ecoles' => $ecoles
        ]);
    }


    /**
     * Creation d'un utilisateur
     * @param UtilisateursRepository $utilisateursRepository
     * @param EcolesRepository $ecolesRepository
     * @param RolesRepository $rolesRepository
     * @param Session $session
     * @param PromotionsRepository $promotionsRepository
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    #[Route(path: '/register', httpMethod: "POST", name: "addUser")]
    public function addUser(UtilisateursRepository $utilisateursRepository, EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, Session $session, PromotionsRepository $promotionsRepository)
    {
        // Verification des données du formulaire
        $verifRegister = $this->_verifRegister($utilisateursRepository);

        // Si il y a des erreurs on redirige sur la page
        if ($verifRegister !== true) {
            $promotions = $promotionsRepository->selectAll();
            $ecoles = $ecolesRepository->selectAll();
            echo $this->twig->render("authentification/register.html.twig", [
                'promotions' => $promotions,
                'ecoles' => $ecoles,
                'errors' => $verifRegister
            ]);
        } else {
            // Role utilisateur par défaut
            $role = $rolesRepository->selectOneByLibelle('UTILISATEUR');

            // Creation de l'utilisateur
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

            // Enregistrement de l'utilisateur dans la BDD
            $utilisateursRepository->save($user);

            // Message de succes en session que l'on envoi sur la page d'accueil
            $session->set('successRegister', 'Votre compte a bien été créé, veuillez vous connecter');
            header("Location: http://localhost:8000/login");
        }

    }

    /**
     * Récupération JSON promotions en fonction de l'école sélectionnée
     * @return void
     */
    #[Route(path: '/json/promotions/{id}', httpMethod: "GET", name: "json_promotions")]
    public function ecoleJson(PromotionsRepository $promotionsRepository, int $id)
    {
        $result = $promotionsRepository->findOneById($id);
        echo json_encode($result);
    }

    /**
     * Vérifier si le password respecte certaines conditions
     * @param $password
     * @param $passwordConfirm
     * @return array|bool
     */
    private function _verifPassword($password, $passwordConfirm): bool|array
    {
        $errors = [];
        if ($password != $passwordConfirm) {
            $errors[] = 'Vos mots de passe ne correspondent pas !';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit faire au moins 8 caractères !';
        }
        if (!preg_match("#[0-9]+#", $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre !";
        }
        if (!preg_match("#[a-z]+#", $password)) {
            $errors[] = "Le mot de passe doit au moins contenir une lettre !";
        }
        if (!preg_match("#[A-Z]+#", $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule !";
        }
        if (!preg_match("#\W#", $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un symbole !";
        }
        if (empty($errors)) {
            return true;
        } else {
            return $errors;
        }
    }

    /**
     * Vérifier que les champs sont tous renseignés
     * @return bool|string
     */
    private function _verifIfEmpty(): bool|string
    {
        if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['date_naissance']) || empty($_POST['promotions']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['password_confirm'])) {
            $error = 'Veuillez renseigner tous les champs !';
        }

        return $error ?? true;
    }

    /**
     * Vérification du format mail
     * @param $mail
     * @return bool|string
     */
    private function _verifMail($mail): bool|string
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return 'Le format du mail est invalide !';
        }
    }

    /**
     * Ensemble des vérifications
     * @param UtilisateursRepository $utilisateursRepository
     * @return bool|array
     * @throws ReflectionException
     */
    public function _verifRegister(UtilisateursRepository $utilisateursRepository): bool|array
    {
        $errors = [];
        $verifPassword = $this->_verifPassword($_POST["password"], $_POST["password_confirm"]);
        if ($verifPassword !== true) {
            $errors["password"] = $verifPassword;
        }

        $verifEmpty = $this->_verifIfEmpty();
        if ($verifEmpty !== true) {
            $errors["empty"] = $verifEmpty;
        }

        $verifMail = $this->_verifMail($_POST["email"]);
        if ($verifMail !== true) {
            $errors["mail"] = $verifMail;
        }

        // Vérifier que l'adresse ne soit pas déjà utilisée
        $emailExist = $utilisateursRepository->selectOneByEmail($_POST["email"]);
        if (isset($emailExist)) {
            $errors["mail_existant"] = "L'adresse mail est déjà utilisée !";
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}