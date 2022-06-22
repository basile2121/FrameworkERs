<?php

namespace App\Controller;

use App\Repository\EcolesRepository;
use App\Repository\EvenementsRepository;
use App\Repository\PromotionsRepository;
use App\Repository\RolesRepository;
use App\Repository\UserRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use DateTime;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UtilisateurController extends AbstractController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route(path: "/admin/utilisateurs", name: "admin_utilisateurs",)]
    public function utilisateurs(PromotionsRepository $promotionsRepository,EcolesRepository $ecolesRepository, RolesRepository $rolesRepository, UtilisateursRepository $utilisateursRepository)
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
        ]);
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
    public function deleteUtilisateurs(UtilisateursRepository $utilisateursRepository)
    {
        $id = intval($_POST['id']);

        $evenementsCreatedByUser = $utilisateursRepository->verifContraintsEvenementCreate($id);
        $evenementsParticipeByUser = $utilisateursRepository->verifContraintsParticipeEvenement($id);
        if ($evenementsCreatedByUser !== null) {
            // TODO POP UP
            // Message pop-up Impossible de supprimer l'utilisateur car il a créer un evenement
        } else if ($evenementsParticipeByUser !== null) {
            // TODO POP UP
            // Message pop-up Impossible de supprimer l'utilisateur car il participe à un evenement
        } else {
            $utilisateursRepository->delete($id);
            header('Location: /admin/utilisateurs');

        }
    }

    #[Route(path: "/admin/delete/utilisateurs/cascade", httpMethod: 'POST', name: "admin_delete_utilisateurs_cascade")]
    public function deleteEvenementsCascade(UtilisateursRepository $utilisateursRepository)
    {
        $id = intval($_POST['id']);
        $utilisateursRepository->deleteCascadeUtilisateur($id);
        header('Location: /admin/utilisateurs');
    }
}