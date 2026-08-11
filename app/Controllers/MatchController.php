<?php

namespace App\Controllers;

use App\Models\MatchModel;

class MatchController
{
    public function index(): void
    {
        $matches = MatchModel::all();
        require __DIR__ . '/../Views/matches/index.php';
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $match = MatchModel::find($id);

        if (!$match) {
            http_response_code(404);
            echo "Match introuvable.";
            return;
        }

        require __DIR__ . '/../Views/matches/show.php';
    }

    public function create(): void
    {
        $errors = [];
        $old = [];
        require __DIR__ . '/../Views/matches/create.php';
    }

    public function store(): void
    {
        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            $old = $_POST;
            require __DIR__ . '/../Views/matches/create.php';
            return;
        }

        MatchModel::create([
            'adversaire' => trim($_POST['adversaire']),
            'domicile' => isset($_POST['domicile']) ? 1 : 0,
            'date_match' => $_POST['date_match'],
            'score_mon_equipe' => (int) $_POST['score_mon_equipe'],
            'score_adversaire' => (int) $_POST['score_adversaire'],
        ]);

        header('Location: /');
        exit;
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['adversaire'] ?? ''))) {
            $errors[] = "Le nom de l'adversaire est requis.";
        }

        if (empty($data['date_match'] ?? '')) {
            $errors[] = "La date du match est requise.";
        }

        if (($data['score_mon_equipe'] ?? '') === '' || !is_numeric($data['score_mon_equipe'])) {
            $errors[] = "Le score de ton équipe doit être un nombre.";
        }

        if (($data['score_adversaire'] ?? '') === '' || !is_numeric($data['score_adversaire'])) {
            $errors[] = "Le score adverse doit être un nombre.";
        }

        return $errors;
    }
}
