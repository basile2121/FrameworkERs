<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Roles
{
    #[EntityParameter('idRole', 'int' , 'id_role')]
    private int $idRole;

    #[EntityParameter('libelleRole', 'string', 'libelle_role')]
    private string $libelleRole;

    /**
     * @return int
     */
    public function getIdRole(): int
    {
        return $this->idRole;
    }

    /**
     * @param int $idRole
     */
    public function setIdRole(int $idRole): void
    {
        $this->idRole = $idRole;
    }

    /**
     * @return string
     */
    public function getLibelleRole(): string
    {
        return $this->libelleRole;
    }

    /**
     * @param string $libelleRole
     */
    public function setLibelleRole(string $libelleRole): void
    {
        $this->libelleRole = $libelleRole;
    }
}
