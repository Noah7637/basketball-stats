-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 25 sep. 2026 à 17:21
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
-- Structure de la table `actions_log`
--

DROP TABLE IF EXISTS `actions_log`;
CREATE TABLE IF NOT EXISTS `actions_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `match_id` int NOT NULL,
  `joueur_id` int DEFAULT NULL,
  `quart_temps` tinyint NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reussi` tinyint DEFAULT NULL,
  `type_possession` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valeur_temps` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  KEY `joueur_id` (`joueur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `joueurs`
--

DROP TABLE IF EXISTS `joueurs`;
CREATE TABLE IF NOT EXISTS `joueurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int DEFAULT NULL,
  `poste` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `matches`
--

DROP TABLE IF EXISTS `matches`;
CREATE TABLE IF NOT EXISTS `matches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `adversaire` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicile` tinyint(1) NOT NULL DEFAULT '1',
  `date_match` date NOT NULL,
  `score_mon_equipe` int NOT NULL DEFAULT '0',
  `score_adversaire` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `minutes_jouees` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_stat_joueur_match` (`match_id`,`joueur_id`),
  KEY `joueur_id` (`joueur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_equipe` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actions_log`
--
ALTER TABLE `actions_log`
  ADD CONSTRAINT `actions_log_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `actions_log_ibfk_2` FOREIGN KEY (`joueur_id`) REFERENCES `joueurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `joueurs`
--
ALTER TABLE `joueurs`
  ADD CONSTRAINT `joueurs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

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
