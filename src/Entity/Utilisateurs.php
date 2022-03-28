<?php

namespace App\Entity;

use App\Utils\Attribute\EntityParameter;
use DateTime;

class Utilisateurs
{
    #[EntityParameter('idUtilisateur', 'int' , 'id_utilisateur')]
    private int $idUtilisateur;

    #[EntityParameter('nom', 'string', 'nom')]
    private string $nom;

    #[EntityParameter('prenom', 'string', 'prenom')]
    private string $prenom;

    #[EntityParameter('dateNaissance', 'DateTime', 'date_naissance')]
    private DateTime $dateNaissance;

    #[EntityParameter('dateInscription', 'DateTime' , 'date_inscription')]
    private DateTime $dateInscription;

    #[EntityParameter('mail', 'string', 'mail')]
    private string $mail;

    #[EntityParameter('telephone', 'string', 'telephone')]
    private string $telephone;

    #[EntityParameter('password', 'string', 'password')]
    private string $password;

    #[EntityParameter('idPromotion', 'int', 'id_promotion')]
    private int $idPromotion;

    #[EntityParameter('idRole', 'int', 'id_role')]
    private int $idRole;

    #[EntityParameter('promotions', 'Object', 'id_promotion', 'App\Entity\Promotions', 'App\\Repository\\PromotionsRepository')]
    private Promotions $promotions;

    #[EntityParameter('roles', 'Object', 'id_role', 'App\Entity\Roles', 'App\\Repository\\RolesRepository')]
    private Roles $roles;

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
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return DateTime
     */
    public function getDateNaissance(): DateTime
    {
        return $this->dateNaissance;
    }

    /**
     * @param DateTime $dateNaissance
     */
    public function setDateNaissance(DateTime $dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }

    /**
     * @return DateTime
     */
    public function getDateInscription(): DateTime
    {
        return $this->dateInscription;
    }

    /**
     * @param DateTime $dateInscription
     */
    public function setDateInscription(DateTime $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return string
     */
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

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
     * @return Promotions
     */
    public function getPromotions(): Promotions
    {
        return $this->promotions;
    }

    /**
     * @param Promotions $promotions
     */
    public function setPromotions(Promotions $promotions): void
    {
        $this->promotions = $promotions;
    }

    /**
     * @return Roles
     */
    public function getRoles(): Roles
    {
        return $this->roles;
    }

    /**
     * @param Roles $roles
     */
    public function setRoles(Roles $roles): void
    {
        $this->roles = $roles;
    }

}
