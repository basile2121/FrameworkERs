<?php

namespace App\Repository;

use App\Entity\Utilisateurs;
use ReflectionException;

final class UtilisateursRepository extends AbstractRepository
{
    protected const TABLE = 'utilisateurs';
    protected const ID = 'id_utilisateur';

    /**
     * Sauvegarde un utilisateur dans la base de données
     * @param Utilisateurs $utilisateurs
     * @return bool
     */
    public function save(Utilisateurs $utilisateurs): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (`nom`, prenom, date_naissance, `date_inscription`, mail, telephone, `password`, id_promotion, id_role)
                                            VALUES (:nom, :prenom, :dateNaissance, :dateInscription, :mail, :telephone, :password, :idPromotion, :idRole)");

        return $stmt->execute([
            'nom' => $utilisateurs->getNom(),
            'prenom' => $utilisateurs->getPrenom(),
            'dateNaissance' => $utilisateurs->getDateNaissance()->format('Y-m-d'),
            'dateInscription' => $utilisateurs->getDateInscription()->format('Y-m-d'),
            'mail' => $utilisateurs->getMail(),
            'telephone' => $utilisateurs->getTelephone(),
            'password' => password_hash($utilisateurs->getPassword(), PASSWORD_BCRYPT),
            'idPromotion' => $utilisateurs->getIdPromotion(),
            'idRole' => $utilisateurs->getIdRole(),
        ]);
    }

    /**
     * Met à jour un utilisateur dans la base de données
     * @param Utilisateurs $utilisateurs
     * @return bool
     */
    public function update(Utilisateurs $utilisateurs): bool
    {
        $stmt = $this->pdo->prepare("UPDATE utilisateurs SET 
                        `nom` = :nom,
                        `prenom` = :prenom,
                        `date_naissance` = :dateNaissance,
                        `mail` = :mail,
                        `telephone` = :telephone,
                        `id_promotion` = :idPromotion,
                        `id_role` = :idRole 
                        WHERE `id_utilisateur` = :idUtilisateur" );

        return $stmt->execute([
            'nom' => $utilisateurs->getNom(),
            'prenom' => $utilisateurs->getPrenom(),
            'dateNaissance' => $utilisateurs->getDateNaissance()->format('Y-m-d'),
            'mail' => $utilisateurs->getMail(),
            'telephone' => $utilisateurs->getTelephone(),
            'idPromotion' => $utilisateurs->getIdPromotion(),
            'idRole' => $utilisateurs->getIdRole(),
            'idUtilisateur' => $utilisateurs->getIdUtilisateur()
        ]);
    }

    /**
     * Recuperation d'un seul element via son email (Login)
     * @param string $mail
     * @return object|null
     * @throws ReflectionException
     */
    public function selectOneByEmail(string $mail): ?object
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE mail = :mail");
        $statement->bindValue('mail', $mail, \PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch();
        if ($results) {
            return $this->setHydrateOne($results);
        }
        return null;
    }

    /**
     * @throws ReflectionException
     */
    public function verifContraintsEvenementCreate(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM utilisateurs as u JOIN evenements as e ON e.id_utilisateur = u.id_utilisateur WHERE u.id_utilisateur = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll();
        if ($results) {
            return $this->setHydrate($results);
        }
        return null;
    }

    /**
     * Vérifie les contraintes d'un utilisateur via son id
     * @param int $id
     * @return array|null
     * @throws ReflectionException
     */
    public function verifContraintsParticipeEvenement(int $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM participe as p JOIN utilisateurs as u ON p.id_utilisateur = u.id_utilisateur WHERE p.id_utilisateur = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll();
        if ($results) {
            return $this->setHydrate($results);
        }
        return null;
    }

    /**
     * Suppresion d'un utilisateur en cascade via son id
     * @param int $id
     */
    public function deleteCascadeUtilisateur(int $id): void
    {
        $statement = $this->pdo->prepare("SELECT id FROM participe as p WHERE p.id_utilisateur = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll();

        if (!empty($results)) {
            foreach ($results as $result) {
                $statement = $this->pdo->prepare("DELETE FROM participe WHERE id =:idParticipe");
                $statement->bindValue('idParticipe', $result['id'], \PDO::PARAM_INT);
                $statement->execute();
            }
        }

        $this->delete($id);
    }

}
