<?php

namespace App\Repository;

use App\Entity\Utilisateurs;
use ReflectionException;

final class UtilisateursRepository extends AbstractRepository
{
    protected const TABLE = 'utilisateurs';
    protected const ID = 'id_utilisateur';

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
     * Recuperation de toutes les données.
     * @throws ReflectionException
     */
    public function filterUtilisateur(array $conditions, array $parameters): array
    {
        $query = 'SELECT * FROM ' . static::TABLE;
        $query .= " WHERE ".implode(" AND ", $conditions);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($parameters);
        $data = $stmt->fetchAll();
        return $this->setHydrate($data);
    }

       /**
     * Recuperation d'un seul element via son email (Login)
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
}
