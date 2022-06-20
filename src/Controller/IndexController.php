<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\UtilisateursRepository;
use App\Routing\Attribute\Route;
use DateTime;

class IndexController extends AbstractController
{
  #[Route(path: "/")]
  public function index(UtilisateursRepository $utilisateursRepository)
  {
    var_dump($utilisateursRepository->selectOneById(281));
  }

  #[Route(path: "/contact", name: "contact", httpMethod: "POST")]
  public function contact()
  {
    echo $this->twig->render('index/contact.html.twig');
  }
  #[Route(path: "/users", name: "users", httpMethod: "GET")]
  public function users()
  {
    $users="walid";
    echo $this->twig->render('user/list.html.twig', ['users'=> $users]);
  }
  #[Route(path: "/base", name: "base", httpMethod: "GET")]
  public function base()
  {
    echo $this->twig->render('base.html.twig');
  }
}
