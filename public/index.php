<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\JoueurController;
use App\Controllers\MatchController;

$router = new Router();

// Accueil = joueurs (le cœur de l'appli, destinée à un coach qui suit ses joueurs)
$router->get('/', [JoueurController::class, 'index']);
$router->get('/joueur', [JoueurController::class, 'show']);
$router->get('/joueurs/create', [JoueurController::class, 'create']);
$router->get('/joueur/delete', [JoueurController::class, 'delete']);
$router->post('/joueurs/store', [JoueurController::class, 'store']);

$router->get('/joueur/edit', [JoueurController::class, 'edit']);
$router->post('/joueur/update', [JoueurController::class, 'update']);

// Matchs = support secondaire pour rattacher des stats
$router->get('/matches', [MatchController::class, 'index']);
$router->get('/match', [MatchController::class, 'show']);
$router->get('/match/create', [MatchController::class, 'create']);
$router->get('/match/delete', [MatchController::class, 'delete']);
$router->post('/match/store', [MatchController::class, 'store']);

$router->get('/match/selection', [MatchController::class, 'selectionJoueurs']);
$router->post('/match/selection', [MatchController::class, 'saveSelection']);

$router->get('/match/edit', [MatchController::class, 'edit']);
$router->post('/match/update', [MatchController::class, 'update']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
