<?php

namespace App\Controllers;

use App\Models\JoueurModel;

class JoueurController
{
    public function index(): void
    {
        $joueurs = JoueurModel::allWithStats();
        // $top = JoueurModel::topPerformers();
        require __DIR__ . '/../Views/joueurs/index.php';
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $joueur = JoueurModel::find($id);
        $moyenne = JoueurModel::joueurWithStats($id);
        $stats_match = JoueurModel::statsParMatch($id);


        if (!$joueur) {
            http_response_code(404);
            echo "Joueur introuvable.";
            return;
        }

        require __DIR__ . '/../Views/joueurs/show.php';
    }

    public function delete(): void
    {
        $id = (int) ($_GET['id']); 

        if (!$id) {
            http_response_code(404);
            echo "Joueur introuvable.";
            return;
        }

        JoueurModel::delete($id);
        header('Location: /');
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

    public function edit(): void
    {
        $joueurId = (int) ($_GET['id'] ?? 0);
        $joueur = JoueurModel::find($joueurId);

        if (!$joueur) {
            http_response_code(404);
            echo "Joueur introuvable.";
            return;
        }

        $errors = [];

        require __DIR__ . '/../Views/joueurs/edit.php';
    }

    public function update(): void
    {
        $joueurId = (int) ($_POST['joueur_id'] ?? 0);
        $joueur = JoueurModel::find($joueurId);

        if (!$joueur) {
            http_response_code(404);
            echo "Joueur introuvable.";
            return;
        }

        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            require __DIR__ . '/../Views/joueurs/edit.php';
            return;
        }

        JoueurModel::update($joueurId, [
            'nom' => trim($_POST['nom']),
            'numero' => (int) $_POST['numero'],
            'poste' => trim($_POST['poste']),
        ]);

        header('Location: /');
        exit;
    }
}
