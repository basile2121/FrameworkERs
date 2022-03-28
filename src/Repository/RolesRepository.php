<?php

namespace App\Repository;

use App\Entity\Roles;

final class RolesRepository extends AbstractRepository
{
    protected const TABLE = 'roles';
    protected const ID = 'id_role';

    public function save(Roles $roles): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO roles (`libelle_role`)
                                            VALUES (:libelleRole)");

        return $stmt->execute([
            'libelleRole' => $roles->getLibelleRole(),
        ]);
    }
}
