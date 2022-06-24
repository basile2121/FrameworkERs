<?php

namespace App\Repository;

use App\Entity\Participe;

final class ParticipeRepository extends AbstractRepository
{
    protected const TABLE = 'participe';
    protected const ID = 'id';


    /**
     * Sauvegarde une participation dans la base de données
     * @param $participe 
     * @return bool
     */
    public function save(Participe $participe): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO participe (`id_utilisateur`, id_evenement)
                                            VALUES (:idUtilisateur, :idEvenement)");

        return $stmt->execute([
            'idUtilisateur' => $participe->getIdUtilisateur(),
            'idEvenement' => $participe->getIdEvenement(),
        ]);
    }

    /**
     * Suppression d'un utilisateur participant à un évènement dans la base de données
     * @param $idUtilisateur 
     * @param $idEvenement
     * @return bool
     */
    public function deleteUtilisateur(int $idUtilisateur, int $idEvenement): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM " . static::TABLE . " WHERE id_utilisateur =:idUtilisateur AND id_evenement =:idEvenement");
        return $statement->execute([
            'idUtilisateur' => $idUtilisateur,
            'idEvenement' => $idEvenement,
        ]);
    }

    /**
     * Suppresion d'un client dans un evenement
     * @param $idUtilisateur 
     * @param $idEvenement
     * @return bool
     */
    public function checkIfAlreadyParticipe(int $idUtilisateur, int $idEvenement): bool
    {
        $statement = $this->pdo->prepare("SELECT * FROM participe WHERE id_utilisateur =:idUtilisateur AND id_evenement =:idEvenement");
        $statement->bindValue('idUtilisateur', $idUtilisateur, \PDO::PARAM_INT);
        $statement->bindValue('idEvenement', $idEvenement, \PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll();
        if (empty($results)) {
            return false;
        }
        return true;
    }
}
