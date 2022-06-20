<?php

namespace App\Repository;

use App\Entity\Promotions;

final class PromotionsRepository extends AbstractRepository
{
    protected const TABLE = 'promotions';
    protected const ID = 'id_promotion';

    public function save(Promotions $promotions): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO promotions (`libelle_promotion`, `id_ecole`)
                                            VALUES (:libellePromotion, :idEcole)");

        return $stmt->execute([
            'libellePromotion' => $promotions->getLibellePromotion(),
            'idEcole' => $promotions->getIdEcole(),
        ]);
    }

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
}
