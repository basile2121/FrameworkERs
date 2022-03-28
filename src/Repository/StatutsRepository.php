<?php

namespace App\Repository;

use App\Entity\Statuts;

final class StatutsRepository extends AbstractRepository
{
    protected const TABLE = 'statuts';
    protected const ID = 'id_statut';

    public function save(Statuts $statuts): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO statuts (`libelle_statut`, couleur_status)
                                            VALUES (:libelleStatut, :couleurStatus)");

        return $stmt->execute([
            'libelleStatut' => $statuts->getLibelleStatut(),
            'couleurStatus' => $statuts->getCouleurStatus(),
        ]);
    }
}
