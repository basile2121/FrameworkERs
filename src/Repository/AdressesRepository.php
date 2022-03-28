<?php

namespace App\Repository;

use App\Entity\Adresses;

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
}
