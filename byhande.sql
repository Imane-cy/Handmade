-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 31 mai 2025 à 11:14
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
-- Base de données : `byhande`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(1, 'Maison et décorBougie'),
(2, 'Bijoux'),
(3, 'Sacs');

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

CREATE TABLE `factures` (
  `id` int(11) NOT NULL,
  `nom_client` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `mode_paiement` enum('Carte Bancaire','Paiement à la livraison') NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `date_achat` datetime NOT NULL,
  `details` text NOT NULL,
  `id_user` int(11) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `factures`
--

INSERT INTO `factures` (`id`, `nom_client`, `email`, `adresse`, `mode_paiement`, `total`, `date_achat`, `details`, `id_user`, `telephone`) VALUES
(55, 'imane mansouri', 'mansouri@gmail.com', 'fjh', 'Paiement à la livraison', 250.00, '2025-05-23 17:13:08', 'Éclat Vanillé (1 - blanc) - 250 DA\n', 14, '0987654321'),
(56, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'asdasdasda', 'Paiement à la livraison', 1250.00, '2025-05-24 14:09:25', 'Éclat Vanillé (5 - violet) - 1250 DA\n', 13, '0987654321'),
(57, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'asdasdasda', 'Paiement à la livraison', 3000.00, '2025-05-27 13:15:29', 'dff (6) - 1500 DA\nNuit au Jasmin (5 - blanc) - 1500 DA\n', 13, '0987654321'),
(58, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'asdasdasda', 'Paiement à la livraison', 300.00, '2025-05-27 19:54:02', 'Nuit au Jasmin (1 - blanc) - 300 DA\n', 13, '0987654321'),
(59, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'asdasdasda', 'Paiement à la livraison', 300.00, '2025-05-27 20:07:46', 'Nuit au Jasmin (1 - blanc) - 300 DA\n', 13, '0987654321'),
(60, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'asdasdasda', 'Paiement à la livraison', 300.00, '2025-05-27 20:17:01', 'Nuit au Jasmin (1 - blanc) - 300 DA\n', 13, '0987654321'),
(61, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'asdasdasda', 'Paiement à la livraison', 300.00, '2025-05-27 20:21:26', 'Nuit au Jasmin (1 - blanc) - 300 DA\n', 13, '0987654321'),
(62, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'asdasdasda', 'Paiement à la livraison', 300.00, '2025-05-27 20:24:06', 'Nuit au Jasmin (1 - blanc) - 300 DA\n', 13, '0987654321'),
(63, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'asdasdasda', 'Paiement à la livraison', 300.00, '2025-05-27 20:26:21', 'Nuit au Jasmin (1 - blanc) - 300 DA\n', 13, '0987654321'),
(65, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'El Hadjar, Annaba, Algérie', 'Paiement à la livraison', 5400.00, '2025-05-29 15:49:40', 'Nuit au Jasmin (18 - rose) - 5400 DA\n', 13, '0687654321'),
(66, 'fatima merrouchi', 'fatimamerrouchi@gmail.com', 'El Hadjar, Annaba, Algérie', 'Paiement à la livraison', 2100.00, '2025-05-30 12:11:43', 'Nuit au Jasmin (7 - roge) - 2100 DA\n', 13, '0687654321');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `couleur` varchar(50) DEFAULT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `panier`
--

INSERT INTO `panier` (`id`, `user_id`, `produit_id`, `couleur`, `quantite`, `date_ajout`) VALUES
(38, 3, 93, 'violet', 1, '2025-05-30 20:25:27');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `sous_categorie` varchar(100) DEFAULT NULL,
  `product_color` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `prix`, `image`, `categorie`, `sous_categorie`, `product_color`, `stock`) VALUES
