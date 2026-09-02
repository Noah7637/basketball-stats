<?php

namespace App\Models;

use App\Core\Database;

class MatchModel
{
    public static function all()
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM matches WHERE user_id = :user_id ORDER BY date_match DESC");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
        $result = $stmt->fetchAll();
        return $result ?: null ;
    }

    public static function moyenne(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT
    COUNT(DISTINCT sc.match_id) AS nb_matchs,
    COALESCE(SUM(sc.points) / NULLIF(SUM(sc.nb_possessions), 0), 0) AS pts_par_possession,
    COALESCE(SUM(sc.points_transition) / NULLIF(SUM(sc.possessions_transition), 0), 0) AS pts_par_transition,
    COALESCE(SUM(sc.points_jeu_pose) / NULLIF(SUM(sc.possessions_jeu_pose), 0), 0) AS pts_par_jeu_pose,
    COALESCE(SUM(sc.lancers_francs_reussis), 0) AS lf_reussis,
    COALESCE(SUM(sc.lancers_francs_tentes), 0) AS lf_tentes,
    COALESCE(SUM(sc.lancers_francs_reussis) / NULLIF(SUM(sc.lancers_francs_tentes), 0), 0) AS pourcentage_lf,
    COALESCE(SUM(sc.nb_contre_attaques), 0) AS contre_attaques,
    COALESCE(SUM(sc.nb_contre_attaques_reussies) / NULLIF(SUM(sc.nb_contre_attaques), 0), 0) AS pourcentage_contre_attaques,
    COALESCE(SUM(sc.rebonds_defensifs), 0) AS reb_def,
    COALESCE(SUM(sc.rebonds_offensifs_adversaires), 0) AS reb_off_adv
FROM statistiques_collectives sc");
        return $stmt->fetch();
    }

    public static function statMatch(int $id): array|false
{
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT
    sc.match_id,
    COALESCE(SUM(sc.points) / NULLIF(SUM(sc.nb_possessions), 0), 0) AS pts_par_possession,
    COALESCE(SUM(sc.points_transition) / NULLIF(SUM(sc.possessions_transition), 0), 0) AS pts_par_transition,
    COALESCE(SUM(sc.points_jeu_pose) / NULLIF(SUM(sc.possessions_jeu_pose), 0), 0) AS pts_par_jeu_pose,
    COALESCE(SUM(sc.lancers_francs_reussis), 0) AS lf_reussis,
    COALESCE(SUM(sc.lancers_francs_tentes), 0) AS lf_tentes,
    COALESCE(SUM(sc.lancers_francs_reussis) / NULLIF(SUM(sc.lancers_francs_tentes), 0), 0) AS pourcentage_lf,
    COALESCE(SUM(sc.nb_contre_attaques), 0) AS contre_attaques,
    COALESCE(SUM(sc.nb_contre_attaques_reussies) / NULLIF(SUM(sc.nb_contre_attaques), 0), 0) AS pourcentage_contre_attaques,
    COALESCE(SUM(sc.rebonds_defensifs), 0) AS reb_def,
    COALESCE(SUM(sc.rebonds_offensifs_adversaires), 0) AS reb_off_adv
    FROM statistiques_collectives sc WHERE sc.match_id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM matches WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO matches (user_id, adversaire, domicile, date_match, score_mon_equipe, score_adversaire)
             VALUES (:user_id, :adversaire, :domicile, :date_match, :score_mon_equipe, :score_adversaire)"
        );
        $stmt->execute($data);
        return (int) $pdo->lastInsertId();
    }
 
    public static function delete(int $id): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("DELETE FROM matches WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }

    public static function update(int $id, array $data): int
{
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare(
        "UPDATE matches
         SET adversaire = :adversaire,
             domicile = :domicile,
             date_match = :date_match,
             score_mon_equipe = :score_mon_equipe,
             score_adversaire = :score_adversaire
         WHERE id = :id"
    );
    $data['id'] = $id;
    $stmt->execute($data);
    return $stmt->rowCount();
}
}
