<?php

namespace App\Repository;

use App\Entity\Adresses;
use App\Entity\Categories;

final class CategoriesRepository extends AbstractRepository
{
    protected const TABLE = 'categories';
    protected const ID = 'id_categorie';


    
     /**
     * Sauvegarde une catégorie dans la base de données
     * @param $categories 
     * @return bool
     */
    public function save(Categories $categories): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO categories (`libelle_categorie`)
                                            VALUES (:libelleCategorie)");

        return $stmt->execute([
            'libelleCategorie' => $categories->getLibelleCategorie(),
        ]);
    }
    
     /**
     * Met à jour une catégorie dans la base de données
     * @param $categorie 
     * @return bool
     */
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

    /**
     * Vérifie les contraintes d'une catégorie via son id
     * @param $id 
     * @return array
     */
    public function verifContraintsEvenementCategories(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM evenements as e WHERE e.id_categorie = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll();
        if ($results) {
            return $this->setHydrate($results);
        }
        return null;
    }
}
