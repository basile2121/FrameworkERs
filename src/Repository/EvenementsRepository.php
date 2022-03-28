<?php

namespace App\Repository;

use App\Entity\Evenements;

final class EvenementsRepository extends AbstractRepository
{
    protected const TABLE = 'evenements';

    public function save(Evenements $evenements): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO evenements (`titre`, sous_titre, description, `nb_participants_max`, prix, date, created_at, updated_at, id_utilisateur, id_adresse, id_media, id_categorie, id_statut)
                                            VALUES (:titre, :sousTitre, :description, :nbParticipantsMax, :prix, :date, :createdAt, :updatedAt, :idUtilisateur, :idAdresse, :idMedia, :idCategorie, :idStatut)");

        return $stmt->execute([
            'titre' => $evenements->getTitre(),
            'sousTitre' => $evenements->getSousTitre(),
            'description' => $evenements->getDescription(),
            'nbParticipantsMax' => $evenements->getNbParticipantsMax(),
            'prix' => $evenements->getPrix(),
            'date' => $evenements->getDate()->format('Y-m-d'),
            'createdAt' => $evenements->getCreatedAt()->format('Y-m-d'),
            'updatedAt' => $evenements->getUpdatedAt()->format('Y-m-d'),
            'idUtilisateur' => $evenements->getIdUtilisateur(),
            'idAdresse' => $evenements->getIdAdresse(),
            'idMedia' => $evenements->getIdMedia(),
            'idCategorie' => $evenements->getIdCategorie(),
            'idStatut' => $evenements->getIdStatut(),
        ]);
    }
}
