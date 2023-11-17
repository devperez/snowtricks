-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : db:3306
-- Généré le : ven. 17 nov. 2023 à 14:48
-- Version du serveur : 5.7.42
-- Version de PHP : 8.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `snowtricks`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trick_id` int(11) NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `user_id`, `trick_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 'Super photos et les vidéos sont super cool !', '2023-08-25 10:06:39'),
(2, 1, 1, 'Super intéressant !', '2023-08-25 13:12:38'),
(3, 7, 1, 'Pas évident...', '2023-10-13 09:31:38'),
(4, 6, 1, 'Tu rigoles, c\'est le plus facile !', '2023-10-13 09:32:04'),
(5, 7, 1, 'Je débute moi !', '2023-10-20 13:07:42'),
(6, 6, 1, 'Faut y aller petit à petit.', '2023-10-20 13:07:53'),
(7, 6, 1, 'Tu vas y arriver !', '2023-10-20 13:08:04'),
(8, 7, 1, 'J\'essaierai cet hiver.', '2023-10-20 13:08:16'),
(9, 7, 1, 'Ça a l\'air difficile !', '2023-10-20 13:08:26'),
(10, 6, 1, 'Je le fais souvent celui-ci !', '2023-10-20 13:08:40'),
(11, 6, 1, 'Ah oui, je connais !', '2023-10-20 13:08:48'),
(12, 6, 2, 'premier commentaire !', '2023-10-20 15:56:17');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20230824141138', '2023-08-24 14:12:15', 645),
('DoctrineMigrations\\Version20230915120549', '2023-09-15 12:09:20', 62),
('DoctrineMigrations\\Version20231027104221', '2023-10-27 10:42:57', 115),
('DoctrineMigrations\\Version20231027144605', '2023-10-27 14:48:00', 142),
('DoctrineMigrations\\Version20231027155920', '2023-10-27 15:59:29', 112),
('DoctrineMigrations\\Version20231101124921', '2023-11-01 12:50:46', 125),
('DoctrineMigrations\\Version20231101130915', '2023-11-01 13:09:37', 139);

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `trick_id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `trick_id`, `type`, `media`) VALUES
(19, 13, 'photo', '/images/Tricks-Stalefish-Grab-620x393-65577066edd07.jpg'),
(20, 14, 'photo', '/images/Trick-Indy-Grab-620x447-655772c544f1f.jpg'),
(22, 1, 'photo', '/images/Trick-Mute-Grab-620x444-655773a507622.jpg'),
(23, 2, 'photo', '/images/Trick-Meloncollie-Grab-620x421-6557748dc47ea.jpg'),
(24, 1, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/k6aOWf0LDcQ?si=h0sR7ut6pYPLfuYm\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>'),
(25, 13, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/f9FjhCt_w2U?si=OjROLvhWw2-BM8FS\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>'),
(26, 14, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/4AlDWWsprZM?si=g5e2JuFez3UHrWPK\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>'),
(27, 2, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/KEdFwJ4SWq4?si=Dwjspqw2VMADaRxb\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>');

-- --------------------------------------------------------

--
-- Structure de la table `password_token`
--

CREATE TABLE `password_token` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `trick`
--

CREATE TABLE `trick` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trick`
--

INSERT INTO `trick` (`id`, `user_id`, `name`, `description`, `category`) VALUES
(1, 1, 'Mute', 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.', 'Grab'),
(2, 1, 'Sad', 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.', 'Grab'),
(13, 1, 'Stalefish', 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière', 'Grab'),
(14, 1, 'Indy', 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière', 'Grab');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `name`, `password`, `email`, `photo`, `is_verified`) VALUES
(1, 'David', '$2y$13$2HU6A3CbBIDtnawdZIIUJejJuCKvXwdpvvzr3QNsyV7cMX5aMBBtK', 'david@email.com', '/images/1000-F-223507324-jKl7xbsaEdUjGr42WzQeSazKRighVDU4-65576d587903b.jpg', 1),
(6, 'Michel', '$2y$13$2HU6A3CbBIDtnawdZIIUJejJuCKvXwdpvvzr3QNsyV7cMX5aMBBtK', 'michel@email.com', '/images/logo-6557493f9c29e.webp', 1),
(7, 'Jean', '$2y$13$HoCDStzzsbj6ejHoM2Qh5OgT4MDsihBniEvhKIOOREVuTl7O4V106', 'jean@email.com', '/images/avatar2-65576e0293d06.png', 1),
(22, 'Paul', '$2y$13$faoftwJp6K5tBO.C0ru3r.K5Dqr30HGzdrbMRU3i93OflZY9OL6I.', 'paul@email.com', NULL, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526CA76ED395` (`user_id`),
  ADD KEY `IDX_9474526CB281BE2E` (`trick_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6A2CA10CB281BE2E` (`trick_id`);

--
-- Index pour la table `password_token`
--
ALTER TABLE `password_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_BEAB6C24A76ED395` (`user_id`);

--
-- Index pour la table `trick`
--
ALTER TABLE `trick`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D8F0A91E5E237E06` (`name`),
  ADD KEY `IDX_D8F0A91EA76ED395` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `password_token`
--
ALTER TABLE `password_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `trick`
--
ALTER TABLE `trick`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9474526CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `FK_6A2CA10CB281BE2E` FOREIGN KEY (`trick_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `password_token`
--
ALTER TABLE `password_token`
  ADD CONSTRAINT `FK_BEAB6C24A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `trick`
--
ALTER TABLE `trick`
  ADD CONSTRAINT `FK_D8F0A91EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
