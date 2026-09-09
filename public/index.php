<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Auth;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\JoueurController;
use App\Controllers\MatchController;

Auth::start();

$router = new Router();

// Routes publiques (accessibles sans être connecté)
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

$cheminDemande = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$routesPubliques = ['/login', '/register', '/logout'];

// Toute route qui n'est pas dans la liste publique nécessite d'être connecté
if (!in_array($cheminDemande, $routesPubliques, true)) {
    Auth::requireLogin();
}

$router->get('/', [JoueurController::class, 'index']);
$router->get('/joueurs/create', [JoueurController::class, 'create']);
$router->post('/joueurs/store', [JoueurController::class, 'store']);
$router->get('/joueur', [JoueurController::class, 'show']);
$router->get('/joueur/edit', [JoueurController::class, 'edit']);
$router->post('/joueur/update', [JoueurController::class, 'update']);
$router->get('/joueur/delete', [JoueurController::class, 'delete']);

$router->get('/matches', [MatchController::class, 'index']);
$router->get('/match', [MatchController::class, 'show']);
$router->get('/match/create', [MatchController::class, 'create']);
$router->post('/match/store', [MatchController::class, 'store']);
$router->get('/match/edit', [MatchController::class, 'edit']);
$router->post('/match/update', [MatchController::class, 'update']);
$router->get('/match/delete', [MatchController::class, 'delete']);
$router->get('/match/selection', [MatchController::class, 'selectionJoueurs']);
$router->post('/match/selection', [MatchController::class, 'saveSelection']);
$router->get('/match/live', [MatchController::class, 'live']);
$router->post('/match/live/save', [MatchController::class, 'liveSave']);
$router->get('/match/journal', [MatchController::class, 'journal']);
$router->post('/match/journal/delete-action', [MatchController::class, 'deleteJournalAction']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);