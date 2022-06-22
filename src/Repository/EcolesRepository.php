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
        $stmt = $this->pdo->prepare("UPDATE ecoles SET 
                        `nom_ecole` = :nomEcole
                        WHERE `id_ecole` = :idEcole");
        return $stmt->execute([
            'idEcole' => $ecoles->getIdEcole(),
            'nomEcole' => $ecoles->getNomEcole()
        ]);
    }

    public function verifContraintsPromotions(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM promotions as p WHERE p.id_ecole = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll();
        if ($results) {
            return $this->setHydrate($results);
        }
        return null;
    }
}
