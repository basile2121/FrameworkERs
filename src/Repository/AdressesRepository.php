<?php

namespace App\Repository;

use App\Entity\Adresses;
use App\Entity\Evenements;

final class AdressesRepository extends AbstractRepository
{
    protected const TABLE = 'adresses';
    protected const ID = 'id_adresse';

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

    public function update(Adresses $adresses): bool
    {
        $stmt = $this->pdo->prepare("UPDATE adresses SET 
                        `libelle_adresse` = :libelleAdresse,
                        `coordonnee_longitude` = :coordonneeLongitude,
                        `coordonne_latitude` = :coordonneLatitude,
                        `ville_libelle` = :villeLibelle,
                        `cp_ville` = :cpVille
                        WHERE `id_adresse` = :idAdresse" );

        return $stmt->execute([
            'libelleAdresse' => $adresses->getLibelleAdresse(),
            'coordonneeLongitude' => $adresses->getCoordonneeLongitude(),
            'coordonneLatitude' => $adresses->getCoordonneLatitude(),
            'villeLibelle' => $adresses->getVilleLibelle(),
            'cpVille' => $adresses->getCpVille(),
            'idAdresse' => $adresses->getIdAdresse(),
        ]);
    }
}
