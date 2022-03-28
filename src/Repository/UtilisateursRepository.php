<?php

namespace App\Repository;

use App\Entity\Utilisateurs;

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
}
