<?php

namespace App\Controller;

use App\Routing\Attribute\Route;

class EventController extends AbstractController
{
  #[Route(path: '/event/create', httpMethod: 'GET', name: 'event_create_form')]
  public function create()
  {
    echo $this->twig->render('event/create.html.twig');
  }

  #[Route(path: '/event/save', httpMethod: 'POST', name: 'event_save')]
  public function save()
  {
    if (!isset($_FILES['image'])) {
      echo "Erreur : pas d'image";
      return;
    }

    $image = $_FILES['image'];

    if (
      is_uploaded_file($image['tmp_name']) &&
      move_uploaded_file(
        $image['tmp_name'],
        __DIR__ . DIRECTORY_SEPARATOR . '../../public/events/' . basename($image['name'])
      )
    ) {
      echo "ok";
    } else {
      echo "Erreur lors de l'upload";
    }
  }
}
