<?php

namespace App\Models;

use App\Core\Database;

class JoueurModel
{
    /**
     * Tous les joueurs actifs avec leurs stats cumulées sur tous les matchs.
     * LEFT JOIN pour ne pas exclure un joueur qui n'a pas encore de stats saisies.
     */
    public static function allWithStats(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query(
            "SELECT
                j.id, j.nom, j.numero, j.poste,
                COALESCE(SUM(s.points), 0) AS total_points,
                COALESCE(SUM(s.rebonds), 0) AS total_rebonds,
                COALESCE(SUM(s.passes_decisives), 0) AS total_passes,
                COALESCE(SUM(s.interceptions), 0) AS total_interceptions,
                COALESCE(SUM(s.contres), 0) AS total_contres,
                COUNT(DISTINCT s.match_id) AS matchs_joues
             FROM joueurs j
             LEFT JOIN statistiques s ON s.joueur_id = j.id
             WHERE j.actif = 1
             GROUP BY j.id
             ORDER BY total_points DESC"
        );
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function all(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM joueurs WHERE actif = 1 ORDER BY nom");
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO joueurs (nom, numero, poste) VALUES (:nom, :numero, :poste)"
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
    public static function topPerformers(): array
    {
        $pdo = Database::getInstance();

        $categories = [
            'marqueur' => 'points',
            'passeur' => 'passes_decisives',
            'rebondeur' => 'rebonds',
        ];

        $result = [];

        foreach ($categories as $key => $colonne) {
            $stmt = $pdo->prepare(
                "SELECT j.nom, j.numero, SUM(s.$colonne) AS total
                 FROM statistiques s
                 JOIN joueurs j ON j.id = s.joueur_id
                 WHERE j.actif = 1
                 GROUP BY j.id
                 ORDER BY total DESC
                 LIMIT 1"
            );
            $stmt->execute();
            $result[$key] = $stmt->fetch() ?: null;
        }

        return $result;
    }
}
