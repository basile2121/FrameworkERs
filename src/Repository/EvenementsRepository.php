<?php

namespace App\Repository;

use App\Entity\Evenements;
use ReflectionException;

final class EvenementsRepository extends AbstractRepository
{
    protected const TABLE = 'evenements';
    protected const ID = 'id_evenement';

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

    public function update(Evenements $evenements): bool
    {
        $stmt = $this->pdo->prepare("UPDATE evenements SET 
                        `titre` = :titre,
                        `sous_titre` = :sousTitre,
                        `description` = :description,
                        `nb_participants_max` = :nbParticipantsMax,
                        `prix` = :prix,
                        `date` = :date,
                        `updated_at` = :updatedAt,
                        `id_categorie` = :idCategorie, 
                        `id_adresse` = :idAdresse
                        WHERE `id_statut` = :idStatut" );

        return $stmt->execute([
            'titre' => $evenements->getTitre(),
            'sousTitre' => $evenements->getSousTitre(),
            'description' => $evenements->getDescription(),
            'nbParticipantsMax' => $evenements->getNbParticipantsMax(),
            'prix' => $evenements->getPrix(),
            'date' => $evenements->getDate()->format('Y-m-d'),
            'updatedAt' => $evenements->getUpdatedAt()->format('Y-m-d'),
            'idCategorie' => $evenements->getIdCategorie(),
            'idAdresse' => $evenements->getIdAdresse(),
            'idStatut' => $evenements->getIdStatut(),
        ]);
    }

    /**
     * Fonction permettant de récupérer trois évenèments dont la date est la plus proche
     * @throws ReflectionException
     */
    public function getEvenementAVenir(): array
    {

        $query = "SELECT * FROM evenements WHERE date > now() ORDER BY date ASC LIMIT 3;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->setHydrate($data);
    }

    /**
     * Fonction permettant de récupérer neuf évenèments dont la date est la plus proche
     * @throws ReflectionException
     */
    public function getEvenementProchain(): array
    {
        $thirdFirstEvent = $this->getEvenementAVenir();
        if (empty($thirdFirstEvent)) {
            return [];
        }
        $id = [];
        foreach($thirdFirstEvent as $event){
            $eventId= $event->getIdEvenement();
            array_push($id, $eventId);
        }
        $idImplode = implode(",", $id);

        $query = "SELECT * FROM evenements WHERE date > now() AND id_evenement NOT IN (".$idImplode.") ORDER BY date ASC LIMIT 9;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->setHydrate($data);
    }

    /**
     * @throws ReflectionException
     */
    public function getEvenementByParticipation(): array
    {
        $query ="SELECT e.titre,(COUNT(u.id_utilisateur)/e.nb_participants_max*100) as pourcentage FROM evenements e LEFT JOIN participe p ON e.id_evenement = p.id_evenement LEFT JOIN utilisateurs u ON  p.id_utilisateur = u.id_utilisateur WHERE e.date > now() GROUP BY e.id_evenement ORDER BY date ASC LIMIT 3;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll();
        return $this->setHydrate($data);
    }
}
