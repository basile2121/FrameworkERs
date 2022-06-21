<?php

namespace App\Repository;

use App\Entity\Statuts;
use ReflectionException;

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

    /**
     * Récuperer évenèments dont la date est la plus proche
     * @throws ReflectionException
     */
    public function selectOneByLibelle(string $libelle): array
    {
        $query = 'SELECT * FROM statuts WHERE id_statut = $libelle';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->setHydrate($data);
    }
}
