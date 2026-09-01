<?php

namespace App\Models;

use App\Core\Database;

class JoueurModel
{
    public static function allWithStats(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query(
            "SELECT
    j.id, j.nom, j.numero, j.poste,
    COALESCE(AVG(s.tirs_2pts_reussis*2 + s.tirs_3pts_reussis*3 + s.lancers_francs_reussis), 0) AS moyenne_points,
    COALESCE(AVG(s.passes_decisives), 0) AS moyenne_passes,
    COALESCE(
        SUM(s.tirs_2pts_reussis*2 + s.tirs_3pts_reussis*3 + s.lancers_francs_reussis) / NULLIF(SUM(s.tirs_2pts_tentes + s.tirs_3pts_tentes), 0),
        0
        ) AS points_par_tentative,
    COALESCE(
        (SUM(s.tirs_2pts_reussis + s.tirs_3pts_reussis) / NULLIF(SUM(s.tirs_2pts_tentes + s.tirs_3pts_tentes), 0))*100,
        0
    ) AS pourcentage_reussite_tirs,
    COALESCE(AVG(s.duels_defensifs_gagnes), 0) AS duel_def,
    COUNT(DISTINCT s.match_id) AS matchs_joues
FROM joueurs j
LEFT JOIN statistiques s ON s.joueur_id = j.id
WHERE j.actif = 1
GROUP BY j.id
ORDER BY moyenne_points DESC"
        );
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * IMPORTANT : le filtre user_id ici n'est pas juste pour trier -- c'est ce
     * qui empêche un utilisateur de voir/modifier la fiche d'un joueur qui
     * appartient à quelqu'un d'autre en devinant son id dans l'URL.
     */
    public static function find(int $id, int $userId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function all(int $userId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE actif = 1 AND user_id = :user_id ORDER BY nom");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public static function create(int $userId, array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO joueurs (user_id, nom, numero, poste) VALUES (:user_id, :nom, :numero, :poste)"
        );
        $stmt->execute(array_merge($data, ['user_id' => $userId]));
        return (int) $pdo->lastInsertId();
    }

    /**
     * Le WHERE ... AND user_id = :user_id est essentiel ici aussi : sans lui,
     * n'importe quel utilisateur connecté pourrait modifier un joueur en
     * changeant juste l'id dans le formulaire, même s'il ne lui appartient pas.
     */
    public static function update(int $id, int $userId, array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "UPDATE joueurs
             SET nom = :nom, numero = :numero, poste = :poste
             WHERE id = :id AND user_id = :user_id"
        );
        $data['id'] = $id;
        $data['user_id'] = $userId;
        $stmt->execute($data);
        return $stmt->rowCount();
    }

    public static function delete(int $id, int $userId): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("DELETE FROM joueurs WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->rowCount();
    }
}