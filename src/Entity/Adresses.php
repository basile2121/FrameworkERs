<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Adresses
{
    #[EntityParameter('idAdresse', 'int' , 'id_adresse')]
    private int $idAdresse;

    #[EntityParameter('libelleAdresse', 'string', 'libelle_adresse')]
    private string $libelleAdresse;

    #[EntityParameter('coordonneeLongitude', 'float', 'coordonnee_longitude')]
    private float $coordonneeLongitude;

    #[EntityParameter('coordonneLatitude', 'float', 'coordonne_latitude')]
    private float $coordonneLatitude;

    #[EntityParameter('villeLibelle', 'string' , 'ville_libelle')]
    private string $villeLibelle;

    #[EntityParameter('cpVille', 'string', 'cp_ville')]
    private string $cpVille;

    /**
     * @return int
     */
    public function getIdAdresse(): int
    {
        return $this->idAdresse;
    }

    /**
     * @param int $idAdresse
     */
    public function setIdAdresse(int $idAdresse): void
    {
        $this->idAdresse = $idAdresse;
    }

    /**
     * @return string
     */
    public function getLibelleAdresse(): string
    {
        return $this->libelleAdresse;
    }

    /**
     * @param string $libelleAdresse
     */
    public function setLibelleAdresse(string $libelleAdresse): void
    {
        $this->libelleAdresse = $libelleAdresse;
    }

    /**
     * @return float
     */
    public function getCoordonneeLongitude(): float
    {
        return $this->coordonneeLongitude;
    }

    /**
     * @param float $coordonneeLongitude
     */
    public function setCoordonneeLongitude(float $coordonneeLongitude): void
    {
        $this->coordonneeLongitude = $coordonneeLongitude;
    }

    /**
     * @return float
     */
    public function getCoordonneLatitude(): float
    {
        return $this->coordonneLatitude;
    }

    /**
     * @param float $coordonneLatitude
     */
    public function setCoordonneLatitude(float $coordonneLatitude): void
    {
        $this->coordonneLatitude = $coordonneLatitude;
    }

    /**
     * @return string
     */
    public function getVilleLibelle(): string
    {
        return $this->villeLibelle;
    }

    /**
     * @param string $villeLibelle
     */
    public function setVilleLibelle(string $villeLibelle): void
    {
        $this->villeLibelle = $villeLibelle;
    }

    /**
     * @return string
     */
    public function getCpVille(): string
    {
        return $this->cpVille;
    }

    /**
     * @param string $cpVille
     */
    public function setCpVille(string $cpVille): void
    {
        $this->cpVille = $cpVille;
    }
}
