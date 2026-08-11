CREATE DATABASE IF NOT EXISTS match_stats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE match_stats;

-- Uniquement les joueurs de TON équipe (l'équipe domicile est toujours la même,
-- donc pas besoin de la stocker sur chaque match)
CREATE TABLE joueurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    numero INT NULL,
    poste VARCHAR(30) NULL, -- Meneur, Arrière, Ailier, Ailier fort, Pivot
    actif BOOLEAN NOT NULL DEFAULT TRUE -- désactiver un joueur sans perdre son historique
);

-- Un match = ton équipe (fixe) contre un adversaire (juste un nom)
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adversaire VARCHAR(100) NOT NULL,
    domicile BOOLEAN NOT NULL DEFAULT TRUE,
    date_match DATE NOT NULL,
    score_mon_equipe INT NOT NULL DEFAULT 0,
    score_adversaire INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Stats basket d'un joueur sur un match donné.
-- Colonnes fixes plutôt qu'une table type_stat/valeur : le basket a un jeu de stats
-- standard et connu à l'avance, donc les colonnes fixes rendent les requêtes
-- d'agrégation (meilleur marqueur, etc.) simples et rapides.
CREATE TABLE statistiques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    joueur_id INT NOT NULL,
    points INT NOT NULL DEFAULT 0,
    rebonds INT NOT NULL DEFAULT 0,
    passes_decisives INT NOT NULL DEFAULT 0,
    interceptions INT NOT NULL DEFAULT 0,
    contres INT NOT NULL DEFAULT 0,
    balles_perdues INT NOT NULL DEFAULT 0,
    fautes INT NOT NULL DEFAULT 0,
    minutes_jouees INT NOT NULL DEFAULT 0,
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (joueur_id) REFERENCES joueurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_stat_joueur_match (match_id, joueur_id) -- une seule ligne de stats par joueur et par match
);
