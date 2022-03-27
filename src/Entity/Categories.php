<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Categories
{
    #[EntityParameter('idCategorie', 'int' , 'id_categorie')]
    private int $idCategorie;

    #[EntityParameter('libelleCategorie', 'string', 'libelle_categorie')]
    private string $libelleCategorie;

    /**
     * @return int
     */
    public function getIdCategorie(): int
    {
        return $this->idCategorie;
    }

    /**
     * @param int $idCategorie
     */
    public function setIdCategorie(int $idCategorie): void
    {
        $this->idCategorie = $idCategorie;
    }

    /**
     * @return string
     */
    public function getLibelleCategorie(): string
    {
        return $this->libelleCategorie;
    }

    /**
     * @param string $libelleCategorie
     */
    public function setLibelleCategorie(string $libelleCategorie): void
    {
        $this->libelleCategorie = $libelleCategorie;
    }
}
