<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Participe
{
    #[EntityParameter('id', 'int' , 'id')]
    private int $id;

    #[EntityParameter('idUtilisateur', 'int', 'id_utilisateur')]
    private int $idUtilisateur;

    #[EntityParameter('idEvenement', 'int', 'id_evenement')]
    private int $idEvenement;

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
    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    /**
     * @param int $idUtilisateur
     */
    public function setIdUtilisateur(int $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
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
}
