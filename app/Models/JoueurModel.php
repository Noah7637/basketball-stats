<?php

namespace App\Models;

use App\Core\Database;

class JoueurModel
{
    /**
     * Tous les joueurs actifs avec leurs stats cumulées sur tous les matchs.
     * LEFT JOIN pour ne pas exclure un joueur qui n'a pas encore de stats saisies.
     */
    public static function allWithStats(int $userId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
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
WHERE j.user_id = :userId
GROUP BY j.id
ORDER BY moyenne_points DESC"
        );
         $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

    public static function joueurWithStats(int $id): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
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
WHERE j.id = :id
GROUP BY j.id, j.nom, j.numero, j.poste"
        );
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function all(int $user_id): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE user_id = :userId ORDER BY nom");
        $stmt->execute(['userId' => $_SESSION['user_id']]);
        $result = $stmt->fetchAll();
        return $result ?: null ;
    }

    public static function statsParMatch(int $id): array
{
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare(
        "SELECT
            m.adversaire,
            m.date_match AS date,
            m.score_mon_equipe,
            m.score_adversaire,
            s.match_id,
            s.passes_decisives,
            s.tirs_2pts_reussis,
            s.tirs_2pts_tentes,
            s.tirs_3pts_reussis,
            s.tirs_3pts_tentes,
            s.lancers_francs_reussis,
            s.lancers_francs_tentes,
            s.duels_defensifs_gagnes,
            (s.tirs_2pts_reussis*2 + s.tirs_3pts_reussis*3 + s.lancers_francs_reussis) AS points
         FROM statistiques s
         JOIN matches m ON s.match_id = m.id
         WHERE s.joueur_id = :id
         ORDER BY m.date_match DESC"
    );
    $stmt->execute(['id' => $id]);
    return $stmt->fetchAll();
}

    public static function create(array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO joueurs (user_id, nom, numero, poste) VALUES (:user_id, :nom, :numero, :poste)"
        );
        $stmt->execute($data);
        return (int) $pdo->lastInsertId();
    }

    public static function delete(int $id): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("DELETE FROM joueurs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

    public static function update(int $id, array $data): int
{
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare(
        "UPDATE joueurs
         SET nom = :nom,
             numero = :numero,
             poste = :poste
         WHERE id = :id"
    );
    $data['id'] = $id;
    $stmt->execute($data);
    return $stmt->rowCount();
}

    /**
     * Le meneur de chaque catégorie (marqueur, passeur, rebondeur), pour le mettre en avant.
     * Retourne null pour une catégorie si aucune stat n'a encore été saisie.
     */
    // public static function topPerformers(): array
    // {
    //     $pdo = Database::getInstance();

    //     $categories = [
    //         'marqueur' => 'points',
    //         'passeur' => 'passes_decisives',
    //         'rebondeur' => 'rebonds',
    //     ];

    //     $result = [];

    //     foreach ($categories as $key => $colonne) {
    //         $stmt = $pdo->prepare(
    //             "SELECT j.nom, j.numero, SUM(s.$colonne) AS total
    //              FROM statistiques s
    //              JOIN joueurs j ON j.id = s.joueur_id
    //              WHERE j.actif = 1
    //              GROUP BY j.id
    //              ORDER BY total DESC
    //              LIMIT 1"
    //         );
    //         $stmt->execute();
    //         $result[$key] = $stmt->fetch() ?: null;
    //     }

    //     return $result;
    // }
}
