<?php

namespace App\Models;

use App\Core\Database;

class MatchModel
{
    public static function all(): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->query("SELECT * FROM matches ORDER BY date_match DESC");
        return $stmt->fetchAll();
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
            "INSERT INTO matches (adversaire, domicile, date_match, score_mon_equipe, score_adversaire)
             VALUES (:adversaire, :domicile, :date_match, :score_mon_equipe, :score_adversaire)"
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
