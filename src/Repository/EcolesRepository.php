<?php

namespace App\Repository;

use App\Entity\Ecoles;
use ReflectionException;

final class EcolesRepository extends AbstractRepository
{
    protected const TABLE = 'ecoles';
    protected const ID = 'id_ecole';


    /**
     * Sauvegarde une école dans la base de données
     * @param Ecoles $ecoles
     * @return bool
     */
    public function save(Ecoles $ecoles): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO ecoles (`nom_ecole`)
                                            VALUES (:nomEcole)");

        return $stmt->execute([
            'nomEcole' => $ecoles->getNomEcole(),
        ]);
    }

    /**
     * Met à jour une école dans la base de données
     * @param Ecoles $ecoles
     * @return bool
     */
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

    /**
     * Vérifie les contraintes d'une école via son id
     * @param int $id
     * @return array|null
     * @throws ReflectionException
     */
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
