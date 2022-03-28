<?php

namespace App\Repository;

use App\Entity\Appartient;

final class AppartientRepository extends AbstractRepository
{
    protected const TABLE = 'appartient';
    protected const ID = 'id';

    public function save(Appartient $appartient): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO appartient (`id_evenement`, id_categorie)
                                            VALUES (:idEvenement, :idCategorie)");

        return $stmt->execute([
            'idEvenement' => $appartient->getIdEvenement(),
            'idCategorie' => $appartient->getIdCategorie(),
        ]);
    }
}
