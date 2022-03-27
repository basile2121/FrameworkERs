<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Statuts
{
    #[EntityParameter('idStatut', 'int' , 'id_statut')]
    private int $idStatut;

    #[EntityParameter('libelleStatut', 'string', 'libelle_statut')]
    private string $libelleStatut;

    #[EntityParameter('couleurStatus', 'string', 'couleur_status')]
    private string $couleurStatus;

    /**
     * @return int
     */
    public function getIdStatut(): int
    {
        return $this->idStatut;
    }

    /**
     * @param int $idStatut
     */
    public function setIdStatut(int $idStatut): void
    {
        $this->idStatut = $idStatut;
    }

    /**
     * @return string
     */
    public function getLibelleStatut(): string
    {
        return $this->libelleStatut;
    }

    /**
     * @param string $libelleStatut
     */
    public function setLibelleStatut(string $libelleStatut): void
    {
        $this->libelleStatut = $libelleStatut;
    }

    /**
     * @return string
     */
    public function getCouleurStatus(): string
    {
        return $this->couleurStatus;
    }

    /**
     * @param string $couleurStatus
     */
    public function setCouleurStatus(string $couleurStatus): void
    {
        $this->couleurStatus = $couleurStatus;
    }
}
