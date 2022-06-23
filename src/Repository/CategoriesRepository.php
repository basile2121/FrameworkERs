<?php

namespace App\Repository;

use App\Entity\Adresses;
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

    public function update(Categories $categorie): bool
    {
        $stmt = $this->pdo->prepare("UPDATE categories SET 
                        `libelle_categorie` = :libelleCategorie
                        WHERE `id_categorie` = :idCategorie");
        return $stmt->execute([
            'libelleCategorie' => $categorie->getLibelleCategorie(),
            'idCategorie' => 187
        ]);
    }
}
