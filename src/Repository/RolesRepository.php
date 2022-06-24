<?php

namespace App\Repository;

use App\Entity\Roles;
use ReflectionException;

final class RolesRepository extends AbstractRepository
{
    protected const TABLE = 'roles';
    protected const ID = 'id_role';

    /**
     * Sauvegarde d'un role dans la base de données
     * @param Roles $roles
     * @return bool
     */
    public function save(Roles $roles): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO roles (`libelle_role`)
                                            VALUES (:libelleRole)");

        return $stmt->execute([
            'libelleRole' => $roles->getLibelleRole(),
        ]);
    }

    /**
     * Recuperation de l'id_role en fonction du libellé
     * @param string $libelleRole
     * @return object|null
     * @throws ReflectionException
     */
    public function selectOneByLibelle(string $libelleRole): ?object
    {
        $statement = $this->pdo->prepare("SELECT id_role FROM " . static::TABLE . " WHERE libelle_role = :role");
        $statement->bindValue('role', $libelleRole, \PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetch();
        if ($results) {
            return $this->setHydrateOne($results);
        }
        return null;
    }
}
