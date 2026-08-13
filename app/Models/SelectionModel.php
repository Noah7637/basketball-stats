<?php

namespace App\Models;

use App\Core\Database;

class SelectionModel
{
   
    public static function saveSelection(int $matchId, array $joueurIds): void
    {
        $pdo = Database::getInstance();

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM match_joueurs WHERE match_id = :match_id");
        $stmt->execute(['match_id' => $matchId]);

        $stmt = $pdo->prepare(
            "INSERT INTO match_joueurs (match_id, joueur_id) VALUES (:match_id, :joueur_id)"
        );

        foreach ($joueurIds as $joueurId) {
            $stmt->execute([
                'match_id' => $matchId,
                'joueur_id' => (int) $joueurId,
            ]);
        }

        $pdo->commit();
    }

    
    public static function joueursDuMatch(int $matchId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "SELECT j.* FROM joueurs j
             JOIN match_joueurs mj ON mj.joueur_id = j.id
             WHERE mj.match_id = :match_id
             ORDER BY j.nom"
        );
        $stmt->execute(['match_id' => $matchId]);
        return $stmt->fetchAll();
    }
}