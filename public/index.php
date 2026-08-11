<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\JoueurController;
use App\Controllers\MatchController;

$router = new Router();

// Accueil = joueurs (le cœur de l'appli, destinée à un coach qui suit ses joueurs)
$router->get('/', [JoueurController::class, 'index']);
$router->get('/joueurs/create', [JoueurController::class, 'create']);
$router->post('/joueurs/store', [JoueurController::class, 'store']);

// Matchs = support secondaire pour rattacher des stats
$router->get('/matches', [MatchController::class, 'index']);
$router->get('/match', [MatchController::class, 'show']);
$router->get('/match/create', [MatchController::class, 'create']);
$router->post('/match/store', [MatchController::class, 'store']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
