-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Dim 23 Février 2014 à 09:53
-- Version du serveur: 5.6.12-log
-- Version de PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `upvdl3`
--
CREATE DATABASE IF NOT EXISTS `upvdl3` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `upvdl3`;

-- --------------------------------------------------------

--
-- Structure de la table `concernerformationstage`
--

CREATE TABLE IF NOT EXISTS `concernerformationstage` (
  `idFormation` int(11) NOT NULL,
  `idStage` int(11) NOT NULL,
  KEY `foreignFormation` (`idFormation`),
  KEY `foreignStage` (`idStage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `concernerformationstage`
--

INSERT INTO `concernerformationstage` (`idFormation`, `idStage`) VALUES
(1, 1),
(4, 1),
(5, 1);

-- --------------------------------------------------------

--
-- Structure de la table `enseignant`
--

CREATE TABLE IF NOT EXISTS `enseignant` (
  `idEnseignant` int(11) NOT NULL AUTO_INCREMENT,
  `nomEnseignant` varchar(250) COLLATE utf8_bin NOT NULL,
  `prenomEnseignant` varchar(250) COLLATE utf8_bin NOT NULL,
  `fonctionEnseignant` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `specialiteEnseignant` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `loginEnseignant` varchar(250) COLLATE utf8_bin NOT NULL,
  `mdpEnseignant` varchar(250) COLLATE utf8_bin NOT NULL,
  `isResponsableSiteEnseignant` int(11) NOT NULL COMMENT '0 => Non & 1 => 1',
  PRIMARY KEY (`idEnseignant`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Contenu de la table `enseignant`
--

INSERT INTO `enseignant` (`idEnseignant`, `nomEnseignant`, `prenomEnseignant`, `fonctionEnseignant`, `specialiteEnseignant`, `loginEnseignant`, `mdpEnseignant`, `isResponsableSiteEnseignant`) VALUES
(1, 'MADELINE', 'Blaise', 'Enseignant', 'Informatique', 'bmadeline', 'bmadeline', 1),
(2, 'SALVAT', 'Eric', 'Enseignant', 'Informatique', 'esalvat', 'esalvat', 0),
(3, 'RHARMAOUI', 'Ahmed', 'Enseignant', 'Robotique', 'arharmaoui', 'arharmaoui', 1),
(4, 'PECH-GOURG', 'Nicolas', 'Intervenant', 'Management', 'npechgourg', 'npechgourg', 0);

-- --------------------------------------------------------

--
-- Structure de la table `enseignerformationenseignant`
--

CREATE TABLE IF NOT EXISTS `enseignerformationenseignant` (
  `idFormation` int(11) NOT NULL,
  `idEnseignant` int(11) NOT NULL,
  PRIMARY KEY (`idEnseignant`,`idFormation`),
  KEY `foreignFormation` (`idFormation`),
  KEY `foreignEnseignant` (`idEnseignant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `enseignerformationenseignant`
--

INSERT INTO `enseignerformationenseignant` (`idFormation`, `idEnseignant`) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 3),
(4, 1),
(4, 2),
(5, 1),
(5, 2);

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

CREATE TABLE IF NOT EXISTS `entreprise` (
  `siretEntreprise` bigint(14) NOT NULL,
  `idPersonneDirigeant` int(11) DEFAULT NULL COMMENT 'Clé étrangère de la table Personne',
  `rsEntreprise` varchar(250) COLLATE utf8_bin NOT NULL,
  `adrRueEntreprise` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `adrCpEntreprise` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `adrVilleEntreprise` varchar(250) COLLATE utf8_bin NOT NULL,
  `telEntreprise` varchar(20) COLLATE utf8_bin NOT NULL,
  `emailEntreprise` varchar(250) COLLATE utf8_bin NOT NULL,
  `loginEntreprise` varchar(250) COLLATE utf8_bin NOT NULL,
  `mdpEntreprise` varchar(250) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`siretEntreprise`),
  KEY `idPersonneDirigeant` (`idPersonneDirigeant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `entreprise`
--

INSERT INTO `entreprise` (`siretEntreprise`, `idPersonneDirigeant`, `rsEntreprise`, `adrRueEntreprise`, `adrCpEntreprise`, `adrVilleEntreprise`, `telEntreprise`, `emailEntreprise`, `loginEntreprise`, `mdpEntreprise`) VALUES
(32467680800045, 1, 'INFORMATIQUE VERTE', 'Mas des Tilleuls', '66680', 'CANOHES', '04 68 51 48 48', 'infoverte@informatiqueverte.fr', 'infoverte', 'infoverte'),
(33092574400097, 1, 'PYRESCOM', 'Mas des Tilleuls', '66680', 'CANOHES', '04-68-68-39-68', 'direct@pyres.com', 'pyrescom', 'pyrescom'),
(42188379400032, 7, 'Square Partners', '4, rue Pierre Talrich', '66000', 'Perpignan', '04 68 34 11 77', 'contact@squarepartners.com', 'squarepartners', 'squarepartners');

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE IF NOT EXISTS `etudiant` (
  `ineEtudiant` varchar(20) COLLATE utf8_bin NOT NULL,
  `idFormation` int(11) NOT NULL,
  `nomEtudiant` varchar(250) COLLATE utf8_bin NOT NULL,
  `prenomEtudiant` varchar(250) COLLATE utf8_bin NOT NULL,
  `loginEtudiant` varchar(250) COLLATE utf8_bin NOT NULL,
  `mdpEtudiant` varchar(250) COLLATE utf8_bin NOT NULL,
  `emailEtudiant` varchar(250) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ineEtudiant`),
  KEY `idFormation` (`idFormation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `etudiant`
--

INSERT INTO `etudiant` (`ineEtudiant`, `idFormation`, `nomEtudiant`, `prenomEtudiant`, `loginEtudiant`, `mdpEtudiant`, `emailEtudiant`) VALUES
('A48154G454', 1, 'CANO', 'Frederic', 'fcano', 'fcano', 'frederic.cano@imerir.com'),
('F14784A454', 1, 'CAMPOY', 'Mickael', 'mcampoy', 'mcampoy', 'mickael.campoy@imerir.com');

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

CREATE TABLE IF NOT EXISTS `formation` (
  `codeFormation` int(11) NOT NULL AUTO_INCREMENT,
  `idEnseignantResponsable` int(11) NOT NULL COMMENT 'Enseignant responsable de la formation (administrativement)',
  `libelleFormation` varchar(250) COLLATE utf8_bin NOT NULL COMMENT 'Licence, Master,...',
  `niveauFormation` int(11) DEFAULT NULL COMMENT '1,2,3,...',
  `specialiteFormation` varchar(250) COLLATE utf8_bin NOT NULL COMMENT 'Informatique,...',
  PRIMARY KEY (`codeFormation`),
  KEY `EnseignantResponsableFormation` (`idEnseignantResponsable`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

--
-- Contenu de la table `formation`
--

INSERT INTO `formation` (`codeFormation`, `idEnseignantResponsable`, `libelleFormation`, `niveauFormation`, `specialiteFormation`) VALUES
(1, 1, 'Licence', 3, 'Informatique'),
(2, 3, 'Master', 1, 'Robotique'),
(3, 3, 'Master', 2, 'Robotique'),
(4, 1, 'Master', 1, 'Informatique'),
(5, 1, 'Master', 2, 'Informatique');

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

CREATE TABLE IF NOT EXISTS `personne` (
  `idPersonne` int(11) NOT NULL AUTO_INCREMENT,
  `idEntrepriseTravail` bigint(14) DEFAULT NULL,
  `nomPersonne` varchar(250) COLLATE utf8_bin NOT NULL,
  `prenomPersonne` varchar(250) COLLATE utf8_bin NOT NULL,
  `fonctionPersonne` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `telPortPersonne` varchar(20) COLLATE utf8_bin NOT NULL,
  `telPostePersonne` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `emailPersonne` varchar(250) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`idPersonne`),
  KEY `EntrepriseTravail` (`idEntrepriseTravail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Contenu de la table `personne`
--

INSERT INTO `personne` (`idPersonne`, `idEntrepriseTravail`, `nomPersonne`, `prenomPersonne`, `fonctionPersonne`, `telPortPersonne`, `telPostePersonne`, `emailPersonne`) VALUES
(1, 33092574400097, 'Guichet', 'Danielle', 'Présidente', '04-68-68-39-68', '04-68-68-39-68', 'd.guichet@pyres.com'),
(7, 42188379400032, 'SESE', 'Stephane', 'President', '04 68 34 11 77', '04 68 34 11 77', 'contact@squarepartners.com');

-- --------------------------------------------------------

--
-- Structure de la table `realiseretudiantstage`
--

CREATE TABLE IF NOT EXISTS `realiseretudiantstage` (
  `idEtudiant` varchar(20) COLLATE utf8_bin NOT NULL,
  `idStage` int(11) NOT NULL,
  `idEnseignantTuteur` int(11) DEFAULT NULL,
  `idSoutenance` int(11) DEFAULT NULL,
  PRIMARY KEY (`idEtudiant`,`idStage`),
  KEY `foreignStageEtudiant` (`idStage`),
  KEY `idEnseignantTuteur` (`idEnseignantTuteur`),
  KEY `idSoutenance` (`idSoutenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `realiseretudiantstage`
--

INSERT INTO `realiseretudiantstage` (`idEtudiant`, `idStage`, `idEnseignantTuteur`, `idSoutenance`) VALUES
('A48154G454', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `soutenance`
--

CREATE TABLE IF NOT EXISTS `soutenance` (
  `idSoutenance` int(11) NOT NULL AUTO_INCREMENT,
  `dateSoutenance` date NOT NULL,
  `salleSoutenance` varchar(250) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`idSoutenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `stage`
--

CREATE TABLE IF NOT EXISTS `stage` (
  `codeStage` int(11) NOT NULL AUTO_INCREMENT,
  `idEntreprise` bigint(14) NOT NULL,
  `idTuteur` int(11) NOT NULL,
  `libelleStage` varchar(250) COLLATE utf8_bin NOT NULL,
  `descriptionStage` text COLLATE utf8_bin NOT NULL,
  `dateDebutStage` date NOT NULL,
  `dateFinStage` date NOT NULL,
  `etatStage` int(11) NOT NULL DEFAULT '0' COMMENT '-1 Refuser, 0 En attente, 1 Valide',
  PRIMARY KEY (`codeStage`),
  KEY `EntrepriseStage` (`idEntreprise`),
  KEY `TuteurStage` (`idTuteur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Contenu de la table `stage`
--

INSERT INTO `stage` (`codeStage`, `idEntreprise`, `idTuteur`, `libelleStage`, `descriptionStage`, `dateDebutStage`, `dateFinStage`, `etatStage`) VALUES
(1, 33092574400097, 1, 'Développeur Web', 'Stage pour du développement web, afin de réaliser un site e-commerce pour la vente de livre, musique et film. Module de paiement en ligne (Paypal).', '2014-03-02', '2014-05-30', 0);

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `concernerformationstage`
--
ALTER TABLE `concernerformationstage`
  ADD CONSTRAINT `foreignFormationStage` FOREIGN KEY (`idFormation`) REFERENCES `formation` (`codeFormation`),
  ADD CONSTRAINT `foreignStage` FOREIGN KEY (`idStage`) REFERENCES `stage` (`codeStage`);

--
-- Contraintes pour la table `enseignerformationenseignant`
--
ALTER TABLE `enseignerformationenseignant`
  ADD CONSTRAINT `foreignEnseignant` FOREIGN KEY (`idEnseignant`) REFERENCES `enseignant` (`idEnseignant`),
  ADD CONSTRAINT `foreignFormation` FOREIGN KEY (`idFormation`) REFERENCES `formation` (`codeFormation`);

--
-- Contraintes pour la table `entreprise`
--
ALTER TABLE `entreprise`
  ADD CONSTRAINT `PersonneDirigeant` FOREIGN KEY (`idPersonneDirigeant`) REFERENCES `personne` (`idPersonne`);

--
-- Contraintes pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD CONSTRAINT `EtudiantFormation` FOREIGN KEY (`idFormation`) REFERENCES `formation` (`codeFormation`);

--
-- Contraintes pour la table `formation`
--
ALTER TABLE `formation`
  ADD CONSTRAINT `enseignantResponsable` FOREIGN KEY (`idEnseignantResponsable`) REFERENCES `enseignant` (`idEnseignant`);

--
-- Contraintes pour la table `personne`
--
ALTER TABLE `personne`
  ADD CONSTRAINT `EntreprisePersonneTravailler` FOREIGN KEY (`idEntrepriseTravail`) REFERENCES `entreprise` (`siretEntreprise`);

--
-- Contraintes pour la table `realiseretudiantstage`
--
ALTER TABLE `realiseretudiantstage`
  ADD CONSTRAINT `foreignSoutenance` FOREIGN KEY (`idSoutenance`) REFERENCES `soutenance` (`idSoutenance`),
  ADD CONSTRAINT `foreignEnseignantTuteur` FOREIGN KEY (`idEnseignantTuteur`) REFERENCES `enseignant` (`idEnseignant`),
  ADD CONSTRAINT `foreignIneEtudiant` FOREIGN KEY (`idEtudiant`) REFERENCES `etudiant` (`ineEtudiant`),
  ADD CONSTRAINT `foreignStageEtudiant` FOREIGN KEY (`idStage`) REFERENCES `stage` (`codeStage`);

--
-- Contraintes pour la table `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `TuteurStage` FOREIGN KEY (`idTuteur`) REFERENCES `personne` (`idPersonne`),
  ADD CONSTRAINT `EntrepriseStage` FOREIGN KEY (`idEntreprise`) REFERENCES `entreprise` (`siretEntreprise`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
