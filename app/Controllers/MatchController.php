<?php

namespace App\Controllers;

use App\Models\JoueurModel;
use App\Models\MatchModel;
use App\Models\SelectionModel;

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
        $joueurs = SelectionModel::joueursDuMatch($id);

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

    public function delete(): void
    {
        $id = (int) ($_GET['id']); 

        if (!$id) {
            http_response_code(404);
            echo "Match introuvable.";
            return;
        }

        MatchModel::delete($id);
        header('Location: /matches');
    }

    public function store(): void
    {
        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            $old = $_POST;
            require __DIR__ . '/../Views/matches/create.php';
            return;
        }

        $matchId = MatchModel::create([
            'adversaire' => trim($_POST['adversaire']),
            'domicile' => isset($_POST['domicile']) ? 1 : 0,
            'date_match' => $_POST['date_match'],
            'score_mon_equipe' => (int) $_POST['score_mon_equipe'],
            'score_adversaire' => (int) $_POST['score_adversaire'],
        ]);

        header('Location: /match/selection?id=' . $matchId);
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

    public function selectionJoueurs(): void
    {
        $matchId = (int) ($_GET['id'] ?? 0);
        $match = MatchModel::find($matchId);

        if (!$match) {
            http_response_code(404);
            echo "Match introuvable.";
            return;
        }

        $joueurs = JoueurModel::all();
        $selectionnes = array_column(SelectionModel::joueursDuMatch($matchId), 'id');

        require __DIR__ . '/../Views/matches/selection.php';
    }

    public function saveSelection(): void
    {
        $matchId = (int) ($_POST['match_id'] ?? 0);
        $joueurIds = $_POST['joueurs'] ?? [];

        if (!$matchId || empty($joueurIds)) {
            // On renvoie vers la sélection avec un message si rien n'est coché
            header('Location: /match/selection?id=' . $matchId . '&error=1');
            exit;
        }

        SelectionModel::saveSelection($matchId, $joueurIds);

        // Prochaine étape à coder : la page de saisie des stats
        header('Location: /match/stats?id=' . $matchId);
        exit;
    }

    public function edit(): void
    {
        $matchId = (int) ($_GET['id'] ?? 0);
        $match = MatchModel::find($matchId);

        if (!$match) {
            http_response_code(404);
            echo "Match introuvable.";
            return;
        }

        $errors = [];
        $joueurs = JoueurModel::all();
        $selectionnes = array_column(SelectionModel::joueursDuMatch($matchId), 'id');

        require __DIR__ . '/../Views/matches/edit.php';
    }

    public function update(): void
    {
        $matchId = (int) ($_POST['match_id'] ?? 0);
        $match = MatchModel::find($matchId);

        if (!$match) {
            http_response_code(404);
            echo "Match introuvable.";
            return;
        }

        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            $joueurs = JoueurModel::all();
            $selectionnes = array_column(SelectionModel::joueursDuMatch($matchId), 'id');
            require __DIR__ . '/../Views/matches/edit.php';
            return;
        }

        MatchModel::update($matchId, [
            'adversaire' => trim($_POST['adversaire']),
            'domicile' => isset($_POST['domicile']) ? 1 : 0,
            'date_match' => $_POST['date_match'],
            'score_mon_equipe' => (int) $_POST['score_mon_equipe'],
            'score_adversaire' => (int) $_POST['score_adversaire'],
        ]);

        SelectionModel::saveSelection($matchId, $_POST['joueurs'] ?? []);

        header('Location: /matches');
        exit;
    }

}
