<?php

namespace App\Repository;

use App\Utils\Hydrator;
use PDO;
use ReflectionException;

abstract class AbstractRepository
{
  protected PDO $pdo;
  protected Hydrator $hydrator;
  protected const TABLE = '';
  protected const ID = '';

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
    public function selectOneById(int $id): ?object
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE ". static::ID . " = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetch();
        if ($results) {
            return $this->setHydrateOne($results);
        }
        return null;
    }

    /**
     * Suppresion d'un element via son id
     */
    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE ". static::ID . " =:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * Suppresion d'un element via son id
     */
    public function deleteAll(): void
    {
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE);
        $statement->execute();
    }

    /**
     * Filtre en fonction des conditions et parameteres passés
     * @throws ReflectionException
     */
    public function filter(array $conditions, array $parameters, string $additionalQuery = '', string $orderBy = '', string $direction = 'ASC', string $limit = ''): array
    {
        $query = 'SELECT * FROM ' . static::TABLE;
        $query .= ' ' . $additionalQuery;
        if(!empty($parameters) && !empty($conditions)) {
            $query .= " WHERE ".implode(" AND ", $conditions);
        }
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }
        if ($limit) {
            $query .= ' ' . $limit;
        }
        $stmt = $this->pdo->prepare($query);
        
        $stmt->execute($parameters);
        $data = $stmt->fetchAll();
        return $this->setHydrate($data);
    }

    /**
     * Hydradation pour les findAll
     * Le tableau est un tableau contenant un tableau associatif de clef valeur ou les clef sont les attributs sql
     * @throws ReflectionException
     */
    public function setHydrate(array $data): array
    {
        $classStrName = "App\\Entity\\" . ucfirst(static::TABLE);
        return $this->hydrator->hydrateAll($data, $classStrName);
    }

    /**
     * Hydratation pour les findOne
     * Le tableau est un tableau associatif de clef valeur ou les clef sont les attributs sql
     * @param array $data
     * @return Object
     * @throws ReflectionException
     */
    public function setHydrateOne(array $data): Object
    {
        $classStrName = "App\\Entity\\" . ucfirst(static::TABLE);
        $model =  new $classStrName();
        $parameters = $this->hydrator->getParameters($model);
        return $this->hydrator->hydrate($data, $model,$parameters );
    }
}
