<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Ecoles
{
    #[EntityParameter('idEcole', 'int' , 'id_ecole')]
    private int $idEcole;

    #[EntityParameter('nomEcole', 'string', 'nom_ecole')]
    private string $nomEcole;

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
     * @return string
     */
    public function getNomEcole(): string
    {
        return $this->nomEcole;
    }

    /**
     * @param string $nomEcole
     */
    public function setNomEcole(string $nomEcole): void
    {
        $this->nomEcole = $nomEcole;
    }
}
