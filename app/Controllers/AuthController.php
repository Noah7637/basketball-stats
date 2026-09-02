<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\UtilisateurModel;

class AuthController
{
    public function showRegister(): void
    {
        $errors = [];
        $old = [];
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function register(): void
    {
        $errors = $this->validateRegister($_POST);

        if (!empty($errors)) {
            $old = $_POST;
            require __DIR__ . '/../Views/auth/register.php';
            return;
        }

        $nomEquipe = trim($_POST['nom_equipe']);
        $userId = UtilisateurModel::create(
            trim($_POST['email']),
            $_POST['mot_de_passe'],
            $nomEquipe
        );

        Auth::login($userId, $nomEquipe);
        header('Location: /');
        exit;
    }

    public function showLogin(): void
    {
        $errors = [];
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';

        $utilisateur = UtilisateurModel::findByEmail($email);

        if (!$utilisateur || !UtilisateurModel::verifierMotDePasse($utilisateur, $motDePasse)) {
            $errors = ["Email ou mot de passe incorrect."];
            require __DIR__ . '/../Views/auth/login.php';
            return;
        }

        Auth::login($utilisateur['id'], $utilisateur['nom_equipe']);
        header('Location: /');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }

    private function validateRegister(array $data): array
    {
        $errors = [];

        if (empty(trim($data['email'] ?? '')) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        } elseif (UtilisateurModel::findByEmail(trim($data['email']))) {
            $errors[] = "Un compte existe déjà avec cet email.";
        }

        if (strlen($data['mot_de_passe'] ?? '') < 8) {
            $errors[] = "Le mot de passe doit faire au moins 8 caractères.";
        }

        if (empty(trim($data['nom_equipe'] ?? ''))) {
            $errors[] = "Le nom de l'équipe est requis.";
        }

        return $errors;
    }
}