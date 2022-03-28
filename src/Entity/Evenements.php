<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Evenements
{
    #[EntityParameter('idEvenement', 'int' , 'id_evenement')]
    private int $idEvenement;

    #[EntityParameter('titre', 'string', 'titre')]
    private string $titre;

    #[EntityParameter('sousTitre', 'string', 'sous_titre')]
    private string $sousTitre;

    #[EntityParameter('description', 'string', 'description')]
    private string $description;

    #[EntityParameter('nbParticipantsMax', 'int' , 'nb_participants_max')]
    private int $nbParticipantsMax;

    #[EntityParameter('prix', 'float', 'prix')]
    private float $prix;

    #[EntityParameter('date', 'DateTime', 'date')]
    private DateTime $date;

    #[EntityParameter('createdAt', 'DateTime', 'created_at')]
    private DateTime $createdAt;

    #[EntityParameter('updatedAt', 'DateTime', 'updated_at')]
    private DateTime $updatedAt;

    #[EntityParameter('idUtilisateur', 'int', 'id_utilisateur')]
    private int $idUtilisateur;

    #[EntityParameter('idAdresse', 'int', 'id_adresse')]
    private int $idAdresse;

    #[EntityParameter('idMedia', 'int', 'id_media')]
    private int $idMedia;

    #[EntityParameter('idCategorie', 'int', 'id_categorie')]
    private int $idCategorie;

    #[EntityParameter('idStatut', 'int', 'id_statut')]
    private int $idStatut;

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
     * @return string
     */
    public function getTitre(): string
    {
        return $this->titre;
    }

    /**
     * @param string $titre
     */
    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    /**
     * @return string
     */
    public function getSousTitre(): string
    {
        return $this->sousTitre;
    }

    /**
     * @param string $sousTitre
     */
    public function setSousTitre(string $sousTitre): void
    {
        $this->sousTitre = $sousTitre;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getNbParticipantsMax(): int
    {
        return $this->nbParticipantsMax;
    }

    /**
     * @param int $nbParticipantsMax
     */
    public function setNbParticipantsMax(int $nbParticipantsMax): void
    {
        $this->nbParticipantsMax = $nbParticipantsMax;
    }

    /**
     * @return float
     */
    public function getPrix(): float
    {
        return $this->prix;
    }

    /**
     * @param float $prix
     */
    public function setPrix(float $prix): void
    {
        $this->prix = $prix;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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
     * @return int
     */
    public function getIdMedia(): int
    {
        return $this->idMedia;
    }

    /**
     * @param int $idMedia
     */
    public function setIdMedia(int $idMedia): void
    {
        $this->idMedia = $idMedia;
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

}
