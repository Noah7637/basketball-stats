DROP DATABASE IF EXISTS match_stats;
CREATE DATABASE match_stats CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE match_stats;

-- --------------------------------------------------------
-- Table `joueurs`
-- --------------------------------------------------------
CREATE TABLE joueurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    numero INT NULL,
    poste VARCHAR(30) NULL,
    actif BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO joueurs (id, nom, numero, poste, actif) VALUES
(1, 'Noah Benhabrou', 10, 'Arrière', 1),
(2, 'Axel Pizzini', 6, 'Ailier fort', 1),
(3, 'Jules Forest', 12, 'Pivot', 1),
(4, 'Noah Roy', 8, 'Meneur', 1),
(6, 'Loan Alvarez', 77, 'Pivot', 1),
(7, 'Léo Scanu', 9, 'Arrière', 1);

-- --------------------------------------------------------
-- Table `matches`
-- --------------------------------------------------------
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adversaire VARCHAR(100) NOT NULL,
    domicile BOOLEAN NOT NULL DEFAULT TRUE,
    date_match DATE NOT NULL,
    score_mon_equipe INT NOT NULL DEFAULT 0,
    score_adversaire INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO matches (id, adversaire, domicile, date_match, score_mon_equipe, score_adversaire, created_at) VALUES
(1, 'Crussol', 0, '2026-09-20', 78, 65, '2026-08-11 21:43:38'),
(2, 'Bron', 1, '2026-09-27', 61, 72, '2026-08-11 22:13:58'),
(3, 'charpenne', 0, '2026-09-21', 0, 0, '2026-08-12 21:43:32'),
(10, 'ASR', 1, '2026-08-12', 0, 0, '2026-08-12 22:50:02'),
(11, 'zebi', 1, '2026-08-12', 0, 0, '2026-08-12 22:57:41');

-- --------------------------------------------------------
-- Table `match_joueurs` (feuille de match)
-- Note : les lignes (1,5) et (1,8) de ton ancien export référençaient des
-- joueurs déjà supprimés — elles ont été retirées ici (données orphelines).
-- --------------------------------------------------------
CREATE TABLE match_joueurs (
    match_id INT NOT NULL,
    joueur_id INT NOT NULL,
    PRIMARY KEY (match_id, joueur_id),
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (joueur_id) REFERENCES joueurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO match_joueurs (match_id, joueur_id) VALUES
(1, 1), (1, 3), (1, 4), (1, 7),
(3, 1), (3, 2), (3, 3), (3, 4), (3, 6), (3, 7),
(10, 1), (10, 2), (10, 3),
(11, 2), (11, 3);

-- --------------------------------------------------------
-- Table `statistiques` (individuelle, par joueur et par match)
-- Pas de colonne "points" : calculé à la volée à partir des tirs
-- (2pts_reussis * 2 + 3pts_reussis * 3 + lancers_francs_reussis)
-- --------------------------------------------------------
CREATE TABLE statistiques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    joueur_id INT NOT NULL,
    passes_decisives INT NOT NULL DEFAULT 0,
    tirs_2pts_tentes INT NOT NULL DEFAULT 0,
    tirs_2pts_reussis INT NOT NULL DEFAULT 0,
    tirs_3pts_tentes INT NOT NULL DEFAULT 0,
    tirs_3pts_reussis INT NOT NULL DEFAULT 0,
    lancers_francs_tentes INT NOT NULL DEFAULT 0,
    lancers_francs_reussis INT NOT NULL DEFAULT 0,
    duels_defensifs_gagnes INT NOT NULL DEFAULT 0,
    UNIQUE KEY unique_stat_joueur_match (match_id, joueur_id),
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    FOREIGN KEY (joueur_id) REFERENCES joueurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table `statistiques_collectives` (une ligne par match ET par quart-temps)
-- --------------------------------------------------------
CREATE TABLE statistiques_collectives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_id INT NOT NULL,
    quart_temps TINYINT NOT NULL, -- 1, 2, 3, 4 (5+ pour prolongations éventuelles)
    points INT NOT NULL DEFAULT 0,
    nb_possessions INT NOT NULL DEFAULT 0,
    possessions_transition INT NOT NULL DEFAULT 0,
    possessions_jeu_pose INT NOT NULL DEFAULT 0,
    lancers_francs_tentes INT NOT NULL DEFAULT 0,
    lancers_francs_reussis INT NOT NULL DEFAULT 0,
    nb_contre_attaques INT NOT NULL DEFAULT 0,
    nb_contre_attaques_reussies INT NOT NULL DEFAULT 0,
    ballons_gagnes_defense INT NOT NULL DEFAULT 0,
    rebonds_defensifs INT NOT NULL DEFAULT 0,
    rebonds_offensifs_adversaires INT NOT NULL DEFAULT 0,
    UNIQUE KEY unique_stats_match_qt (match_id, quart_temps),
    FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
