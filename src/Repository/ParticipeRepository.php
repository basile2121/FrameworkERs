<?php

namespace App\Repository;

use App\Entity\Participe;

final class ParticipeRepository extends AbstractRepository
{
    protected const TABLE = 'participe';

    public function save(Participe $participe): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO participe (`id_utilisateur`, id_evenement)
                                            VALUES (:idUtilisateur, :idEvenement)");

        return $stmt->execute([
            'idUtilisateur' => $participe->getIdUtilisateur(),
            'idEvenement' => $participe->getIdEvenement(),
        ]);
    }
}
