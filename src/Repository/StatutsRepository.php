<?php

namespace App\Repository;

use App\Entity\Statuts;
use ReflectionException;

final class StatutsRepository extends AbstractRepository
{
    protected const TABLE = 'statuts';
    protected const ID = 'id_statut';

    /**
     * Sauvegarde un statut dans la base de données
     * @param Statuts $statuts
     * @return bool
     */
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
     * Récuperer un status via son libelle
     * @param string $libelle
     * @return array
     * @throws ReflectionException
     */
    public function selectOneByLibelle(string $libelle): array
    {
        $query = 'SELECT * FROM statuts WHERE libelle_statut = :libelle';
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'libelle' => $libelle,
        ]);
        $data = $stmt->fetchAll();
    
        return $this->setHydrate($data);
    }
}
