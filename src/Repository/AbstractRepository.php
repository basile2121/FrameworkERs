<?php

namespace App\Repository;

use App\Entity\User;
use App\Utils\Hydrator;
use PDO;
use ReflectionException;

abstract class AbstractRepository
{
  protected PDO $pdo;
  protected Hydrator $hydrator;
  protected const TABLE = '';

  public function __construct(PDO $pdo, Hydrator $hydrator)
  {
    $this->pdo = $pdo;
    $this->hydrator = $hydrator;
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

        return $this->setHydrate($this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC));
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

    /**
     * @throws ReflectionException
     */
    public function setHydrate(array $data): array
    {
        $classStrName = "App\\Entity\\" . ucfirst(static::TABLE);
        return $this->hydrator->hydrateAll($data, $classStrName);
    }

}