(85, 'Nuit au Jasmin', 'Un parfum floral délicat de jasmin blanc pour une sensation de luxe et de sérénité.', 300.00, 'jas.png', 'Décor', 'Bougie', NULL, 23),
(86, 'Café Crémeux', 'Une odeur gourmande de café fraîchement moulu adoucie par une touche de lait vanillé.', 200.00, 'cf.png', 'Décor', 'Bougie', NULL, 69),
(91, 'Forêt Enchantée', 'Un parfum vert et frais de pin et d’eucalyptus, qui transporte au cœur d’une forêt calme et mystérieuse.', 350.00, 'not mine.jfif', 'Décor', 'Bougie', NULL, -6),
(92, 'Lavande Provençale', 'Un arôme floral classique et relaxant qui rappelle les champs de lavande du sud de la France.', 300.00, 'téléchargement (13).jfif', 'Décor', 'Bougie', NULL, 0),
(93, 'Coïncidence', 'Bougie artisanale aux notes florales subtiles et boisées. Son contenant en béton brut révèle la beauté des détails simples. Une lumière douce pour des instants inattendus.', 200.00, 'Aesthetic Seshell Candle Shell Candle, Minimalist Shaped Candle, Personalized Gifts, Pillar Soy Candle - Etsy.jfif', 'Décor', 'Bougie', NULL, 73),
(94, 'Éclats de Lune', 'Bagues délicates faites à la main, ornées de pierres naturelles et enroulées de fil doré. À porter seules ou en accumulation pour un style doux et lumineux.', 300.00, 'téléchargement (14).jfif', 'Bijoux', 'Bague', NULL, 0),
(95, 'Essence Minérale', 'Des bagues raffinées en fil doré, ornées de pierres naturelles comme l\'améthyste, le quartz rose et le jade. Chaque pierre est unique et symbolise douceur, sérénité.', 350.00, 'ES.png', 'Bijoux', 'Bague', NULL, 0),
(97, 'Aura Lumineuse', 'Cette bague au design épuré et moderne est fabriquée avec des matériaux de haute qualité, offrant une allure délicate et intemporelle. Son style minimaliste, avec une finition lisse, s\'adapte parfaitement ', 300.00, 'LL.jfif', 'Bijoux', 'Bague', NULL, 0),
(98, 'papillion', 'Un bracelet tendance avec un pendentif papillon délicat, parfait pour sublimer vos looks du quotidien.', 350.00, 'pap.png', 'Bijoux', 'Bracelet', NULL, 0),
(99, 'Florya', 'Un bracelet délicat composé de petites fleurs alignées, parfait pour ajouter une touche de douceur et d’élégance à votre tenue.\r\n', 350.00, 'flo.png', 'Bijoux', 'Bracelet', NULL, 0),
(100, 'Lunaya', 'Un bracelet fin orné d’un croissant de lune, symbole de mystère et de féminité, parfait pour une touche céleste au quotidien.', 350.00, 'mo.png', 'Bijoux', 'Bague', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_couleurs`
--

CREATE TABLE `produit_couleurs` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `couleur` varchar(7) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produit_couleurs`
--

INSERT INTO `produit_couleurs` (`id`, `produit_id`, `couleur`, `image`, `stock`) VALUES
(18, 85, 'blanc', 'Notre modèle La Délicate, sublimé par les fleurs de @la_fleur_en_plus 🌸 Une touche naturelle qui fait toute la différence ! ✨__Disponible sur pncandles_com 💚.jfif', 16),
(19, 85, 'roge', 'téléchargement (10).jfif', 4),
(21, 85, 'rose', 'téléchargement (8).jfif', 3),
(22, 86, 'beige', 'A stunning candle that will definitely add a touch of style and elegance to your home or space_ 🤍.jfif', 29),
(23, 86, 'maron', 'Bougie spirale.jfif', 40),
(33, 91, 'rose', 'téléchargement (11).jfif', 34),
(34, 91, 'jaune', 'Candela profumata in barattolo e vassoio.jfif', 30),
(35, 91, 'blue', 'téléchargement (12).jfif', 40),
(36, 92, 'blanc', 'Zahvalite se gostima na upečatljiv način 🌸 Tu smo za Vas da realizujemo Vaše ideje, da ispunimo svaku Vašu želju 🌸 Za više informacija obratite nam se porukom 🤍 #zahvalnice #zahvalnicezavjencanje #zahvalnicezakrst.jfif', 40),
(37, 92, 'violet', 'اقحوان لون نهدي.jfif', 35),
(38, 93, 'blanc', 'SHELL CANDLE SET! now available for purchase! 🕯️✨ Choose from four irresistible scents that are sure to fill your home with warmth and tranquility_ Tap the link in bio to shop now! 🛒.jfif', 30),
(39, 93, 'blue', 'Aesthetic Seshell Candle  Shell Candle Minimalist Shaped - Etsy Canada.jfif', 25),
(40, 93, 'violet', 'Shelly Soy Wax Candles.jfif', 18),
(41, 94, 'vert', 'téléchargement (15).jfif', 20),
(42, 94, 'rose', 'Etsy __ Your place to buy and sell all things handmade.jfif', 15),
(43, 94, 'blanc', 'RINGS.jfif', 17),
(44, 95, 'vert', 'GREEN CATS EYE BRAIDED WIRE RING.jfif', 20),
(45, 95, 'blanc', 'OPALITE BRAIDED WIRE RING.jfif', 15),
(49, 97, 'blanc', 'téléchargement (17).jfif', 12),
(50, 97, 'vert', 'téléchargement (222).jfif', 14),
(51, 97, 'violet', 'téléchargement (18).jfif', 17),
(52, 98, 'rose', 'Pink Beaded Acrylic Butterfly Elastic Beaded Bracelet For Women.jfif', 15),
(53, 98, 'violet', '1pc Y2K Style Youthful & Lively Colorful Acrylic Butterfly Elastic Beaded Bracelet For Women (1).jfif', 12),
(54, 98, 'blue', 'Butterfly Charm Beaded Bracelet.jfif', 13),
(55, 98, 'orange', 'Butterfly Charm Beaded Bracelet (1).jfif', 17),
(56, 99, 'rose', 'téléchargement (23).jfif', 12),
(57, 99, 'violet', '⋆˚ 𝜗𝜚˚⋆.jfif', 13),
(58, 99, 'blue', '𝜗𝜚 _ blue and white floral bracelet.jfif', 10),
(59, 99, 'blanc', 'Cavigliera di perline.jfif', 10),
(60, 100, 'blue', 'mo.png', 12),
(61, 100, 'rose', '1pc Pink Ins Style Moon & Star Beaded Bracelet Suitable For Women\'s Daily Wear (1).jfif', 10),
(62, 100, 'violet', '1pc Purple Cat Eye Stone Beaded Bracelet With Moon & Star Charm For Women, Birthday_Christmas Gift.jfif', 11);

-- --------------------------------------------------------

--
-- Structure de la table `produit_images`
--

CREATE TABLE `produit_images` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sous_categorie`
--

CREATE TABLE `sous_categorie` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `categorie_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sous_categorie`
--

INSERT INTO `sous_categorie` (`id`, `nom`, `categorie_id`) VALUES
(1, 'Bougie', 1),
(2, 'Textiles & Tapis', 1),
(3, 'Broderie', 1),
(4, 'Béton', 1),
(5, 'Bague', 2),
(6, 'Bracelet', 2),
(7, 'Boucle d\'oreille', 2),
(8, 'Chaînes', 2),
(9, 'Totbag', 3),
(10, 'Trousses', 3),
(11, 'Sac Laptop', 3),
(12, 'Sac au Cristal', 3);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(100) NOT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_inscription` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `email`, `mot_de_passe`, `role`, `nom`, `prenom`, `date_inscription`) VALUES
(3, 'admin@gmail.com', '12345678', 'admin', 'Admin', 'Admin', '2025-05-14 13:06:24'),
(13, 'fatimamerrouchi@gmail.com', '$2y$10$C2eVq0NIaSSeYLX2k4oC0OvTfL6oVnQUPbN.r0PW4.obaH8lNuhxa', 'client', 'merrouchi', 'fatima', '2025-05-22 07:18:44'),
(14, 'mansouri@gmail.com', '$2y$10$qzda0EM1x2Yxm7DEdGenlemk7rKBuMNhEVgUW/jJPILFvliIFGgN2', 'client', 'mansouri', 'imane', '2025-05-22 07:36:27'),
(17, 'toubel@gmail.com', '$2y$10$OUH42vzqbNmrrNxVlI4jbelZzMvnbYca0.IHioujs/woOO3sCEAcm', 'client', 'toubel', 'salma', '2025-05-26 08:25:49');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `factures`
--
ALTER TABLE `factures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateurs` (`id_user`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `produit_couleurs` (`product_color`);

--
-- Index pour la table `produit_couleurs`
--
ALTER TABLE `produit_couleurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `produit_images`
--
ALTER TABLE `produit_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `sous_categorie`
--
ALTER TABLE `sous_categorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `factures`
--
ALTER TABLE `factures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT pour la table `produit_couleurs`
--
ALTER TABLE `produit_couleurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT pour la table `produit_images`
--
ALTER TABLE `produit_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `sous_categorie`
--
ALTER TABLE `sous_categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produit_couleurs`
--
ALTER TABLE `produit_couleurs`
  ADD CONSTRAINT `produit_couleurs_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produit_images`
--
ALTER TABLE `produit_images`
  ADD CONSTRAINT `produit_images_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sous_categorie`
--
ALTER TABLE `sous_categorie`
  ADD CONSTRAINT `sous_categorie_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
