<?php

namespace App\Repository;

use App\Entity\Promotions;

final class PromotionsRepository extends AbstractRepository
{
    protected const TABLE = 'promotions';

    public function save(Promotions $promotions): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO promotions (`libelle_promotion`, id_ecole)
                                            VALUES (:libellePromotion, :idEcole)");

        return $stmt->execute([
            'libellePromotion' => $promotions->getLibellePromotion(),
            'idEcole' => $promotions->getIdEcole(),
        ]);
    }
}
