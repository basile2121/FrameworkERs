<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Promotions
{
    #[EntityParameter('idPromotion', 'int', 'id_promotion')]
    private int $idPromotion;

    #[EntityParameter('libellePromotion', 'string', 'libelle_promotion')]
    private string $libellePromotion;

    #[EntityParameter('idEcole', 'int', 'id_ecole')]
    private int $idEcole;

    #[EntityParameter('ecoles', 'Object', 'id_ecole', 'App\Entity\Ecoles', 'App\Repository\EcolesRepository')]
    private Ecoles $ecoles;

    /**
     * @return int
     */
    public function getIdPromotion(): int
    {
        return $this->idPromotion;
    }

    /**
     * @param int $idPromotion
     */
    public function setIdPromotion(int $idPromotion): void
    {
        $this->idPromotion = $idPromotion;
    }

    /**
     * @return string
     */
    public function getLibellePromotion(): string
    {
        return $this->libellePromotion;
    }

    /**
     * @param string $libellePromotion
     */
    public function setLibellePromotion(string $libellePromotion): void
    {
        $this->libellePromotion = $libellePromotion;
    }

    /**
     * @return int
     */
    public function getIdEcole(): int
    {
        return $this->idEcole;
    }

    /**
     * @param int $idEcole
     */
    public function setIdEcole(int $idEcole): void
    {
        $this->idEcole = $idEcole;
    }

    /**
     * @return Ecoles
     */
    public function getEcoles(): Ecoles
    {
        return $this->ecoles;
    }

    /**
     * @param Ecoles $ecoles
     */
    public function setEcoles(Ecoles $ecoles): void
    {
        $this->ecoles = $ecoles;
    }
}

