# Match Stats

Application de suivi de statistiques de matchs (PHP MVC + MySQL).

## Installation

1. Cloner le repo
2. `composer install` (ou `composer dump-autoload` si pas de dépendances)
3. Copier `config/config.example.php` vers `config/config.php` et renseigner les identifiants de la base
4. Importer `sql/schema.sql` dans MySQL
5. Lancer le serveur PHP intégré depuis le dossier `public/` :
   ```
   php -S localhost:8000
   ```
6. Ouvrir http://localhost:8000

## Structure

```
app/
  Controllers/   Logique métier, reçoit les requêtes
  Models/        Accès aux données (requêtes SQL via PDO)
  Views/         Templates HTML/PHP
  Core/          Router, Database (classes techniques)
config/          Config (non commitée)
public/          Point d'entrée web (index.php) + assets CSS/JS
sql/             Schéma de la base de données
```

## Roadmap

- [ ] Formulaire de création de match
- [ ] Ajout de statistiques par joueur sur un match
- [ ] Gestion des joueurs/équipes
