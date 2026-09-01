<?php

namespace App\Models;

use App\Core\Database;

class UtilisateurModel
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT id, email, nom_equipe, created_at FROM utilisateurs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function create(string $email, string $motDePasse, string $nomEquipe): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO utilisateurs (email, mot_de_passe_hash, nom_equipe)
             VALUES (:email, :hash, :nom_equipe)"
        );
        $stmt->execute([
            'email' => $email,
            'hash' => password_hash($motDePasse, PASSWORD_DEFAULT),
            'nom_equipe' => $nomEquipe,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function verifierMotDePasse(array $utilisateur, string $motDePasse): bool
    {
        return password_verify($motDePasse, $utilisateur['mot_de_passe_hash']);
    }
}