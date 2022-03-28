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
}
