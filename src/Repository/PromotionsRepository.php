<?php

namespace App\Repository;

use App\Entity\Promotions;

final class PromotionsRepository extends AbstractRepository
{
    protected const TABLE = 'promotions';
    protected const ID = 'id_promotion';

    /**
     * Sauvegarde d'une promotion dans la base de données
     * @param Promotions $promotions
     * @return bool
     */
    public function save(Promotions $promotions): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO promotions (`libelle_promotion`, `id_ecole`)
                                            VALUES (:libellePromotion, :idEcole)");

        return $stmt->execute([
            'libellePromotion' => $promotions->getLibellePromotion(),
            'idEcole' => $promotions->getIdEcole(),
        ]);
    }

    
    /**
     * Met à jour d'une promotion dans la base de données
     * @param $promotion 
     * @return bool
     */
    public function update(Promotions $promotions): bool
    {
    
        $stmt = $this->pdo->prepare("UPDATE promotions SET 
                        `libelle_promotion` = :libellePromotion,
                        `id_ecole` = :idEcole
                        WHERE `id_promotion` = :idPromotion");

        return $stmt->execute([
            'idPromotion' => $promotions->getIdPromotion(),
            'libellePromotion' => $promotions->getLibellePromotion(),
            'idEcole' => $promotions->getIdEcole()
        ]);
    }

    /**
     * Fonction permettant de trouver une promotion via un identifiant d'une école
     * @param $idEcole 
     * @return array
     */
    public function findOneById(int $idEcole): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id_ecole = :idEcole");
        $stmt->bindValue('idEcole', $idEcole, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        return $results;

    }
    /**
     * Vérifie les contraintes d'une promotion via son id
     * @param $id 
     * @throws ReflectionException
     * @return array
     */
 
    public function verifContraintsPromotions(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE id_promotion = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll();
        if ($results) {
            return $this->setHydrate($results);
        }
        return null;
    }
}
