<?php

namespace App\Repository;

use App\Entity\Adresses;
use ReflectionException;

final class AdressesRepository extends AbstractRepository
{
    protected const TABLE = 'adresses';
    protected const ID = 'id_adresse';

    /**
     * Sauvegarde d'une adresse dans la base de données
     * @param Adresses $adresses
     * @return bool
     */
    public function save(Adresses $adresses): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO adresses (`libelle_adresse`, coordonnee_longitude, coordonne_latitude, `ville_libelle`, cp_ville)
                                            VALUES (:libelleAdresse, :coordonneeLongitude, :coordonneLatitude, :villeLibelle, :cpVille)");

        return $stmt->execute([
            'libelleAdresse' => $adresses->getLibelleAdresse(),
            'coordonneeLongitude' => $adresses->getCoordonneeLongitude(),
            'coordonneLatitude' => $adresses->getCoordonneLatitude(),
            'villeLibelle' => $adresses->getVilleLibelle(),
            'cpVille' => $adresses->getCpVille(),
        ]);
    }

    /**
     * Met à jour une adresse dans la base de données
     * @param Adresses $adresses
     * @return bool
     */
    public function update(Adresses $adresses): bool
    {
        $stmt = $this->pdo->prepare("UPDATE adresses SET 
                        `libelle_adresse` = :libelleAdresse,
                        `coordonnee_longitude` = :coordonneeLongitude,
                        `coordonne_latitude` = :coordonneLatitude,
                        `ville_libelle` = :villeLibelle,
                        `cp_ville` = :cpVille
                        WHERE `id_adresse` = :idAdresse");

        return $stmt->execute([
            'libelleAdresse' => $adresses->getLibelleAdresse(),
            'coordonneeLongitude' => $adresses->getCoordonneeLongitude(),
            'coordonneLatitude' => $adresses->getCoordonneLatitude(),
            'villeLibelle' => $adresses->getVilleLibelle(),
            'cpVille' => $adresses->getCpVille(),
            'idAdresse' => $adresses->getIdAdresse(),
        ]);
    }


    /**
     * Vérifie les contraintes d'une adresse via son id
     * @param int $id
     * @return array|null
     * @throws ReflectionException
     */
    public function verifContraintsAdresse(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM evenements WHERE id_adresse = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll();
        if ($results) {
            return $this->setHydrate($results);
        }
        return null;
    }
}
