-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 13 Juillet 2017 à 23:18
-- Version du serveur :  5.7.11-0ubuntu6
-- Version de PHP :  7.0.18-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `resume` text NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `article`
--

INSERT INTO `article` (`id`, `id_user`, `id_tag`, `title`, `resume`, `content`, `date`) VALUES
(1, 1, 1, 'Ivre, il refait tous les challenges en un week-end sans dormir', 'Ou comment j\'ai appris plein de choses en faisant un nouvelle fois tous les challenges que j\'avais loupé', 'Ou comment j\'ai appris plein de choses en faisant un nouvelle fois tous les challenges que j\'avais loupé', '2017-07-13'),
(2, 2, 3, 'hdfghPOO, that is the qufghdfghdfgdfghh', 'La POO est-elle vraiment indispensable pour gfhdfhgdfgd?fghdfgh', 'La POO est-elle vraiment indispensable pour chaque architecture?', '2017-07-04'),
(3, 1, 1, 'ghdfghfdghdffghdfhgdfghmir', 'Ou comment j\'ai appris plein de choses en faisant un nouvelle fois tous les challenges que j\'avais loupé', 'Ou comment j\'ai appris plein de choses en faisant un nouvelle fois tous les challenges que j\'avais loupé', '2017-07-13'),
(4, 2, 3, 'POO or not POO, that is the question.', 'La POO est-elle vraiment indispensable pour chaque architecture?', 'La POO est-elle vraiment indispensable pour chaque architecture?', '2017-07-04'),
(5, 1, 1, 'zerze', 'zerzer', 'zerze', '2017-07-13'),
(6, 12, 12, 'qzdqzd', 'qdqdz', 'qzdqzd', '2017-07-13'),
(7, 12, 12, 'qzdqzd', 'qdqdz', 'qzdqzd', '2017-07-13'),
(8, 23, 23, 'zaze', 'azeaze', 'azeaez', '2017-07-13');

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `id_article` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `comment`
--

INSERT INTO `comment` (`id`, `id_article`, `id_user`, `content`, `date`) VALUES
(1, 1, 2, 'Je teste l\'affichage des commentaires...', '2017-07-13'),
(2, 1, 1, 'Je teste un second commentaires', '2017-07-12');

-- --------------------------------------------------------

--
-- Structure de la table `tag`
--

CREATE TABLE `tag` (
  `id` int(11) NOT NULL,
  `label` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `tag`
--

INSERT INTO `tag` (`id`, `label`) VALUES
(1, 'MaVieDeDev'),
(2, 'TeamBack'),
(3, 'TeamFront'),
(4, 'Collaboration');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(20) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `pseudo`, `email`, `password`) VALUES
(1, 'jmoulis', 'julien.moulis@moulis.me', 'test'),
(2, 'mTranchant', 'marie.tv@hotmail.fr', 'test');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pseudo` (`pseudo`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
