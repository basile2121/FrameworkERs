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

  protected function verifAccessRole(Session $session, UtilisateursRepository $utilisateursRepository, $roleLibelle): bool
  {
      // Si pas connecté
      if ($session->get('id') === null) {
          return false;
      }
      // Récupération du role
      $roleUser = $utilisateursRepository->selectOneById($session->get('id'))->getRoles()->getLibelleRole();

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

  protected function renderDeniedAcces(Session $session, UtilisateursRepository $utilisateursRepository, $roleLibelle) {
      if (!$this->verifAccessRole($session, $utilisateursRepository, $roleLibelle)) {
          header('Location: /');
      }
  }
}
