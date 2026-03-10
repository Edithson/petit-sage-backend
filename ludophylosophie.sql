-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 18 juil. 2025 à 17:01
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ludophylosophie`
--
CREATE DATABASE IF NOT EXISTS `ludophylosophie` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `ludophylosophie`;

-- --------------------------------------------------------

--
-- Structure de la table `badges`
--

DROP TABLE IF EXISTS `badges`;
CREATE TABLE IF NOT EXISTS `badges` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emoji` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 0xF09F8F86,
  `thematique_id` int NOT NULL,
  `nbr_min_point` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `badges`
--

INSERT INTO `badges` (`id`, `titre`, `description`, `emoji`, `thematique_id`, `nbr_min_point`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'La nature', 'La nature', '🏆', 2, 6, '2025-07-14 05:52:43', '2025-07-14 05:52:43', NULL),
(2, 'Super apprentie', 'Super apprentie', '😎', 2, 18, '2025-07-14 09:22:47', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `badge_users`
--

DROP TABLE IF EXISTS `badge_users`;
CREATE TABLE IF NOT EXISTS `badge_users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `badge_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `badge_users`
--

INSERT INTO `badge_users` (`id`, `user_id`, `badge_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-07-14 06:48:53', '2025-07-14 06:48:53'),
(4, 1, 2, '2025-07-14 08:30:28', '2025-07-14 08:30:28');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
CREATE TABLE IF NOT EXISTS `evaluations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `thematique_id` int NOT NULL,
  `user_id` int NOT NULL,
  `score` int NOT NULL,
  `temps` time DEFAULT NULL,
  `question` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evaluations`
--

INSERT INTO `evaluations` (`id`, `thematique_id`, `user_id`, `score`, `temps`, `question`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 4, '00:01:41', '[{\"explication\": null, \"question_id\": 1, \"intitule_text\": \"Nouvelle Question\", \"correct_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 2, \"intitule_text\": \"Auelle est la capitale de la france?\", \"correct_option\": {\"text\": \"Paris\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Marseil\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 05:50:28', '2025-07-14 05:50:28'),
(2, 2, 1, 5, '00:02:24', '[{\"explication\": null, \"question_id\": 1, \"intitule_text\": \"Nouvelle Question\", \"correct_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 06:48:53', '2025-07-14 06:48:53'),
(3, 2, 1, 5, '00:00:09', '[{\"explication\": null, \"question_id\": 4, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 08:20:50', '2025-07-14 08:20:50'),
(4, 2, 1, 1, '00:00:23', '[{\"explication\": null, \"question_id\": 6, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 3, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 5, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 08:21:22', '2025-07-14 08:21:22'),
(5, 2, 1, 0, '00:00:19', '[{\"explication\": null, \"question_id\": 2, \"intitule_text\": \"Auelle est la capitale de la france?\", \"correct_option\": {\"text\": \"Paris\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Marseil\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 1, \"intitule_text\": \"Nouvelle Question\", \"correct_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 4, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 08:21:53', '2025-07-14 08:21:53'),
(8, 2, 1, 5, '00:00:36', '[{\"explication\": null, \"question_id\": 2, \"intitule_text\": \"Auelle est la capitale de la france?\", \"correct_option\": {\"text\": \"Paris\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Marseil\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 08:30:28', '2025-07-14 08:30:28'),
(9, 2, 1, 4, '00:01:57', '[{\"explication\": null, \"question_id\": 1, \"intitule_text\": \"Nouvelle Question\", \"correct_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 3, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 09:02:21', '2025-07-14 09:02:21'),
(10, 2, 1, 5, '00:02:21', '[{\"explication\": null, \"question_id\": 5, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 09:04:57', '2025-07-14 09:04:57'),
(11, 2, 1, 3, '00:00:50', '[{\"explication\": null, \"question_id\": 5, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 6, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 1, \"intitule_text\": \"Nouvelle Question\", \"correct_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 09:25:01', '2025-07-14 09:25:01'),
(12, 2, 1, 4, '00:00:42', '[{\"explication\": null, \"question_id\": 1, \"intitule_text\": \"Nouvelle Question\", \"correct_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Nouvelle Question\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": null, \"question_id\": 5, \"intitule_text\": \"Auelle est la capitale du Cameraoun?\", \"correct_option\": {\"text\": \"Yaoundé\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Douala\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-14 09:40:06', '2025-07-14 09:40:06'),
(13, 16, 1, 8, '00:03:23', '[{\"explication\": \"Le vieux papier peut être recyclé pour fabriquer du nouveau papier, des livres ou des cahiers.\", \"question_id\": 23, \"intitule_text\": \"Que peut-on faire avec du papier journal utilisé ?\", \"correct_option\": {\"text\": \"Le recycler en nouveau papier\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Le brûler\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Le symbole de recyclage avec trois flèches qui tournent nous montre qu\'un objet peut être recyclé.\", \"question_id\": 22, \"intitule_text\": \"Quel symbole trouve-t-on sur les objets recyclables ?\", \"correct_option\": {\"mediaUrl\": \"/storage/questions/L02Ku0413H8bIBTjRZqp2h9XF6857jYmLd7lr29b.png\", \"isCorrect\": true, \"contentType\": \"media\", \"mediaSourceType\": \"file\", \"mediaDescription\": \"Trois flèches qui tournent\"}, \"selected_option\": {\"mediaUrl\": \"/storage/questions/qSvyF0eAsZxm5gXl3SpxazaVJMNvEagCBIfsKcxN.png\", \"isCorrect\": false, \"contentType\": \"media\", \"mediaSourceType\": \"file\", \"mediaDescription\": \"Etoile\"}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-18 11:38:05', '2025-07-18 11:38:05'),
(14, 16, 1, 6, '00:01:40', '[{\"explication\": \"Recycler, c\'est transformer les déchets en nouveaux objets utiles au lieu de les jeter.\", \"question_id\": 19, \"intitule_text\": \"Que veut dire \\\"recycler\\\" ?\", \"correct_option\": {\"text\": \"Transformer en quelque chose de nouveau\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Casser en morceaux\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Le symbole de recyclage avec trois flèches qui tournent nous montre qu\'un objet peut être recyclé.\", \"question_id\": 22, \"intitule_text\": \"Quel symbole trouve-t-on sur les objets recyclables ?\", \"correct_option\": {\"mediaUrl\": \"/storage/questions/L02Ku0413H8bIBTjRZqp2h9XF6857jYmLd7lr29b.png\", \"isCorrect\": true, \"contentType\": \"media\", \"mediaSourceType\": \"file\", \"mediaDescription\": \"Trois flèches qui tournent\"}, \"selected_option\": {\"mediaUrl\": \"/storage/questions/m1MmzGSBgrC3DwOlS9BPYPKCxWd765t7biuyYTzL.png\", \"isCorrect\": false, \"contentType\": \"media\", \"mediaSourceType\": \"file\", \"mediaDescription\": \"Rond\"}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Le verre peut être recyclé à l\'infini sans perdre sa qualité, c\'est un super-héros du recyclage !\", \"question_id\": 25, \"intitule_text\": \"Combien de fois peut-on recycler une bouteille en verre ?\", \"correct_option\": {\"text\": \"À l\'infini (plein de fois)\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"5 fois\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-18 11:42:08', '2025-07-18 11:42:08'),
(15, 16, 1, 1, '00:02:05', '[{\"explication\": \"Elles peuvent nourrir la terre !\", \"question_id\": 20, \"intitule_text\": \"Où doit-on jeter les épluchures de légumes ?\", \"correct_option\": {\"text\": \"Dans le compost\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Dans la poubelle jaune\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"En triant nos déchets, on aide à recycler et on protège notre planète en réduisant les déchets.\", \"question_id\": 24, \"intitule_text\": null, \"correct_option\": {\"text\": \"Pour protéger la planète\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Pour se fatiguer\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": \"/storage/questions/WYsCnpTRnJB10hUNnBwbkUx8HKzVKse90I4bLpFW.png\", \"intitule_media_description\": \"Pourquoi est-ce important de trier ses déchets ?\"}, {\"explication\": \"Les bouteilles en plastique vont dans la poubelle jaune pour être recyclées et transformées en nouveaux objets.\", \"question_id\": 18, \"intitule_text\": null, \"correct_option\": {\"text\": \"Dans la poubelle jaune\", \"mediaUrl\": null, \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Dans la poubelle normale\", \"mediaUrl\": null, \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": \"/storage/questions/icEGe3mKUJvg7pRGesVPEdLX9rcIfqZ2J5L0RrjH.jpg\", \"intitule_media_description\": \"Dans quelle poubelle doit-on jeter une bouteille en plastique ?\"}]', '2025-07-18 14:09:51', '2025-07-18 14:09:51'),
(16, 16, 1, 10, '00:02:36', '[]', '2025-07-18 14:13:22', '2025-07-18 14:13:22'),
(17, 16, 1, 9, '00:01:13', '[{\"explication\": \"Sans recyclage, les déchets s\'accumulent et polluent notre belle planète, ce qui rend tristes les animaux et les plantes.\", \"question_id\": 27, \"intitule_text\": \"Qu\'est-ce qui arrive si on ne recycle pas ?\", \"correct_option\": {\"text\": \"Il y a trop de déchets partout\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"La planète devient plus belle\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-18 14:20:57', '2025-07-18 14:20:57'),
(18, 16, 1, 0, '00:00:16', '[{\"explication\": \"Le vieux papier peut être recyclé pour fabriquer du nouveau papier, des livres ou des cahiers.\", \"question_id\": 23, \"intitule_text\": \"Que peut-on faire avec du papier journal utilisé ?\", \"correct_option\": {\"text\": \"Le recycler en nouveau papier\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Le brûler\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Sans recyclage, les déchets s\'accumulent et polluent notre belle planète, ce qui rend tristes les animaux et les plantes.\", \"question_id\": 27, \"intitule_text\": \"Qu\'est-ce qui arrive si on ne recycle pas ?\", \"correct_option\": {\"text\": \"Il y a trop de déchets partout\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Rien ne change\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Le symbole de recyclage avec trois flèches qui tournent nous montre qu\'un objet peut être recyclé.\", \"question_id\": 22, \"intitule_text\": \"Quel symbole trouve-t-on sur les objets recyclables ?\", \"correct_option\": {\"mediaUrl\": \"/storage/questions/L02Ku0413H8bIBTjRZqp2h9XF6857jYmLd7lr29b.png\", \"isCorrect\": true, \"contentType\": \"media\", \"mediaSourceType\": \"file\", \"mediaDescription\": \"Trois flèches qui tournent\"}, \"selected_option\": {\"mediaUrl\": \"/storage/questions/qSvyF0eAsZxm5gXl3SpxazaVJMNvEagCBIfsKcxN.png\", \"isCorrect\": false, \"contentType\": \"media\", \"mediaSourceType\": \"file\", \"mediaDescription\": \"Etoile\"}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-18 14:21:55', '2025-07-18 14:21:55'),
(19, 16, 1, 0, '00:00:18', '[{\"explication\": \"Le symbole de recyclage avec trois flèches qui tournent nous montre qu\'un objet peut être recyclé.\", \"question_id\": 22, \"intitule_text\": \"Quel symbole trouve-t-on sur les objets recyclables ?\", \"correct_option\": {\"mediaUrl\": \"/storage/questions/L02Ku0413H8bIBTjRZqp2h9XF6857jYmLd7lr29b.png\", \"isCorrect\": true, \"contentType\": \"media\", \"mediaSourceType\": \"file\", \"mediaDescription\": \"Trois flèches qui tournent\"}, \"selected_option\": {\"mediaUrl\": \"/storage/questions/m1MmzGSBgrC3DwOlS9BPYPKCxWd765t7biuyYTzL.png\", \"isCorrect\": false, \"contentType\": \"media\", \"mediaSourceType\": \"file\", \"mediaDescription\": \"Rond\"}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Au lieu de jeter, on peut réutiliser une bouteille pour en faire un pot à crayons ou un autre objet utile.\", \"question_id\": 21, \"intitule_text\": \"Qu\'est-ce qu\'on peut faire avec une bouteille en plastique vide ?\", \"correct_option\": {\"text\": \"En faire un pot à crayons\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"La jeter par terre\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Une canette en aluminium peut être fondue et transformée en nouvelle canette, c\'est magique !\", \"question_id\": 26, \"intitule_text\": \"Que devient une canette en aluminium après recyclage ?\", \"correct_option\": {\"text\": \"Une nouvelle canette\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Elle disparaît\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-18 14:24:12', '2025-07-18 14:24:12'),
(20, 16, 1, 1, '00:00:24', '[{\"explication\": \"Le verre peut être recyclé à l\'infini sans perdre sa qualité, c\'est un super-héros du recyclage !\", \"question_id\": 25, \"intitule_text\": \"Combien de fois peut-on recycler une bouteille en verre ?\", \"correct_option\": {\"text\": \"À l\'infini (plein de fois)\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Jamais\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Recycler, c\'est transformer les déchets en nouveaux objets utiles au lieu de les jeter.\", \"question_id\": 19, \"intitule_text\": \"Que veut dire \\\"recycler\\\" ?\", \"correct_option\": {\"text\": \"Transformer en quelque chose de nouveau\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Jeter à la poubelle\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Elles peuvent nourrir la terre !\", \"question_id\": 20, \"intitule_text\": \"Où doit-on jeter les épluchures de légumes ?\", \"correct_option\": {\"text\": \"Dans le compost\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Dans la poubelle jaune\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-18 14:27:14', '2025-07-18 14:27:14'),
(21, 16, 1, 0, '00:00:20', '[{\"explication\": \"Le verre peut être recyclé à l\'infini sans perdre sa qualité, c\'est un super-héros du recyclage !\", \"question_id\": 25, \"intitule_text\": \"Combien de fois peut-on recycler une bouteille en verre ?\", \"correct_option\": {\"text\": \"À l\'infini (plein de fois)\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"5 fois\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Les bouteilles en plastique vont dans la poubelle jaune pour être recyclées et transformées en nouveaux objets.\", \"question_id\": 18, \"intitule_text\": null, \"correct_option\": {\"text\": \"Dans la poubelle jaune\", \"mediaUrl\": null, \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Dans la poubelle normale\", \"mediaUrl\": null, \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": \"/storage/questions/APi5CoN6NbViMlHXy4jRFPrNYwPupAtMZENlgZlD.jpg\", \"intitule_media_description\": \"Dans quelle poubelle doit-on jeter une bouteille en plastique ?\"}, {\"explication\": \"Recycler, c\'est transformer les déchets en nouveaux objets utiles au lieu de les jeter.\", \"question_id\": 19, \"intitule_text\": \"Que veut dire \\\"recycler\\\" ?\", \"correct_option\": {\"text\": \"Transformer en quelque chose de nouveau\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Mettre dans un sac\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-18 14:28:18', '2025-07-18 14:28:18'),
(22, 16, 1, 7, '00:12:04', '[{\"explication\": \"Le verre peut être recyclé à l\'infini sans perdre sa qualité, c\'est un super-héros du recyclage !\", \"question_id\": 25, \"intitule_text\": \"Combien de fois peut-on recycler une bouteille en verre ?\", \"correct_option\": {\"text\": \"À l\'infini (plein de fois)\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Jamais\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Au lieu de jeter, on peut réutiliser une bouteille pour en faire un pot à crayons ou un autre objet utile.\", \"question_id\": 21, \"intitule_text\": \"Qu\'est-ce qu\'on peut faire avec une bouteille en plastique vide ?\", \"correct_option\": {\"text\": \"En faire un pot à crayons\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"La casser\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}, {\"explication\": \"Le vieux papier peut être recyclé pour fabriquer du nouveau papier, des livres ou des cahiers.\", \"question_id\": 23, \"intitule_text\": \"Que peut-on faire avec du papier journal utilisé ?\", \"correct_option\": {\"text\": \"Le recycler en nouveau papier\", \"isCorrect\": true, \"contentType\": \"text\", \"mediaDescription\": null}, \"selected_option\": {\"text\": \"Le brûler\", \"isCorrect\": false, \"contentType\": \"text\", \"mediaDescription\": null}, \"intitule_media_url\": null, \"intitule_media_description\": null}]', '2025-07-18 15:13:01', '2025-07-18 15:13:01');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(9, '0001_01_01_000000_create_users_table', 1),
(10, '0001_01_01_000001_create_cache_table', 1),
(11, '0001_01_01_000002_create_jobs_table', 1),
(12, '2025_07_08_032733_create_questions_table', 1),
(13, '2025_07_08_140228_create_thematiques_table', 1),
(14, '2025_07_09_070518_create_badges_table', 1),
(15, '2025_07_09_070540_create_badge_users_table', 1),
(16, '2025_07_09_070613_create_evaluations_table', 1),
(17, '2025_07_10_190730_create_personal_access_tokens_table', 2),
(19, '2025_07_18_031438_create_niveaux_table', 3);

-- --------------------------------------------------------

--
-- Structure de la table `niveaux`
--

DROP TABLE IF EXISTS `niveaux`;
CREATE TABLE IF NOT EXISTS `niveaux` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero` int NOT NULL,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `niveaux`
--

INSERT INTO `niveaux` (`id`, `numero`, `nom`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Au début', 'Au début', NULL, NULL, NULL),
(2, 2, 'Je prends mes marques', 'Je prends mes marques', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', 'a37860378fb1f97ad13a0ca586957e63d6699fa75f062804c2eda37edcafff39', '[\"*\"]', NULL, NULL, '2025-07-10 18:07:52', '2025-07-10 18:07:52'),
(2, 'App\\Models\\User', 4, 'auth_token', '6b0251c08f7e5588033d5caa0ecfa66634b799e16879901986a65d957708e09d', '[\"*\"]', NULL, NULL, '2025-07-10 18:08:28', '2025-07-10 18:08:28'),
(3, 'App\\Models\\User', 5, 'auth_token', '668909c80bb069c5ea25c37dcbf9e46565e70fb0f8410a18a8cee9fa8f6d7725', '[\"*\"]', NULL, NULL, '2025-07-10 18:12:21', '2025-07-10 18:12:21'),
(4, 'App\\Models\\User', 1, 'auth_token', 'd5073e3c05050647a7f80b9039408bc6b7a5b003553dc94f48ed4e7d3ba21961', '[\"*\"]', NULL, NULL, '2025-07-10 21:44:48', '2025-07-10 21:44:48'),
(5, 'App\\Models\\User', 1, 'auth_token', '7053afe3a0057b91e04f39ca07e46314832219a0a08b59fb38f388e42337f277', '[\"*\"]', '2025-07-13 06:27:50', NULL, '2025-07-13 06:25:42', '2025-07-13 06:27:50'),
(6, 'App\\Models\\User', 1, 'auth_token', '4759b0a6963ee6d0c32e22017924506caf4063bf8ce205c19b57899b6277d16c', '[\"*\"]', '2025-07-13 06:39:12', NULL, '2025-07-13 06:30:25', '2025-07-13 06:39:12'),
(7, 'App\\Models\\User', 1, 'auth_token', '195d57657a79bd31d9ceb913ff257b05844784b504efc8cbdefc969270f83794', '[\"*\"]', '2025-07-13 06:41:12', NULL, '2025-07-13 06:39:30', '2025-07-13 06:41:12'),
(8, 'App\\Models\\User', 1, 'auth_token', 'b5be73a75cd9c39e8424af9aca0e3f8a7fb32118942b9e618645aff362e4fac5', '[\"*\"]', '2025-07-13 06:41:39', NULL, '2025-07-13 06:41:17', '2025-07-13 06:41:39'),
(9, 'App\\Models\\User', 1, 'auth_token', '0a0a2c2ea48b817c8f8da0dde29196d8ea07113aa6983fa819f475dc7fb702f1', '[\"*\"]', '2025-07-13 06:42:13', NULL, '2025-07-13 06:41:42', '2025-07-13 06:42:13'),
(10, 'App\\Models\\User', 1, 'auth_token', '4e0d5fd26ef07f33db7671648f9addc5fac34cb6c980706d0c2988d4be57495b', '[\"*\"]', '2025-07-13 06:49:50', NULL, '2025-07-13 06:42:15', '2025-07-13 06:49:50'),
(11, 'App\\Models\\User', 1, 'auth_token', '873565de6c4a10f83aba46200058b504469ba987041480cc8a3acd90e8726cd3', '[\"*\"]', '2025-07-13 06:58:54', NULL, '2025-07-13 06:55:26', '2025-07-13 06:58:54'),
(12, 'App\\Models\\User', 1, 'auth_token', '4d709eccc1c0831ae530a829c8e20bc720c398d3f577d6923e639789fa9a3578', '[\"*\"]', '2025-07-13 07:00:01', NULL, '2025-07-13 06:59:10', '2025-07-13 07:00:01'),
(13, 'App\\Models\\User', 1, 'auth_token', 'f7cb429dbf62b5a334261fcd58cd75f9e07449b26a15e14c7de7798d522e675c', '[\"*\"]', '2025-07-13 07:07:55', NULL, '2025-07-13 07:00:11', '2025-07-13 07:07:55'),
(14, 'App\\Models\\User', 1, 'auth_token', '30b4c4b333e2ec125ccadf418c8c76dcfa9e0b2887d058cde72d6adc6023cdc5', '[\"*\"]', '2025-07-13 07:08:15', NULL, '2025-07-13 07:08:02', '2025-07-13 07:08:15'),
(15, 'App\\Models\\User', 1, 'auth_token', '5e8e86047fbe181f0fea6e982763f2fdc88340ef9dc1652dc06e85123ee8cde3', '[\"*\"]', '2025-07-13 07:09:20', NULL, '2025-07-13 07:08:19', '2025-07-13 07:09:20'),
(16, 'App\\Models\\User', 1, 'auth_token', '0f8c100482947d6e7b61fcd9b05f0c3fe04c3f21b205cc67b4300ce9e33bc0a4', '[\"*\"]', NULL, NULL, '2025-07-13 07:09:24', '2025-07-13 07:09:24');

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

DROP TABLE IF EXISTS `questions`;
CREATE TABLE IF NOT EXISTS `questions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `intitule_text` text COLLATE utf8mb4_unicode_ci,
  `intitule_media_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intitule_media_description` text COLLATE utf8mb4_unicode_ci,
  `thematique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `degre_difficulte` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_reponse` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `explication` text COLLATE utf8mb4_unicode_ci,
  `indice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reponses` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `intitule_text`, `intitule_media_url`, `intitule_media_description`, `thematique_id`, `degre_difficulte`, `type_reponse`, `explication`, `indice`, `reponses`, `created_at`, `updated_at`, `deleted_at`) VALUES
(18, NULL, '/storage/questions/APi5CoN6NbViMlHXy4jRFPrNYwPupAtMZENlgZlD.jpg', 'Dans quelle poubelle doit-on jeter une bouteille en plastique ?', '16', '2', 'unique', 'Les bouteilles en plastique vont dans la poubelle jaune pour être recyclées et transformées en nouveaux objets.', 'C\'est la couleur du soleil !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle normale\\\",\\\"mediaUrl\\\":null},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle jaune\\\",\\\"mediaUrl\\\":null},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle verte\\\",\\\"mediaUrl\\\":null},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Par terre\\\",\\\"mediaUrl\\\":null}]\"', '2025-07-18 11:06:18', '2025-07-18 14:10:33', NULL),
(19, 'Que veut dire \"recycler\" ?', NULL, NULL, '16', '1', 'unique', 'Recycler, c\'est transformer les déchets en nouveaux objets utiles au lieu de les jeter.', 'C\'est comme donner une nouvelle vie aux objets !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Jeter \\\\u00e0 la poubelle\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Casser en morceaux\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Transformer en quelque chose de nouveau\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Mettre dans un sac\\\"}]\"', '2025-07-18 11:10:09', '2025-07-18 11:10:09', NULL),
(20, 'Où doit-on jeter les épluchures de légumes ?', NULL, NULL, '16', '1', 'unique', 'Elles peuvent nourrir la terre !', 'Elles peuvent nourrir la terre !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans le compost\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle jaune\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la rue\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle bleue\\\"}]\"', '2025-07-18 11:12:03', '2025-07-18 11:12:03', NULL),
(21, 'Qu\'est-ce qu\'on peut faire avec une bouteille en plastique vide ?', NULL, NULL, '16', '2', 'unique', 'Au lieu de jeter, on peut réutiliser une bouteille pour en faire un pot à crayons ou un autre objet utile.', 'On peut la transformer en quelque chose d\'utile !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"La jeter par terre\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"La casser\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"La manger\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"En faire un pot \\\\u00e0 crayons\\\"}]\"', '2025-07-18 11:13:45', '2025-07-18 11:13:45', NULL),
(22, 'Quel symbole trouve-t-on sur les objets recyclables ?', NULL, NULL, '16', '2', 'unique', 'Le symbole de recyclage avec trois flèches qui tournent nous montre qu\'un objet peut être recyclé.', 'Elles forment un cercle et se suivent !', '\"[{\\\"contentType\\\":\\\"media\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":\\\"Etoile\\\",\\\"mediaSourceType\\\":\\\"file\\\",\\\"mediaUrl\\\":\\\"\\\\/storage\\\\/questions\\\\/qSvyF0eAsZxm5gXl3SpxazaVJMNvEagCBIfsKcxN.png\\\"},{\\\"contentType\\\":\\\"media\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":\\\"Trois fl\\\\u00e8ches qui tournent\\\",\\\"mediaSourceType\\\":\\\"file\\\",\\\"mediaUrl\\\":\\\"\\\\/storage\\\\/questions\\\\/L02Ku0413H8bIBTjRZqp2h9XF6857jYmLd7lr29b.png\\\"},{\\\"contentType\\\":\\\"media\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":\\\"Rond\\\",\\\"mediaSourceType\\\":\\\"file\\\",\\\"mediaUrl\\\":\\\"\\\\/storage\\\\/questions\\\\/m1MmzGSBgrC3DwOlS9BPYPKCxWd765t7biuyYTzL.png\\\"}]\"', '2025-07-18 11:19:47', '2025-07-18 11:19:47', NULL),
(23, 'Que peut-on faire avec du papier journal utilisé ?', NULL, NULL, '16', '2', 'unique', 'Le vieux papier peut être recyclé pour fabriquer du nouveau papier, des livres ou des cahiers.', 'Il peut redevenir du papier tout neuf !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Le br\\\\u00fbler\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Le manger\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Le mettre sous l\'oreiller\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Le recycler en nouveau papier\\\"}]\"', '2025-07-18 11:22:21', '2025-07-18 11:22:21', NULL),
(24, NULL, '/storage/questions/WYsCnpTRnJB10hUNnBwbkUx8HKzVKse90I4bLpFW.png', 'Pourquoi est-ce important de trier ses déchets ?', '16', '2', 'unique', 'En triant nos déchets, on aide à recycler et on protège notre planète en réduisant les déchets.', 'C\'est pour prendre soin de notre belle Terre !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour faire joli\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour faire du bruit\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour prot\\\\u00e9ger la plan\\\\u00e8te\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour se fatiguer\\\"}]\"', '2025-07-18 11:26:33', '2025-07-18 11:26:33', NULL),
(25, 'Combien de fois peut-on recycler une bouteille en verre ?', NULL, NULL, '16', '3', 'unique', 'Le verre peut être recyclé à l\'infini sans perdre sa qualité, c\'est un super-héros du recyclage !', 'C\'est comme si elle avait une vie éternelle !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"\\\\u00c0 l\'infini (plein de fois)\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"5 fois\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Jamais\\\"}]\"', '2025-07-18 11:28:10', '2025-07-18 11:28:10', NULL),
(26, 'Que devient une canette en aluminium après recyclage ?', NULL, NULL, '16', '3', 'unique', 'Une canette en aluminium peut être fondue et transformée en nouvelle canette, c\'est magique !', 'Elle peut redevenir exactement la même chose !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Elle dispara\\\\u00eet\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Une nouvelle canette\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Un g\\\\u00e2teau\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Une fleur\\\"}]\"', '2025-07-18 11:29:49', '2025-07-18 11:29:49', NULL),
(27, 'Qu\'est-ce qui arrive si on ne recycle pas ?', NULL, NULL, '16', '3', 'unique', 'Sans recyclage, les déchets s\'accumulent et polluent notre belle planète, ce qui rend tristes les animaux et les plantes.', 'Imagine ta chambre si tu ne ranges jamais tes jouets !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Il y a trop de d\\\\u00e9chets partout\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"La plan\\\\u00e8te devient plus belle\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Rien ne change\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Les animaux sont plus heureux\\\"}]\"', '2025-07-18 11:31:36', '2025-07-18 11:31:36', NULL),
(28, 'Quelle est la capitale du Cameroun?', NULL, NULL, '2', '2', 'unique', NULL, NULL, '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Yaound\\\\u00e9\\\",\\\"mediaUrl\\\":null},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Douala\\\",\\\"mediaUrl\\\":null}]\"', '2025-07-18 12:39:22', '2025-07-18 12:40:59', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('G00ba1FgKv7Fh7yQpt9gVWnB0tcVLyEhg52n28vm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 Edg/132.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRlpobE5nVDY2UTZGTzlXdHNGVGhKeVloSkNySXprbURtbFprU0EyUyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1752719111);

-- --------------------------------------------------------

--
-- Structure de la table `thematiques`
--

DROP TABLE IF EXISTS `thematiques`;
CREATE TABLE IF NOT EXISTS `thematiques` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `media_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `niveau_id` int DEFAULT '1',
  `nbr_min_point` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `thematiques`
--

INSERT INTO `thematiques` (`id`, `name`, `description`, `parent_id`, `media_url`, `media_description`, `niveau_id`, `nbr_min_point`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Mon environnement', 'l\'ensemble des conditions naturelles (physiques, chimiques, biologiques) et culturelles (sociologiques) susceptibles d’agir sur les organismes vivants et les activités humaines.', NULL, '/storage/thematique/CAC54S9jMFkD4GzQSBaAeKmIhUdgmpAmqmFVoWcm.jpg', 'Mon environnement', 1, 23, '2025-07-14 05:18:36', '2025-07-18 10:33:10', NULL),
(2, 'Ami(e)s de la nature', 'Ami(e)s de la nature', 1, '/storage/thematique/xdWtydDOKEJP4Xb68BRz8j1PXmwWK7a6oF2v14zx.png', NULL, 1, NULL, '2025-07-14 05:19:08', '2025-07-14 05:19:08', NULL),
(3, 'Mamamiya', 'Mamamiya', 1, NULL, NULL, 1, NULL, '2025-07-17 14:58:46', '2025-07-18 10:33:43', '2025-07-18 10:33:43'),
(4, 'Echologie', NULL, NULL, NULL, NULL, 1, NULL, '2025-07-17 15:52:11', '2025-07-18 01:28:33', '2025-07-18 01:28:33'),
(5, 'Famille', NULL, NULL, NULL, NULL, 1, NULL, '2025-07-17 15:52:26', '2025-07-18 01:53:56', '2025-07-18 01:53:56'),
(6, 'Politesse', 'Politesse', NULL, NULL, NULL, 2, 20, '2025-07-18 01:56:03', '2025-07-18 09:50:19', '2025-07-18 09:50:19'),
(7, 'Nature', 'Nature', NULL, NULL, NULL, 2, 35, '2025-07-18 02:37:57', '2025-07-18 03:09:42', '2025-07-18 03:09:42'),
(8, 'Arbre', 'Arbre', 7, NULL, NULL, 1, NULL, '2025-07-18 02:41:25', '2025-07-18 03:09:42', '2025-07-18 03:09:42'),
(9, 'Admin', 'Admin', 7, NULL, NULL, 1, NULL, '2025-07-18 02:44:07', '2025-07-18 02:50:35', '2025-07-18 02:50:35'),
(10, 'Admin', 'Admin', 7, NULL, NULL, 1, NULL, '2025-07-18 03:01:56', '2025-07-18 03:05:50', '2025-07-18 03:05:50'),
(11, 'Admin', NULL, 7, NULL, NULL, 1, NULL, '2025-07-18 03:06:04', '2025-07-18 03:06:27', '2025-07-18 03:06:27'),
(12, 'Mamamiya', NULL, NULL, NULL, NULL, 1, 20, '2025-07-18 03:26:03', '2025-07-18 03:45:54', '2025-07-18 03:45:54'),
(13, 'eric le retour 2', NULL, NULL, NULL, NULL, 1, 20, '2025-07-18 03:38:20', '2025-07-18 03:40:33', '2025-07-18 03:40:33'),
(14, 'banane', NULL, NULL, NULL, NULL, 1, 20, '2025-07-18 03:39:58', '2025-07-18 03:49:06', '2025-07-18 03:49:06'),
(15, 'banane', NULL, NULL, '/storage/thematique/NjeLCzN2bJZ441oOStV4Jlmw4iqp05Ss6j9c6GHt.png', NULL, 1, NULL, '2025-07-18 03:53:00', '2025-07-18 10:30:49', '2025-07-18 10:30:49'),
(16, 'Recyclage', 'Le recyclage permet la réduction du volume de déchets ainsi que la préservation des ressources naturelles', 1, NULL, NULL, 1, NULL, '2025-07-18 10:36:06', '2025-07-18 10:36:06', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_google` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_id` int NOT NULL DEFAULT '1',
  `sexe` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `telephone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profil` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'profil/profil.png',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `code`, `id_google`, `name`, `email`, `type_id`, `sexe`, `age`, `telephone`, `profil`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '094e69b8-290a-4cd9-8286-018f6833f568', NULL, 'moafogaus@gmail.com', 'moafogaus@gmail.com', 3, NULL, NULL, '658995265', 'profil/profil.png', NULL, '$2y$12$ibY.ITV6qqk3GV5HOlosv.TDejgPPkcV.7t0Bg58kHJL7cJDHB/qW', NULL, '2025-07-10 15:59:19', '2025-07-10 15:59:19', NULL),
(5, 'bd46cbfa-eb2c-4df0-a276-928cd426961c', NULL, 'GT', 'moafogaus@gmail.cm', 1, NULL, NULL, NULL, 'profil/profil.png', NULL, '$2y$12$u6fRs0mxe0aXvU0u0D6fle0uHOiWbyoDA1jya5IWG14veN7neZxJ6', NULL, '2025-07-10 18:12:21', '2025-07-10 18:12:21', NULL),
(4, 'a61609a5-6869-4f58-8d26-ad461de2056c', NULL, 'AZERTY', 'moafogaus@gmail.fr', 1, NULL, NULL, NULL, 'profil/profil.png', NULL, '$2y$12$Mkv0a2Ra.F71lkMws0Qo1OICc4xUPrKxYxYEyBsTNT6XSIsWZlMxm', NULL, '2025-07-10 18:08:28', '2025-07-10 18:08:28', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
