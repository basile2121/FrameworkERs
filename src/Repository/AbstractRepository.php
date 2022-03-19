<?php

namespace App\Repository;

use PDO;

abstract class AbstractRepository
{
  protected PDO $pdo;
  protected const TABLE = '';

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

    /**
     * Recuperation de toutes les données.
     */
    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = 'SELECT * FROM ' . static::TABLE;
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Recuperation d'un seul element via son id
     */
    public function selectOneById(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch();

        return ($results !== false) ? $results : [];
    }

    /**
     * Suppresion d'un element via son id
     */
    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }


}
