<?php

namespace App\Repository;

use App\Entity\Medias;

final class MediasRepository extends AbstractRepository
{
    protected const TABLE = 'medias';
    protected const ID = 'id_media';

    public function save(Medias $medias): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO medias (`nom`, path, type)
                                            VALUES (:nom, :path, :type)");

        return $stmt->execute([
            'nom' => $medias->getNom(),
            'path' => $medias->getPath(),
            'type' => $medias->getType(),
        ]);
    }
    public function getLastId(){
        $statement = $this->pdo->prepare("SELECT id_media FROM medias ORDER BY id_media DESC LIMIT 1");
            $statement->execute();
            $results = $statement->fetch();
            if ($results) {
              return $results['id_media'];
            } 
    }
}
