-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 01 sep. 2026 à 11:33
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `match_stats`
--

-- --------------------------------------------------------

--
-- Structure de la table `joueurs`
--

DROP TABLE IF EXISTS `joueurs`;
CREATE TABLE IF NOT EXISTS `joueurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int DEFAULT NULL,
  `poste` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `joueurs`
--

INSERT INTO `joueurs` (`id`, `nom`, `numero`, `poste`, `actif`) VALUES
(1, 'Noah Benhabrou', 10, 'Arrière', 1),
(2, 'Axel Pizzini', 6, 'Ailier fort', 1),
(3, 'Jules Forest', 12, 'Pivot', 1),
(4, 'Noah Roy', 8, 'Meneur', 1),
(7, 'Léo Scanu', 9, 'Arrière', 1),
(8, 'Arthur Engola', 5, 'Ailier', 1),
(9, 'Mylo Alvarez', 7, 'Meneur', 1),
(10, 'Martin Michel', 15, 'Ailier', 1),
(11, 'Wael Makloufi', 13, 'Ailier fort', 1),
(12, 'Loïck', 4, 'Meneur', 1),
(14, 'Paul Sevieri', 0, 'Ailier fort', 1);

-- --------------------------------------------------------

--
-- Structure de la table `matches`
--

DROP TABLE IF EXISTS `matches`;
CREATE TABLE IF NOT EXISTS `matches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `adversaire` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicile` tinyint(1) NOT NULL DEFAULT '1',
  `date_match` date NOT NULL,
  `score_mon_equipe` int NOT NULL DEFAULT '0',
  `score_adversaire` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matches`
--

INSERT INTO `matches` (`id`, `adversaire`, `domicile`, `date_match`, `score_mon_equipe`, `score_adversaire`, `created_at`) VALUES
(16, 'Crussol', 0, '2026-09-20', 0, 0, '2026-08-14 21:28:27');

-- --------------------------------------------------------

--
-- Structure de la table `match_joueurs`
--

DROP TABLE IF EXISTS `match_joueurs`;
CREATE TABLE IF NOT EXISTS `match_joueurs` (
  `match_id` int NOT NULL,
  `joueur_id` int NOT NULL,
  PRIMARY KEY (`match_id`,`joueur_id`),
  KEY `joueur_id` (`joueur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `match_joueurs`
--

INSERT INTO `match_joueurs` (`match_id`, `joueur_id`) VALUES
(16, 1),
(16, 2),
(16, 3),
(16, 4),
(16, 7),
(16, 8),
(16, 9),
(16, 11),
(16, 12),
(16, 14);

-- --------------------------------------------------------

--
-- Structure de la table `statistiques`
--

DROP TABLE IF EXISTS `statistiques`;
CREATE TABLE IF NOT EXISTS `statistiques` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` int NOT NULL,
  `joueur_id` int NOT NULL,
  `passes_decisives` int NOT NULL DEFAULT '0',
  `tirs_2pts_tentes` int NOT NULL DEFAULT '0',
  `tirs_2pts_reussis` int NOT NULL DEFAULT '0',
  `tirs_3pts_tentes` int NOT NULL DEFAULT '0',
  `tirs_3pts_reussis` int NOT NULL DEFAULT '0',
  `lancers_francs_tentes` int NOT NULL DEFAULT '0',
  `lancers_francs_reussis` int NOT NULL DEFAULT '0',
  `duels_defensifs_gagnes` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_stat_joueur_match` (`match_id`,`joueur_id`),
  KEY `joueur_id` (`joueur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `statistiques`
--

INSERT INTO `statistiques` (`id`, `match_id`, `joueur_id`, `passes_decisives`, `tirs_2pts_tentes`, `tirs_2pts_reussis`, `tirs_3pts_tentes`, `tirs_3pts_reussis`, `lancers_francs_tentes`, `lancers_francs_reussis`, `duels_defensifs_gagnes`) VALUES
(22, 16, 8, 0, 1, 1, 0, 0, 0, 0, 0),
(23, 16, 2, 1, 0, 0, 2, 1, 0, 0, 1),
(24, 16, 3, 1, 0, 0, 0, 0, 0, 0, 0),
(25, 16, 7, 0, 0, 0, 1, 1, 0, 0, 0),
(26, 16, 12, 1, 1, 1, 1, 1, 0, 0, 0),
(27, 16, 9, 1, 0, 0, 1, 0, 0, 0, 2),
(28, 16, 1, 1, 1, 1, 0, 0, 0, 0, 0),
(29, 16, 4, 1, 1, 1, 0, 0, 0, 0, 0),
(30, 16, 14, 0, 1, 0, 1, 1, 0, 0, 1),
(31, 16, 11, 0, 1, 0, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `statistiques_collectives`
--

DROP TABLE IF EXISTS `statistiques_collectives`;
CREATE TABLE IF NOT EXISTS `statistiques_collectives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` int NOT NULL,
  `quart_temps` tinyint NOT NULL,
  `points` int NOT NULL DEFAULT '0',
  `points_transition` int NOT NULL DEFAULT '0',
  `points_jeu_pose` int NOT NULL DEFAULT '0',
  `points_contre_attaque` int NOT NULL DEFAULT '0',
  `nb_possessions` int NOT NULL DEFAULT '0',
  `possessions_transition` int NOT NULL DEFAULT '0',
  `possessions_jeu_pose` int NOT NULL DEFAULT '0',
  `lancers_francs_tentes` int NOT NULL DEFAULT '0',
  `lancers_francs_reussis` int NOT NULL DEFAULT '0',
  `nb_contre_attaques` int NOT NULL DEFAULT '0',
  `nb_contre_attaques_reussies` int NOT NULL DEFAULT '0',
  `ballons_gagnes_defense` int NOT NULL DEFAULT '0',
  `rebonds_defensifs` int NOT NULL DEFAULT '0',
  `rebonds_offensifs_adversaires` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_stats_match_qt` (`match_id`,`quart_temps`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `statistiques_collectives`
--

INSERT INTO `statistiques_collectives` (`id`, `match_id`, `quart_temps`, `points`, `points_transition`, `points_jeu_pose`, `points_contre_attaque`, `nb_possessions`, `possessions_transition`, `possessions_jeu_pose`, `lancers_francs_tentes`, `lancers_francs_reussis`, `nb_contre_attaques`, `nb_contre_attaques_reussies`, `ballons_gagnes_defense`, `rebonds_defensifs`, `rebonds_offensifs_adversaires`) VALUES
(1, 16, 1, 6, 5, 0, 0, 2, 1, 1, 1, 1, 0, 0, 0, 2, 1),
(2, 16, 2, 5, 2, 3, 0, 3, 1, 2, 0, 0, 0, 0, 0, 6, 3),
(3, 16, 3, 5, 0, 5, 0, 4, 1, 3, 0, 0, 0, 0, 0, 3, 1),
(4, 16, 4, 8, 3, 5, 0, 3, 1, 2, 0, 0, 0, 0, 0, 3, 2);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `match_joueurs`
--
ALTER TABLE `match_joueurs`
  ADD CONSTRAINT `match_joueurs_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `match_joueurs_ibfk_2` FOREIGN KEY (`joueur_id`) REFERENCES `joueurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `statistiques`
--
ALTER TABLE `statistiques`
  ADD CONSTRAINT `statistiques_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `statistiques_ibfk_2` FOREIGN KEY (`joueur_id`) REFERENCES `joueurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `statistiques_collectives`
--
ALTER TABLE `statistiques_collectives`
  ADD CONSTRAINT `statistiques_collectives_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
