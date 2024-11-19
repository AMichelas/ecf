-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 19 nov. 2024 à 13:55
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
-- Base de données : `ecf`
--

-- --------------------------------------------------------

--
-- Structure de la table `animals`
--

CREATE TABLE `animals` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `breed` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `food_type` varchar(255) DEFAULT NULL,
  `food_amount` int(11) DEFAULT NULL,
  `feeding_time` datetime DEFAULT NULL,
  `vet_visit_date` date DEFAULT NULL,
  `details` text DEFAULT NULL,
  `habitat_id` int(11) DEFAULT NULL,
  `click_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `animals`
--

INSERT INTO `animals` (`id`, `name`, `breed`, `image_url`, `status`, `food_type`, `food_amount`, `feeding_time`, `vet_visit_date`, `details`, `habitat_id`, `click_count`) VALUES
(24, 'Barracuda', 'Crocodile', 'uploads/animal1.jpg', NULL, 'Viande', 85500, '2024-04-21 19:00:00', '2024-05-21', '', 76, 7),
(26, 'Japhare', 'Lion', 'uploads/animal4.jpg', NULL, 'Viande', 5000, '2024-11-08 19:00:00', '2024-11-22', '', 80, 3),
(28, 'Sophie', 'Girafe', 'uploads/animal2.jpg', NULL, 'Feuille', 8500, '2024-11-21 01:00:00', '2024-11-20', '', 82, 1);

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `avis` text NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`id`, `pseudo`, `avis`, `status`, `created_at`) VALUES
(1, 'Bonjour', 'test', 1, '2024-11-15 16:45:25'),
(3, 'test2', 'test2', 1, '2024-11-15 17:30:13'),
(4, 'test3', 'test3', 1, '2024-11-15 17:30:18');

-- --------------------------------------------------------

--
-- Structure de la table `habitats`
--

CREATE TABLE `habitats` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `page_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `habitats`
--

INSERT INTO `habitats` (`id`, `name`, `image_url`, `page_url`) VALUES
(76, 'marais', 'marais.jpg', 'marais'),
(80, 'savane', 'savane.jpg', 'savane'),
(82, 'foret', 'forest.png', 'foret');

-- --------------------------------------------------------

--
-- Structure de la table `horaires`
--

CREATE TABLE `horaires` (
  `id` int(11) NOT NULL,
  `jour` varchar(20) NOT NULL,
  `ouverture` time DEFAULT NULL,
  `fermeture` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `horaires`
--

INSERT INTO `horaires` (`id`, `jour`, `ouverture`, `fermeture`) VALUES
(1, 'Lundi', '09:00:00', '18:00:00'),
(2, 'Mardi', '08:00:00', '18:00:00'),
(3, 'Mercredi', '08:00:00', '18:00:00'),
(4, 'Jeudi', '09:00:00', '18:00:00'),
(5, 'Vendredi', '08:00:00', '18:00:00'),
(6, 'Samedi', '08:00:00', '21:00:00'),
(7, 'Dimanche', '08:00:00', '21:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `titre`, `description`) VALUES
(1, 'Petit Train', 'Un train pour visiter le parc sans fatigue'),
(2, 'Visite guider du zoo entièrement gratuite !', 'Une visite 100% gratuite venez visitez dans les habitats nos animaux'),
(6, 'Restaurent', 'Une chaîne de restaurent crée par le parc afin de proposer une expérience immersive jusqu\'à l\'heure du repas !');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rank` int(11) NOT NULL CHECK (`rank` between 1 and 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `rank`) VALUES
(1, 'Adrien', 'Michelas', 'michelasadrien@gmail.com', '$2y$10$t9YOUoAeqnwe6wSmtPO6P.uEmmWxmTLJr8iIV.8Z6sKhVjO8B88WC', 3),
(5, 'veto', 'veto', 'test@test.fr', '$2y$10$AGt6zgYFMB74gCnflop9R.lYOweKiKs/xxi1utj9DEV.oyyUrYxCC', 1),
(9, 'try', 'try', 'employertest@zoo.fr', '$2y$10$WxoEpgPTfkE6z2ytl1bYEuqtzlPgt843Ayijy6DgKbGikbn9HJYeO', 2),
(10, 'José', 'Arcadia', 'josearcadia@arcadia.fr', '$2y$10$kamD870Ei4VlpDNv8Qpr3.TwIPAvLIEtCuIcWtpRqPPHjFu7IzUta', 3);

-- --------------------------------------------------------

--
-- Structure de la table `vet_reviews`
--

CREATE TABLE `vet_reviews` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `review_date` datetime NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `animals`
--
ALTER TABLE `animals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animals_ibfk_1` (`habitat_id`);

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `habitats`
--
ALTER TABLE `habitats`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `horaires`
--
ALTER TABLE `horaires`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `vet_reviews`
--
ALTER TABLE `vet_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `animal_id` (`animal_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `animals`
--
ALTER TABLE `animals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `habitats`
--
ALTER TABLE `habitats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT pour la table `horaires`
--
ALTER TABLE `horaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `vet_reviews`
--
ALTER TABLE `vet_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `animals`
--
ALTER TABLE `animals`
  ADD CONSTRAINT `animals_ibfk_1` FOREIGN KEY (`habitat_id`) REFERENCES `habitats` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vet_reviews`
--
ALTER TABLE `vet_reviews`
  ADD CONSTRAINT `vet_reviews_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
