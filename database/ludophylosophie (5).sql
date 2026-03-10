-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 13 nov. 2025 à 09:12
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

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

-- --------------------------------------------------------

--
-- Structure de la table `badges`
--

CREATE TABLE `badges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titre` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `emoji` varchar(191) DEFAULT '?',
  `thematique_id` bigint(20) UNSIGNED NOT NULL,
  `nbr_min_point` int(11) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `last_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `badges`
--

INSERT INTO `badges` (`id`, `titre`, `description`, `emoji`, `thematique_id`, `nbr_min_point`, `created_by`, `last_updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ami de la nature', '\"Ami de la nature\" peut désigner le mouvement associatif international des Amis de la nature, fondé en 1895, qui promeut les loisirs de plein air et la défense de l\'environnement, ou une personne qui a une forte affinité avec la nature et s\'engage pour sa préservation.', '🌍', 7, 8, 1, 4, 1, '2025-11-05 13:41:35', '2025-11-05 13:45:42', '2025-11-05 13:45:42');

-- --------------------------------------------------------

--
-- Structure de la table `badge_users`
--

CREATE TABLE `badge_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `badge_id` bigint(20) UNSIGNED NOT NULL,
  `profil_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `badge_users`
--

INSERT INTO `badge_users` (`id`, `user_id`, `badge_id`, `profil_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, '2025-10-14 07:53:39', '2025-10-14 07:53:39'),
(2, 2, 2, NULL, '2025-10-14 07:53:39', '2025-10-14 07:53:39');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thematique_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `profil_id` bigint(20) UNSIGNED DEFAULT NULL,
  `partie_id` bigint(20) UNSIGNED NOT NULL,
  `score` int(11) NOT NULL,
  `temps` time NOT NULL,
  `question` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`question`)),
  `max_score` int(11) DEFAULT 10,
  `drawing_data` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evaluations`
--

INSERT INTO `evaluations` (`id`, `thematique_id`, `user_id`, `profil_id`, `partie_id`, `score`, `temps`, `question`, `max_score`, `drawing_data`, `created_at`, `updated_at`) VALUES
(1, 2, 2, NULL, 3, 10, '00:01:42', '[{\"question_id\":3,\"selected_option\":{\"text\":\"Être en retard c\'est lorsque tu viens avant l\'heure\",\"isCorrect\":false,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"correct_option\":{\"text\":\"Être en retard c\'est lorsque tu viens après l\'heure\",\"isCorrect\":true,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"intitule_text\":\"Qu\'est-ce qu\'être en retard ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":null}]', 10, 'drawings/2025/10/drawing_2_3_1760435619.png', '2025-10-14 07:53:39', '2025-10-14 07:53:39'),
(2, 7, 1, 2, 4, 9, '00:02:25', '[{\"question_id\":29,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"text\":\"La jeter par terre\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"text\":\"En faire un pot à crayons\"},\"intitule_text\":\"Qu\'est-ce qu\'on peut faire avec une bouteille en plastique vide ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":\"Au lieu de jeter, on peut réutiliser une bouteille pour en faire un pot à crayons ou un autre objet utile.\"}]', 10, 'drawings/2025/11/drawing_1_4_1762925826.png', '2025-11-12 04:37:06', '2025-11-12 04:37:06'),
(3, 2, 1, 2, 3, 8, '00:02:09', '[{\"question_id\":2,\"selected_option\":{\"text\":\"Pourquoi la maison de la tortue est si loin qu\'elle n\'arrive à vite?\",\"isCorrect\":false,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"correct_option\":{\"text\":\"Pourquoi la tortue ne se réveille pas vite ?\",\"isCorrect\":true,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"intitule_text\":\"Peux-tu poser une question par rapport à la tortue ou à la panthère dans l\'histoire racontée dans la vidéo ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":null},{\"question_id\":11,\"selected_option\":{\"text\":\"C\'est faire tout ce qu\'on veut de notre temps à n\'importe quel moment\",\"isCorrect\":false,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"correct_option\":{\"text\":\"C\'est organiser son temps de manière à ne pas être en retard\",\"isCorrect\":true,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"intitule_text\":\"Pour toi, qu\'est-ce que bien gérer son temps ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":null}]', 10, 'drawings/2025/11/drawing_1_3_1762926285.png', '2025-11-12 04:44:45', '2025-11-12 04:44:45'),
(4, 2, 1, 3, 3, 8, '00:02:03', '[{\"question_id\":4,\"selected_option\":{\"text\":\"Être en retard c\'est lorsque tu viens après l\'heure\",\"isCorrect\":false,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"correct_option\":{\"text\":\"Être en retard c\'est lorsque tu respectes l\'heure\",\"isCorrect\":true,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"intitule_text\":\"Qu\'est-ce qu\'être ponctuel ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":null},{\"question_id\":1,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"mediaUrl\":null,\"text\":\"Son problème c\'est parce qu\'elle marche lentement\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"mediaUrl\":null,\"text\":\"Son problème est qu\'elle est toujours en retard\"},\"intitule_text\":\"Alors mes amies, dans la vidéo que vous venez regarder, quel est le problème de notre petite tortue Koulou par rapport au léopard ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":\"RAS\"}]', 10, 'drawings/2025/11/drawing_1_3_1762959580.png', '2025-11-12 13:59:40', '2025-11-12 13:59:40'),
(5, 8, 1, 2, 5, 3, '00:00:37', '[]', 3, 'drawings/2025/11/drawing_1_5_1762963006.png', '2025-11-12 14:56:46', '2025-11-12 14:56:46'),
(6, 8, 1, 2, 5, 3, '00:00:37', '[]', 3, 'drawings/2025/11/drawing_1_5_1762963006.png', '2025-10-14 14:56:46', '2025-10-14 14:56:46'),
(7, 2, 1, 3, 3, 8, '00:02:03', '[{\"question_id\":4,\"selected_option\":{\"text\":\"Être en retard c\'est lorsque tu viens après l\'heure\",\"isCorrect\":false,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"correct_option\":{\"text\":\"Être en retard c\'est lorsque tu respectes l\'heure\",\"isCorrect\":true,\"mediaUrl\":null,\"contentType\":\"text\",\"mediaDescription\":null},\"intitule_text\":\"Qu\'est-ce qu\'être ponctuel ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":null},{\"question_id\":1,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"mediaUrl\":null,\"text\":\"Son problème c\'est parce qu\'elle marche lentement\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"mediaUrl\":null,\"text\":\"Son problème est qu\'elle est toujours en retard\"},\"intitule_text\":\"Alors mes amies, dans la vidéo que vous venez regarder, quel est le problème de notre petite tortue Koulou par rapport au léopard ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":\"RAS\"}]', 10, 'drawings/2025/11/drawing_1_3_1762959580.png', '2025-09-12 13:59:40', '2025-09-12 13:59:40'),
(8, 7, 1, 2, 4, 9, '00:02:25', '[{\"question_id\":29,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"text\":\"La jeter par terre\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"text\":\"En faire un pot à crayons\"},\"intitule_text\":\"Qu\'est-ce qu\'on peut faire avec une bouteille en plastique vide ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":\"Au lieu de jeter, on peut réutiliser une bouteille pour en faire un pot à crayons ou un autre objet utile.\"}]', 10, 'drawings/2025/11/drawing_1_4_1762925826.png', '2025-09-12 04:37:06', '2025-09-12 04:37:06'),
(9, 7, 1, 2, 4, 9, '00:02:25', '[{\"question_id\":29,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"text\":\"La jeter par terre\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"text\":\"En faire un pot à crayons\"},\"intitule_text\":\"Qu\'est-ce qu\'on peut faire avec une bouteille en plastique vide ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":\"Au lieu de jeter, on peut réutiliser une bouteille pour en faire un pot à crayons ou un autre objet utile.\"}]', 10, 'drawings/2025/11/drawing_1_4_1762925826.png', '2025-09-15 04:37:06', '2025-09-15 04:37:06'),
(10, 7, 1, 2, 4, 7, '00:01:49', '[{\"question_id\":28,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"text\":\"Dans la rue\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"text\":\"Dans le compost\"},\"intitule_text\":\"Où doit-on jeter les épluchures de légumes ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":\"Elles peuvent nourrir la terre !\"},{\"question_id\":33,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"text\":\"5 fois\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"text\":\"À l\'infini (plein de fois)\"},\"intitule_text\":\"Combien de fois peut-on recycler une bouteille en verre ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":\"Le verre peut être recyclé à l\'infini sans perdre sa qualité, c\'est un super-héros du recyclage !\"},{\"question_id\":29,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"text\":\"La jeter par terre\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"text\":\"En faire un pot à crayons\"},\"intitule_text\":\"Qu\'est-ce qu\'on peut faire avec une bouteille en plastique vide ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":\"Au lieu de jeter, on peut réutiliser une bouteille pour en faire un pot à crayons ou un autre objet utile.\"}]', 10, 'drawings/2025/11/drawing_1_4_1763021027.png', '2025-11-13 07:03:47', '2025-11-13 07:03:47'),
(11, 8, 1, 2, 5, 2, '00:01:35', '[{\"question_id\":38,\"selected_option\":{\"contentType\":\"text\",\"isCorrect\":false,\"mediaDescription\":null,\"text\":\"Pour qu\'ils soient obligé eux aussi de partager en retour\"},\"correct_option\":{\"contentType\":\"text\",\"isCorrect\":true,\"mediaDescription\":null,\"text\":\"Pour avoir de bonne relation avec son entourage\"},\"intitule_text\":\"Pourquoi doit-on partager avec les autres ?\",\"intitule_media_url\":null,\"intitule_media_description\":null,\"explication\":null}]', 3, 'drawings/2025/11/drawing_1_5_1763021187.png', '2025-11-13 07:06:27', '2025-11-13 07:06:27');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0000_07_08_030438_create_niveaux_table', 1),
(2, '0000_10_11_174551_create_types_table', 1),
(3, '0001_010_21_140427_create_profils_table', 1),
(4, '0001_01_01_000000_create_users_table', 1),
(5, '0001_01_01_000001_create_cache_table', 1),
(6, '0001_01_01_000002_create_jobs_table', 1),
(7, '2025_07_08_031228_create_thematiques_table', 1),
(8, '2025_07_08_031575_create_parties_table', 1),
(9, '2025_07_08_032733_create_questions_table', 1),
(10, '2025_07_09_070518_create_badges_table', 1),
(11, '2025_07_09_070540_create_badge_users_table', 1),
(12, '2025_07_09_070613_create_evaluations_table', 1),
(13, '2025_07_10_190730_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Structure de la table `niveaux`
--

CREATE TABLE `niveaux` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero` int(11) NOT NULL,
  `nom` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `last_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `niveaux`
--

INSERT INTO `niveaux` (`id`, `numero`, `nom`, `description`, `created_by`, `last_updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Niveau de base', 'Niveau 1 de base créé par défaut pour toutes les thématiques.', NULL, NULL, NULL, '2025-10-04 17:46:46', '2025-10-08 07:45:55', NULL),
(2, 2, 'FOWA', 'fffggvgv', NULL, NULL, NULL, '2025-10-08 07:13:28', '2025-10-08 07:14:26', '2025-10-08 07:14:26'),
(3, 2, 'On charbone', 'iyugytf', NULL, NULL, NULL, '2025-10-08 07:21:58', '2025-10-08 08:12:23', '2025-10-08 08:12:23'),
(4, 2, 'FOWA', 'kds,v', NULL, NULL, NULL, '2025-10-08 08:13:48', '2025-10-08 08:14:04', '2025-10-08 08:14:04'),
(5, 2, 'Calvin', 'fsdlkn', NULL, NULL, NULL, '2025-10-08 08:13:57', '2025-10-08 08:18:02', '2025-10-08 08:18:02'),
(6, 2, 'Rober', 'rgb2', NULL, NULL, NULL, '2025-10-08 08:17:54', '2025-10-08 08:18:51', '2025-10-08 08:18:51'),
(7, 2, 'x', 'x', NULL, NULL, NULL, '2025-10-08 08:18:23', '2025-10-08 08:20:51', '2025-10-08 08:20:51'),
(8, 2, 'Jeremy', 'jerrar', NULL, NULL, NULL, '2025-10-08 08:20:35', '2025-10-08 08:22:12', '2025-10-08 08:22:12'),
(9, 2, 'parfum de pacouraban', 'parfum de pacouraban', NULL, NULL, NULL, '2025-10-08 08:22:05', '2025-10-08 08:23:09', '2025-10-08 08:23:09'),
(10, 2, 'Salmonelle', 'flsq', NULL, NULL, NULL, '2025-10-08 08:23:00', '2025-10-08 08:23:50', '2025-10-08 08:23:50'),
(11, 2, 'x', 'x', NULL, NULL, NULL, '2025-10-08 08:23:45', '2025-10-08 08:25:50', '2025-10-08 08:25:50'),
(12, 2, 'y', 'y', NULL, NULL, NULL, '2025-10-08 08:25:44', '2025-10-08 08:28:01', '2025-10-08 08:28:01'),
(13, 2, 'bonbon', 'bonbon', NULL, NULL, NULL, '2025-10-08 08:27:49', '2025-10-08 08:29:24', '2025-10-08 08:29:24'),
(14, 2, 'pomme', 'pomme', NULL, NULL, NULL, '2025-10-08 08:29:19', '2025-10-08 08:31:06', '2025-10-08 08:31:06'),
(15, 2, 'riz', 'riz', NULL, NULL, NULL, '2025-10-08 08:31:00', '2025-10-08 08:31:52', '2025-10-08 08:31:52'),
(16, 2, 'On charbone', 'sdf', NULL, NULL, NULL, '2025-10-08 08:31:48', '2025-10-08 08:33:53', '2025-10-08 08:33:53'),
(17, 2, 'Client user', 'Client user', NULL, NULL, NULL, '2025-10-08 08:33:45', '2025-10-08 08:35:14', '2025-10-08 08:35:14'),
(18, 2, 'x', 'x', NULL, NULL, NULL, '2025-10-08 08:35:07', '2025-10-08 08:35:14', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `parties`
--

CREATE TABLE `parties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `thematique_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `parties`
--

INSERT INTO `parties` (`id`, `numero`, `name`, `description`, `thematique_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Le petit voyage de Koulou la tortue', 'Lorsqu\'on voyage on tient compte du temps et de l\'espace, c\'est-à-dire de l\'heure de départ ou d\'arrivée, de la durée du voyage... notre ami la tortue n\'a pas l\'air de le savoir !', 1, '2025-10-04 17:46:46', '2025-10-04 17:46:46', NULL),
(2, 2, 'La petite tête de mule de Fari l\'âne', 'La têtutesse qualifie une personne qui refuse d\'obéir à une règle/loi pour ne que chercher à obtenir de manière déterminer sa propre liberté. C\'est le cas de notre ami la Fari.', 1, '2025-10-04 17:46:46', '2025-10-04 17:46:46', NULL),
(3, 1, 'Le grand retard de Koulou chez le roi lion', 'Pendant cette séance, il s’agit d’amener les enfants d’abord à comprendre ce qu’est le retard par rapport à la ponctualité. En outre, il s’agit de les amener à faire une démarcation conceptuelle entre ces deux concepts. Le plus simple pour eux serait certainement de faire cette distinction conceptuelle entre être en retard et être ponctuel.', 2, '2025-10-04 18:03:48', '2025-10-04 18:03:48', NULL),
(4, 1, 'Le recyclage', 'Le recyclage', 7, '2025-10-14 08:19:51', '2025-10-14 08:19:51', NULL),
(5, 1, 'Partager son gouter', 'Partager son gouter', 8, '2025-11-12 14:49:03', '2025-11-12 14:49:03', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(2, 'App\\Models\\User', 1, 'auth_token', '3a9b61bcb95e1e5f8c8ac06c011b28dd8607b82b471cdcd8cd466590569037b3', '[\"*\"]', '2025-11-06 10:53:54', NULL, '2025-11-06 05:50:54', '2025-11-06 10:53:54'),
(3, 'App\\Models\\User', 1, 'auth_token', '2dfa2468f6e59e45a9df82b5a5a948e4268c11883e95528f88e9695bf4b0fbfd', '[\"*\"]', '2025-11-07 09:19:30', NULL, '2025-11-07 08:01:16', '2025-11-07 09:19:30'),
(4, 'App\\Models\\User', 1, 'auth_token', '5c7ddda9d07ce6835a4050c34ea796739f6d79ffa2242e94fda3d5324afeec84', '[\"*\"]', '2025-11-09 16:20:08', NULL, '2025-11-09 16:04:13', '2025-11-09 16:20:08'),
(5, 'App\\Models\\User', 1, 'auth_token', 'd29aac6546702953f9fa560eb7ede6cfbc3bfefc8bc83dddd7fb653c8682d712', '[\"*\"]', '2025-11-12 04:44:57', NULL, '2025-11-12 04:29:30', '2025-11-12 04:44:57'),
(6, 'App\\Models\\User', 1, 'auth_token', 'c35d3ab16b5b7b912c89e451a59ac376330b10923c4c8d8924fffdab5cade0d6', '[\"*\"]', '2025-11-13 07:06:41', NULL, '2025-11-12 13:53:38', '2025-11-13 07:06:41');

-- --------------------------------------------------------

--
-- Structure de la table `profils`
--

CREATE TABLE `profils` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `sexe` varchar(191) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `profil` varchar(191) DEFAULT '/storage/profil/profil.png',
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `niveau_id` bigint(20) UNSIGNED DEFAULT NULL,
  `password` tinytext NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `profils`
--

INSERT INTO `profils` (`id`, `code`, `name`, `sexe`, `age`, `profil`, `user_id`, `niveau_id`, `password`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'FVz6EWoe', 'Mballa', 'Féminin', 8, '/storage/profil/LzjesTSImdmjQaGYE9zsHvZ1cbxCu9gPtDXNqHde.png', 1, 1, '', 1, '2025-11-02 15:43:05', '2025-11-06 07:35:27', NULL),
(2, 'wy1JFGCL', 'John', 'Masculin', 5, '/storage/profil/KIwNxpc4fFZS3vtID7CVpvcDn11Fq3eWU3hx4ASZ.jpg', 1, 1, 'eyJpdiI6IlplcDBmVWp3Q1Z5YkJLVE9rN2FTSnc9PSIsInZhbHVlIjoidmxHeVBSNElrOW0rQXRRZTVmL0ZEQT09IiwibWFjIjoiN2I2NjQ0OTY3MmQ5OTkxMWI4ZmI1MDA4ZTdhM2FjZDY2NjhkYTJmMGFiMzg0ZjgyNzM0YTNkM2ZkOGJkMGQwYSIsInRhZyI6IiJ9', 1, '2025-11-03 04:52:48', '2025-11-07 09:06:43', NULL),
(3, 'ywtsCekx', 'Blaise', 'Masculin', 11, '/storage/profil/JsNnf1GBA1AjJebkfuN0QFFi975y3QkTUoxWeRXW.png', 1, 1, 'eyJpdiI6ImxrQXpKYnJjOHhLUlJEQkdxcjlWWVE9PSIsInZhbHVlIjoiM3JXM0hyTGV4MDYwNVV1OWJZamRBUT09IiwibWFjIjoiMmRiN2UwODE3OTJmOWViZmM0YjI1MTIzNDIzMmNlMjhlMTJjNTRmNzY3YzVjNGJjZTM3Y2I1ZDQ4M2UwNDBhNCIsInRhZyI6IiJ9', 1, '2025-11-06 10:04:59', '2025-11-06 10:04:59', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `intitule_text` text DEFAULT NULL,
  `intitule_media_url` varchar(191) DEFAULT NULL,
  `intitule_media_description` text DEFAULT NULL,
  `thematique_id` bigint(20) UNSIGNED NOT NULL,
  `partie_id` bigint(20) UNSIGNED NOT NULL,
  `degre_difficulte` varchar(191) NOT NULL,
  `type_reponse` varchar(191) NOT NULL,
  `indice` text DEFAULT NULL,
  `explication` text DEFAULT NULL,
  `reponses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`reponses`)),
  `numero` int(11) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `last_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `questions`
--

INSERT INTO `questions` (`id`, `intitule_text`, `intitule_media_url`, `intitule_media_description`, `thematique_id`, `partie_id`, `degre_difficulte`, `type_reponse`, `indice`, `explication`, `reponses`, `numero`, `created_by`, `last_updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Alors mes amies, dans la vidéo que vous venez regarder, quel est le problème de notre petite tortue Koulou par rapport au léopard ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Le problème se trouve dans ce que la petite tortue n\'arrive pas à faire.', 'RAS', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"mediaUrl\\\":null,\\\"text\\\":\\\"Son probl\\\\u00e8me c\'est parce qu\'elle marche lentement\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"mediaUrl\\\":null,\\\"text\\\":\\\"Son probl\\\\u00e8me est qu\'elle est toujours en retard\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"mediaUrl\\\":null,\\\"text\\\":\\\"Son probl\\\\u00e8me est qu\'elle n\'est pas ponctuelle\\\"}]\"', 1, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-10-04 21:56:43', NULL),
(2, 'Peux-tu poser une question par rapport à la tortue ou à la panthère dans l\'histoire racontée dans la vidéo ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Pour se poser les bonnes questions, il faut se demander si on peut avoir quelques idées correctes aux questions qu\'on choisit de se poser.', NULL, '\"[{\\\"text\\\": \\\"Pourquoi la tortue ne se réveille pas vite ?\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Pourquoi la maison de la tortue est si loin qu\'elle n\'arrive à vite?\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Qu\'est-ce que fait la tortue pour ne pas arriver à l\'heure ?\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 2, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(3, 'Qu\'est-ce qu\'être en retard ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Être en retard c\'est lorsque tu viens après l\'heure\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Être en retard c\'est lorsque tu ne respectes pas l\'heure\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Être en retard c\'est lorsque tu viens avant l\'heure\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 3, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(4, 'Qu\'est-ce qu\'être ponctuel ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Être en retard c\'est lorsque tu viens après l\'heure\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Être en retard c\'est lorsque tu respectes l\'heure\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Être en retard c\'est lorsque tu viens avant l\'heure\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 4, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(5, 'A-t-on le droit d\'être en retard ? Et pourquoi ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Non on n\'a pas le droit, parce qu\'arriver en retard comme la tortue, ce n\'est pas être respectueux de l\'autre\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Non on n\'a pas le droit, parce que ça veut dire que tu ne le méprises pas\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Oui on a le droit, parce qu\'on peut faire tout ce qu\'on veut\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 5, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(6, 'Mais est-ce qu\'il y a une situation où on a le droit de venir en retard ou de ne pas venir du tout?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Oui monsieur. On peut être malade\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Oui monsieur. On peut aussi avoir un accident\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Non monsieur. Pas d\'excuses.\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 6, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(7, 'Finalement a-t-on vraiment le droit d\'être en retard ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Oui. On peut venir en retard sans se soucier des autres\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Non. On n\'a pas le droit de venir en retard car c\'est ne pas respecter les autres.\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Non. On n\'a pas le droit d\'arriver en retard parce que ceux qui nous invitent, attendent qu\'on arrive à l\'heure.\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 7, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(8, 'Montre dans ton dessin la différence entre quelqu\'un qui est à l\'heure et quelqu\'un qui est en retard?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Utilise ton doigt sur l\'espace tactile en bas\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Fais des coloris\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Et sauvegarde ton dessin pour le montrer à tes amis et à tes parents\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 8, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(9, 'Alors mes amies, dans la vidéo que vous avez regardé, quel peut être encore le problème de notre petite tortue Koulou par rapport au temps ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Le problème se trouve dans ce que la petite tortue n\'arrive pas à faire.', NULL, '\"[{\\\"text\\\": \\\"Son problème est qu\'elle est toujours en retard\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Son problème c\'est parce qu\'elle marche lentement\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Son problème est qu\'elle n\'est gère pas bien son temps pour être ponctuelle\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 9, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(10, 'Peux-tu poser une question par rapport à la tortue ou à la panthère dans l\'histoire racontée dans la vidéo ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Pour se poser les bonnes questions, il faut se demander si on peut avoir quelques idées correctes aux questions qu\'on choisit de se poser.', NULL, '\"[{\\\"text\\\": \\\"Pourquoi la maison de la tortue est si loin qu\'elle n\'arrive à vite?\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Pourquoi la tortue ne se réveille pas vite ?\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Qu\'est-ce que fait la tortue pour ne pas arriver à l\'heure ?\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 10, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(11, 'Pour toi, qu\'est-ce que bien gérer son temps ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"C\'est organiser son temps de manière à ne pas être en retard\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est faire tout ce qu\'on veut de notre temps à n\'importe quel moment\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est s\'adapter au temps qu\'il fait de manière à ne pas subir le temps\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 11, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(12, 'Tu sais ce que signifie organiser son temps? Peux-tu me dire avec des exemples ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"C\'est ranger son temps comme on range sa chambre\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est faire les choses vites sans préparation comme un fou\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est partager son temps en plusieurs tranches comme on coupe un gâteau\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 12, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(13, 'Tu peux aussi me dire avec des exemples ce que signifie s\'adapter à son temps ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"C\'est ne pas voir le temps qu\'il fait comme vouloir sortir de la maison dans la pluie sans parapluie\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est faire avec le temps qu\'on nous donne comme pendant la composition\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est se mettre au rythme ou à la vitesse du temps comme un tam-tam\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 13, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(14, 'Et ça veut dire quoi subir le temps ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"C\'est lorsque tu dois attendre longtemps, même si c\'est long comme quand tu es en salle t\'attende à l\'hôpital\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est lorsque le temps continue a passé, même quand tu voudrais qu\'il s\'arrête comme pendant que tu dors durant la nuit\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est quand on est libre de décider à la place du temps comme on décide de manger ou non\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 14, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(15, 'Peux-tu me dire alors ce que tu penses du temps: est-il le même pour tout le monde ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Oui, le temps est le même pour tous parce que 30 min à l\'école égales 30 min à la maison.\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Oui le temps est le même pace que le jour se lève et se couche au même moment partout\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Non, les 30 min à l\'école durent plus que les 30 min à la maison, donc le temps n\'est pas la même chose pour tout le monde\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Non, le temps n\'est pas le même, car chaque chose à son temps, il y a les temps de saison pluvieuse et les temps de saison sèche\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 15, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(16, 'Alors mon ami, comment faut-il faire vraiment pour bien gérer son temps afin de ne plus venir en retard ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Il faut ranger son temps, c\'est-à-dire régler son réveil pour se lever tôt le matin\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Il faut marcher lentement comme une tortue sur le chemin et s\'arrêter pour jouer\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"C\'est partager son temps en plusieurs activités pour faire chaque chose à son temps et sans traîner\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 16, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(17, 'Qui peut dessiner un ami qui gère bien son temps et n\'arrive jamais en retard à l\'école ?', NULL, NULL, 2, 3, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Utilise ton doigt sur l\'espace tactile en bas\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Fais des coloris\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Et sauvegarde ton dessin pour le montrer à tes amis et à tes parents\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 17, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(18, 'Alors mes amies. dans la vidéo que vous venez regarder, quel est le problème de notre petit Fari l\'âne par rapport au cheval?', NULL, NULL, 1, 2, '1', 'multiple', 'Alors les amis! Le problème se trouve dans ce que la petit âne refuse de faire ou veut faire absolument.', NULL, '\"[{\\\"text\\\": \\\"Son problème est qu\'il refuse d\'obéir\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Son problème c\'est que son maître lui tape sur les fesses tout le temps\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Son problème est qu\'il veut être libre et faire tout ce qu\'il veut\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 1, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(19, 'Peux-tu poser une question par rapport à l\'âne ou au cheval dans l\'histoire racontée dans la vidéo ?', NULL, NULL, 1, 2, '1', 'multiple', 'Alors les amis! Pour se poser les bonnes questions, il faut se demander si la question choisi nous fait réfléchir, si elle n\'est pas trop facile pour trouver des idées.', NULL, '\"[{\\\"text\\\": \\\"Pourquoi l\'âne est court?\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Comment être obéissant ?\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Qu\'est-ce qu\'être obéissant ?\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 2, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(20, 'Qu\'est-ce qu\'être obéissant ?', NULL, NULL, 1, 2, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Être obéissant, c\'est partager quelque chose\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Etre obéissant, c\'est respecter les règles ou les lois\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Obéir, ça veut dire que tu dois faire ce que tes aînés de disent\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 3, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(21, 'Qu\'est-ce qu\'être désobéissant ?', NULL, NULL, 1, 2, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Être désobéissant, c\'est refuser de faire ce que tes aînés de disent\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Être désobéissant, c\'est partager quelque chose\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Etre désobéissant, c\'est ne pas respecter les règles ou les lois\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 4, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(22, 'Toute les règles sont-elles justes ?', NULL, NULL, 1, 2, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Non, toutes les lois ne sont pas juste, par exemple dire qu\'on doit: « traverser la route au feu vert » est une mauvaise loi.\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Non, toutes les règles ne sont pas bonnes, par exemple dire que : « seuls certains enfants ont le droit de jouer » est une règle injuste.\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Oui, toutes les règles sont bonnes et il faut les obéir\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 5, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(23, 'Si toutes les règles ou lois ne sont pas bonnes, a-t-on le droit de désobéir aux mauvaises règles ?', NULL, NULL, 1, 2, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Oui, on doit désobéir une règle qu\'on juge mauvaise car si on l\'obéit on se met en danger\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Oui, on doit désobéir une loi qu\'on juge injuste car si on l\'obéit on peut faire du mal à l\'autre\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Non, la loi c\'est la loi, il faut l\'obéir\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 6, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(24, 'Finalement comment être vraiment obéissant ?', NULL, NULL, 1, 2, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Il faut d\'abord bien juger ce qu\'on nous demande de faire si c\'est juste ou injustes, bien ou mauvais\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Il faut obéir les règles/lois sans réfléchir car ce sont les adultes qui les ont créées\\\", \\\"isCorrect\\\": false, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Il faut d\'abord bien juger ce qu\'on nous demande de faire si c\'est juste ou injustes, bien ou mauvais\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 7, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(25, 'Montre dans ton dessin la différence entre Fari et le cheval?', NULL, NULL, 1, 2, '1', 'multiple', 'Alors les amis! Regarde sur l\'image.', NULL, '\"[{\\\"text\\\": \\\"Utilise ton doigt sur l\'espace tactile en bas\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Fais des coloris\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}, {\\\"text\\\": \\\"Et sauvegarde ton dessin pour le montrer à tes amis et à tes parents\\\", \\\"isCorrect\\\": true, \\\"mediaUrl\\\": null, \\\"contentType\\\": \\\"text\\\", \\\"mediaDescription\\\": null}]\"', 8, 1, NULL, NULL, '2025-09-28 07:26:28', '2025-09-28 07:26:28', NULL),
(26, NULL, '/storage/questions/APi5CoN6NbViMlHXy4jRFPrNYwPupAtMZENlgZlD.jpg', 'Dans quelle poubelle doit-on jeter une bouteille en plastique ?', 7, 4, '2', 'unique', 'C\'est la couleur du soleil !', 'Les bouteilles en plastique vont dans la poubelle jaune pour être recyclées et transformées en nouveaux objets.', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle normale\\\",\\\"mediaUrl\\\":null},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle jaune\\\",\\\"mediaUrl\\\":null},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle verte\\\",\\\"mediaUrl\\\":null},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Par terre\\\",\\\"mediaUrl\\\":null}]\"', 1, NULL, NULL, NULL, '2025-07-18 07:06:18', '2025-09-13 09:56:38', NULL),
(27, 'Que veut dire \"recycler\" ?', NULL, NULL, 7, 4, '1', 'unique', 'C\'est comme donner une nouvelle vie aux objets !', 'Recycler, c\'est transformer les déchets en nouveaux objets utiles au lieu de les jeter.', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Jeter \\\\u00e0 la poubelle\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Casser en morceaux\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Transformer en quelque chose de nouveau\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Mettre dans un sac\\\"}]\"', 2, NULL, NULL, NULL, '2025-07-18 07:10:09', '2025-09-13 09:56:38', NULL),
(28, 'Où doit-on jeter les épluchures de légumes ?', NULL, NULL, 7, 4, '1', 'unique', 'Elles peuvent nourrir la terre !', 'Elles peuvent nourrir la terre !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans le compost\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle jaune\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la rue\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Dans la poubelle bleue\\\"}]\"', 4, NULL, NULL, NULL, '2025-07-18 07:12:03', '2025-09-13 09:56:38', NULL),
(29, 'Qu\'est-ce qu\'on peut faire avec une bouteille en plastique vide ?', NULL, NULL, 7, 4, '2', 'unique', 'On peut la transformer en quelque chose d\'utile !', 'Au lieu de jeter, on peut réutiliser une bouteille pour en faire un pot à crayons ou un autre objet utile.', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"La jeter par terre\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"La casser\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"La manger\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"En faire un pot \\\\u00e0 crayons\\\"}]\"', 3, NULL, NULL, NULL, '2025-07-18 07:13:45', '2025-09-13 09:56:38', NULL),
(30, 'Quel symbole trouve-t-on sur les objets recyclables ?', NULL, NULL, 7, 4, '2', 'unique', 'Elles forment un cercle et se suivent !', 'Le symbole de recyclage avec trois flèches qui tournent nous montre qu\'un objet peut être recyclé.', '\"[{\\\"contentType\\\":\\\"media\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":\\\"Etoile\\\",\\\"mediaSourceType\\\":\\\"file\\\",\\\"mediaUrl\\\":\\\"\\\\/storage\\\\/questions\\\\/qSvyF0eAsZxm5gXl3SpxazaVJMNvEagCBIfsKcxN.png\\\"},{\\\"contentType\\\":\\\"media\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":\\\"Trois fl\\\\u00e8ches qui tournent\\\",\\\"mediaSourceType\\\":\\\"file\\\",\\\"mediaUrl\\\":\\\"\\\\/storage\\\\/questions\\\\/L02Ku0413H8bIBTjRZqp2h9XF6857jYmLd7lr29b.png\\\"},{\\\"contentType\\\":\\\"media\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":\\\"Rond\\\",\\\"mediaSourceType\\\":\\\"file\\\",\\\"mediaUrl\\\":\\\"\\\\/storage\\\\/questions\\\\/m1MmzGSBgrC3DwOlS9BPYPKCxWd765t7biuyYTzL.png\\\"}]\"', 6, NULL, NULL, NULL, '2025-07-18 07:19:47', '2025-09-13 09:56:38', NULL),
(31, 'Que peut-on faire avec du papier journal utilisé ?', NULL, NULL, 7, 4, '2', 'unique', 'Il peut redevenir du papier tout neuf !', 'Le vieux papier peut être recyclé pour fabriquer du nouveau papier, des livres ou des cahiers.', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Le br\\\\u00fbler\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Le manger\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Le mettre sous l\'oreiller\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Le recycler en nouveau papier\\\"}]\"', 7, NULL, NULL, NULL, '2025-07-18 07:22:21', '2025-09-13 09:56:38', NULL),
(32, NULL, '/storage/questions/WYsCnpTRnJB10hUNnBwbkUx8HKzVKse90I4bLpFW.png', 'Pourquoi est-ce important de trier ses déchets ?', 7, 4, '2', 'unique', 'C\'est pour prendre soin de notre belle Terre !', 'En triant nos déchets, on aide à recycler et on protège notre planète en réduisant les déchets.', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour faire joli\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour faire du bruit\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour prot\\\\u00e9ger la plan\\\\u00e8te\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour se fatiguer\\\"}]\"', 5, NULL, NULL, NULL, '2025-07-18 07:26:33', '2025-09-13 09:56:38', NULL),
(33, 'Combien de fois peut-on recycler une bouteille en verre ?', NULL, NULL, 7, 4, '3', 'unique', 'C\'est comme si elle avait une vie éternelle !', 'Le verre peut être recyclé à l\'infini sans perdre sa qualité, c\'est un super-héros du recyclage !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"\\\\u00c0 l\'infini (plein de fois)\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"5 fois\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Jamais\\\"}]\"', 8, NULL, NULL, NULL, '2025-07-18 07:28:10', '2025-09-13 09:56:38', NULL),
(34, 'Que devient une canette en aluminium après recyclage ?', NULL, NULL, 7, 4, '3', 'unique', 'Elle peut redevenir exactement la même chose !', 'Une canette en aluminium peut être fondue et transformée en nouvelle canette, c\'est magique !', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Elle dispara\\\\u00eet\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Une nouvelle canette\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Un g\\\\u00e2teau\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Une fleur\\\"}]\"', 9, NULL, NULL, NULL, '2025-07-18 07:29:49', '2025-09-13 09:56:38', NULL),
(35, 'Qu\'est-ce qui arrive si on ne recycle pas ?', NULL, NULL, 7, 4, '3', 'unique', 'Imagine ta chambre si tu ne ranges jamais tes jouets !', 'Sans recyclage, les déchets s\'accumulent et polluent notre belle planète, ce qui rend tristes les animaux et les plantes.', '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Il y a trop de d\\\\u00e9chets partout\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"La plan\\\\u00e8te devient plus belle\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Rien ne change\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Les animaux sont plus heureux\\\"}]\"', 10, NULL, NULL, NULL, '2025-07-18 07:31:36', '2025-09-13 09:56:38', NULL),
(36, 'Quelle est la capitale du Cameroun?', NULL, NULL, 2, 1, '2', 'unique', NULL, NULL, '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Yaound\\\\u00e9\\\",\\\"mediaUrl\\\":null},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Douala\\\",\\\"mediaUrl\\\":null}]\"', 11, NULL, NULL, NULL, '2025-07-18 08:39:22', '2025-09-13 09:56:38', NULL),
(37, 'Que veut dire partager ?', NULL, NULL, 8, 5, '1', 'unique', NULL, NULL, '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Garder tout pour soi\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Ne rien donner au autre\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Donner aux autres un peu de ce que l\'on a\\\"}]\"', 1, 1, NULL, NULL, '2025-11-12 14:52:01', '2025-11-12 14:52:01', NULL),
(38, 'Pourquoi doit-on partager avec les autres ?', NULL, NULL, 8, 5, '1', 'unique', NULL, NULL, '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour qu\'ils soient oblig\\\\u00e9 eux aussi de partager en retour\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour avoir de bonne relation avec son entourage\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Pour que les autres nous laisse enfin tranquile\\\"}]\"', 2, 1, NULL, NULL, '2025-11-12 14:53:37', '2025-11-12 14:53:37', NULL),
(39, 'Doit-on partager jusqu\'a ne plus en avoir pour soi-meme ?', NULL, NULL, 8, 5, '1', 'unique', NULL, NULL, '\"[{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":false,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Oui\\\"},{\\\"contentType\\\":\\\"text\\\",\\\"isCorrect\\\":true,\\\"mediaDescription\\\":null,\\\"text\\\":\\\"Non\\\"}]\"', 3, 1, NULL, NULL, '2025-11-12 14:54:25', '2025-11-12 14:54:25', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('2viDueCSfjfKsaCipNr4l9hWUDMLhxA4yMnjBR2l', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYkV3bThZU1diN1llanNNUGFFNjVrOEF4dWJiOWg4UkhvSHFkVXVjeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762503522),
('XWhxAPFJzyLlcIDHPIfPvLZzkbHW3Bo45xJQATfd', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR2hyMWF4TGtidGNiRWh1bXlVTks4ZmpMM0tQSkFUU0dpckNVMmJvNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1762707843);

-- --------------------------------------------------------

--
-- Structure de la table `thematiques`
--

CREATE TABLE `thematiques` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `media_type` varchar(191) DEFAULT NULL,
  `media_url` varchar(191) DEFAULT NULL,
  `media_description` varchar(191) DEFAULT NULL,
  `emoji` varchar(191) DEFAULT '?',
  `couleur` varchar(191) DEFAULT '#ff6600ff',
  `nbr_min_point` int(11) NOT NULL DEFAULT 1,
  `niveau_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `last_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `thematiques`
--

INSERT INTO `thematiques` (`id`, `name`, `description`, `parent_id`, `media_type`, `media_url`, `media_description`, `emoji`, `couleur`, `nbr_min_point`, `niveau_id`, `created_by`, `last_updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Le vivre-ensemble', 'Le vivre-ensemble est un concept central qui permet de comprendre différents rapports entre les divers hommes vivant dans la même société. Il s\'agit de leurs rapports au temps, l\'espace, la générosité, la liberté, au travail, et à la politesse.', NULL, 'url', '/storage/thematique/uPuk62Nr0ZbuIZBPhduS6ckQsE3U1VwTpfHkFNDg.mp4', NULL, '🤝', '#fcb69f', 10, 1, NULL, NULL, NULL, '2025-10-04 17:46:46', '2025-10-13 12:00:15', NULL),
(2, 'Le petit voyage de Koulou la tortue', 'Lorsqu’on voyage on tient compte du temps et de l’espace, c’est-à-dire de l’heure de départ ou d’arrivée, de la durée du voyage, de l’accélération de nos mouvements pour la distance à parcourir (si on se déplace rapidement ou lentement) et d’où on va (du lieu) : notre ami la tortue n’a pas l’air de le savoir !', 1, 'file', '/storage/thematique/3HGiuWQpZ4sH3ilS0Tr1oJaoezqng3sv5G9pryLJ.png', 'Le petit voyage de Koulou la tortue', NULL, NULL, 8, 1, NULL, NULL, NULL, '2025-10-04 18:02:19', '2025-10-04 18:02:19', NULL),
(3, 'Le recyclage', 'Le recyclage', NULL, 'text', NULL, NULL, '🎨', '#ff6600ff', 1, NULL, NULL, NULL, NULL, '2025-10-13 09:48:38', '2025-10-13 09:57:14', '2025-10-13 09:57:14'),
(4, 'Le recyclage', 'Le recyclage', NULL, 'text', NULL, NULL, '🎨', '#ff6600ff', 1, 18, NULL, NULL, NULL, '2025-10-13 09:50:15', '2025-10-13 09:57:12', '2025-10-13 09:57:12'),
(5, 'Le recyclage', 'Le recyclage', NULL, 'text', NULL, NULL, '♻️', '#44a08d', 1, 1, NULL, NULL, NULL, '2025-10-13 09:57:46', '2025-10-13 11:58:41', NULL),
(6, 'Le partage', 'Le partage', NULL, 'file', '/storage/thematique/xRD4tVxOpoGDQlcIQFvq1ycpiUwbLSp0GnfCSCvd.png', NULL, '🌍', '#f3ff99', 1, 1, NULL, NULL, NULL, '2025-10-13 10:44:48', '2025-10-13 11:53:21', NULL),
(7, 'Le recyclage', 'Le recyclage', 5, 'text', NULL, NULL, '🎨', '#3fab65', 8, 1, NULL, NULL, NULL, '2025-10-14 08:19:19', '2025-10-14 08:19:19', NULL),
(8, 'Pourquoi partager ?', 'Les raisons du partage', 6, 'text', NULL, NULL, '👊', '#f8c4c4', 8, 1, 1, NULL, NULL, '2025-11-12 14:48:26', '2025-11-12 14:48:26', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `types`
--

CREATE TABLE `types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `types`
--

INSERT INTO `types` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Apprenti', NULL, '2025-10-11 17:24:50', '2025-10-11 17:24:50'),
(2, 'Parent', NULL, '2025-10-11 17:24:50', '2025-10-11 17:24:50'),
(3, 'Admin', NULL, '2025-10-11 17:25:31', '2025-10-11 17:25:31'),
(4, 'Super Admin', NULL, '2025-10-11 17:25:31', '2025-10-11 17:25:31');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(191) NOT NULL,
  `id_google` varchar(191) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT 1,
  `sexe` varchar(191) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `telephone` varchar(191) DEFAULT NULL,
  `profil` varchar(191) DEFAULT '/storage/profil/profil.png',
  `niveau_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `last_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `code`, `id_google`, `name`, `email`, `type_id`, `sexe`, `age`, `telephone`, `profil`, `niveau_id`, `created_by`, `last_updated_by`, `deleted_by`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TEST12USER', NULL, 'Test User', 'moafogaus@gmail.com', 3, 'Masculin', 25, '678859210', '/storage/profil/profil.png', 1, NULL, NULL, 1, NULL, '$2y$12$AElcNMo2rUuRYOdSxo.VreE15ZLJphBHTp0SBk5O.9DnzuelZBH3O', 'rng0cthO0Y', '2025-10-04 17:46:46', '2025-11-06 05:43:58', NULL),
(2, 'ADMIN01USER', NULL, 'Admin User', 'admin@example.com', 1, NULL, NULL, NULL, '/storage/profil/profil.png', NULL, NULL, NULL, NULL, '2025-10-04 17:46:46', '$2y$12$AElcNMo2rUuRYOdSxo.VreE15ZLJphBHTp0SBk5O.9DnzuelZBH3O', '21BzBAECib', '2025-10-04 17:46:46', '2025-10-04 17:46:46', NULL),
(3, 'ipED2D5B', NULL, 'Albert', 'albert@gmail.com', 1, 'Masculin', 13, '637635637', '/storage/profil/50FyUc327NpT2BLcjG5j7D9t9wfU03WyuJRTVWPa.png', 18, NULL, NULL, NULL, NULL, '$2y$12$EHYS3xp8QHTVUnVLm1GqZeBDqFs/A2ghApPj2EyCL8crYzGkyst5y', NULL, '2025-10-12 04:10:39', '2025-10-12 05:09:52', '2025-10-12 05:09:52'),
(4, 'BYVm3Vci', NULL, 'My admin', 'myadmin@gmail.com', 3, '1', 1, '1', '/storage/profil/Ub9tOyNx5571pG79GxJzBhIsAs6BxKVgu2wPQKLK.jpg', 2, NULL, NULL, NULL, NULL, '$2y$12$eWJ3BKfg4se6et26ahIR8eaZ6o20t1Kz9sReoxzWayrfizquWZhve', NULL, '2025-11-05 13:43:15', '2025-11-05 13:43:15', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `badge_users`
--
ALTER TABLE `badge_users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `niveaux`
--
ALTER TABLE `niveaux`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `parties`
--
ALTER TABLE `parties`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `profils`
--
ALTER TABLE `profils`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `questions_thematique_id_partie_id_numero_unique` (`thematique_id`,`partie_id`,`numero`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `thematiques`
--
ALTER TABLE `thematiques`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `badge_users`
--
ALTER TABLE `badge_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `niveaux`
--
ALTER TABLE `niveaux`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `parties`
--
ALTER TABLE `parties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `profils`
--
ALTER TABLE `profils`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `thematiques`
--
ALTER TABLE `thematiques`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `types`
--
ALTER TABLE `types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
