<?php

namespace App\Repository;

use App\Entity\Participe;

final class ParticipeRepository extends AbstractRepository
{
    protected const TABLE = 'participe';
    protected const ID = 'id';

    public function save(Participe $participe): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO participe (`id_utilisateur`, id_evenement)
                                            VALUES (:idUtilisateur, :idEvenement)");

        return $stmt->execute([
            'idUtilisateur' => $participe->getIdUtilisateur(),
            'idEvenement' => $participe->getIdEvenement(),
        ]);
    }

    /**
     * Suppresion d'un client dans un evenement
     */
    public function deleteUtilisateur(int $idUtilisateur, int $idEvenement): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id_utilisateur =:idUtilisateur AND id_evenement =:idEvenement");
        return $statement->execute([
            'idUtilisateur' => $idUtilisateur,
            'idEvenement' => $idEvenement,
        ]);
    }
}
