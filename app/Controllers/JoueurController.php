<?php

namespace App\Controllers;

use App\Models\JoueurModel;

class JoueurController
{
    public function index(): void
    {
        $joueurs = JoueurModel::allWithStats();
        $top = JoueurModel::topPerformers();
        require __DIR__ . '/../Views/joueurs/index.php';
    }

    public function create(): void
    {
        $errors = [];
        $old = [];
        require __DIR__ . '/../Views/joueurs/create.php';
    }

    public function store(): void
    {
        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            $old = $_POST;
            require __DIR__ . '/../Views/joueurs/create.php';
            return;
        }

        JoueurModel::create([
            'nom' => trim($_POST['nom']),
            'numero' => $_POST['numero'] !== '' ? (int) $_POST['numero'] : null,
            'poste' => $_POST['poste'] !== '' ? $_POST['poste'] : null,
        ]);

        header('Location: /');
        exit;
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['nom'] ?? ''))) {
            $errors[] = "Le nom du joueur est requis.";
        }

        if (($data['numero'] ?? '') !== '' && !is_numeric($data['numero'])) {
            $errors[] = "Le numéro doit être un nombre.";
        }

        return $errors;
    }
}
