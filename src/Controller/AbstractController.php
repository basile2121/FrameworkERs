<?php

namespace App\Controller;

use App\Repository\UtilisateursRepository;
use App\Session\Session;
use Twig\Environment;

abstract class AbstractController
{
  protected Environment $twig;

  public function __construct(Environment $twig)
  {
    $this->twig = $twig;
  }

    /**
     * Verifier si l'utilisateur connecté posséde le droit renseigné en paramatre $roleLibelle
     * @param Session $session
     * @param UtilisateursRepository $utilisateursRepository
     * @param $roleLibelle
     * @return bool
     */
  protected function verifAccessRole(Session $session, UtilisateursRepository $utilisateursRepository, $roleLibelle): bool
  {
      // Si pas connecté pas d'accès
      if ($session->get('id') === null) {
          return false;
      }
      // Récupération du role
      $roleUser = $utilisateursRepository->selectOneById($session->get('id'))->getRoles()->getLibelleRole();

      // Vérification des accès
      if ($roleLibelle === 'SUPER_ADMIN') {
          if ($roleUser === 'SUPER_ADMIN') {
              return true;
          }
      } else if ($roleLibelle === 'ADMIN') {
          if ($roleUser === 'SUPER_ADMIN' || $roleUser === 'ADMIN') {
              return true;
          }
      } else if ($roleLibelle === 'BDE') {
          if ($roleUser === 'SUPER_ADMIN' || $roleUser === 'ADMIN' || $roleUser === 'BDE' ) {
              return true;
          }
      }
      return false;
  }

    /**
     * Retourne sur la page d'accueil si l'utilisateur ne possède pas le rôle
     * @param Session $session
     * @param UtilisateursRepository $utilisateursRepository
     * @param $roleLibelle
     */
  protected function renderDeniedAcces(Session $session, UtilisateursRepository $utilisateursRepository, $roleLibelle) {
      if (!$this->verifAccessRole($session, $utilisateursRepository, $roleLibelle)) {
          header('Location: /');
      }
  }
}
