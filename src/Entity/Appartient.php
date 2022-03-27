<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Appartient
{
    #[EntityParameter('id', 'int' , 'id')]
    private int $id;

    #[EntityParameter('idEvenement', 'int', 'id_evenement')]
    private int $idEvenement;

    #[EntityParameter('idCategorie', 'int', 'id_categorie')]
    private int $idCategorie;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getIdEvenement(): int
    {
        return $this->idEvenement;
    }

    /**
     * @param int $idEvenement
     */
    public function setIdEvenement(int $idEvenement): void
    {
        $this->idEvenement = $idEvenement;
    }

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
}
