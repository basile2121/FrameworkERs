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

    public function update(Ecoles $ecoles): bool
    {
        var_dump($ecoles);
        $stmt = $this->pdo->prepare("UPDATE ecoles SET 
                        `nom_ecole` = :nomEcole
                        WHERE `id_ecole` = :idEcole");

        return $stmt->execute([
            'idEcole' => $ecoles->getIdEcole(),
            'nomEcole' => $ecoles->getNomEcole()
        ]);
    }

    public function filterEcole(array $conditions, array $parameters): array
    {
        $query = 'SELECT * FROM ' . static::TABLE;
        $query .= " WHERE ".implode(" AND ", $conditions);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($parameters);
        $data = $stmt->fetchAll();
        return $this->setHydrate($data);
    }
}
