<?php

namespace App\Repository;

use App\Entity\Ecoles;

final class EcolesRepository extends AbstractRepository
{
    protected const TABLE = 'ecoles';
    protected const ID = 'id_ecole';

    public function save(Ecoles $ecoles): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO ecoles (`nom_ecole`)
                                            VALUES (:nomEcole)");

        return $stmt->execute([
            'nomEcole' => $ecoles->getNomEcole(),
        ]);
    }
}
