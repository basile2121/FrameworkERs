<?php

namespace App\Repository;

use App\Entity\Categories;

final class CategoriesRepository extends AbstractRepository
{
    protected const TABLE = 'categories';
    protected const ID = 'id_categorie';

    public function save(Categories $categories): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (`libelle_categorie`)
                                            VALUES (:libelleCategorie)");

        return $stmt->execute([
            'libelleCategorie' => $categories->getLibelleCategorie(),
        ]);
    }
}
