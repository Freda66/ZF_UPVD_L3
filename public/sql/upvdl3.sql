-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Dim 15 Juin 2014 à 16:57
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
  PRIMARY KEY (`idFormation`,`idStage`),
  KEY `foreignFormation` (`idFormation`),
  KEY `foreignStage` (`idStage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `concernerformationstage`
--

INSERT INTO `concernerformationstage` (`idFormation`, `idStage`) VALUES
(1, 6),
(1, 7),
(1, 9),
(2, 7),
(2, 9),
(3, 8),
(4, 6),
(4, 7),
(5, 6),
(5, 8);

-- --------------------------------------------------------

--
-- Structure de la table `demandeetudiantstage`
--

CREATE TABLE IF NOT EXISTS `demandeetudiantstage` (
  `idEtudiant` varchar(20) COLLATE utf8_bin NOT NULL,
  `idStage` int(11) NOT NULL,
  PRIMARY KEY (`idEtudiant`,`idStage`),
  KEY `demandedestage` (`idStage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `demandeetudiantstage`
--

INSERT INTO `demandeetudiantstage` (`idEtudiant`, `idStage`) VALUES
('A48154G454', 6);

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
  `etatEnseignant` int(11) NOT NULL DEFAULT '1' COMMENT '-1 => Supprimer / 0 => En attente / => 1 => Ok',
  PRIMARY KEY (`idEnseignant`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Contenu de la table `enseignant`
--

INSERT INTO `enseignant` (`idEnseignant`, `nomEnseignant`, `prenomEnseignant`, `fonctionEnseignant`, `specialiteEnseignant`, `loginEnseignant`, `mdpEnseignant`, `isResponsableSiteEnseignant`, `etatEnseignant`) VALUES
(1, 'MADELINE', 'Blaise', 'Enseignant', 'Informatique', 'bmadeline', '9ecfe277b47f4d68f32cd8b2ec170ddb', 1, 1),
(2, 'SALVAT', 'Eric', 'Enseignant', 'Informatique', 'esalvat', 'ebda79dc9abfe58d5ad78bb589a15f82', 0, 1),
(3, 'RHARMAOUI', 'Ahmed', 'Enseignant', 'Robotique', 'arharmaoui', '9228e1be5ec275244059029253e7d5cb', 1, 1),
(4, 'PECH-GOURG', 'Nicolas', 'Intervenant', 'Management', 'npechgourg', 'c26218326d905927cb263c1912019062', 0, 1),
(5, 'JANET', 'Karine', 'Administration', 'Robotique', 'kjanet', '6ab006a9ef66a8a54fb36c1a6998ce3e', 1, 1);

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
(1, 5),
(2, 3),
(2, 5),
(3, 3),
(3, 5),
(4, 1),
(4, 2),
(4, 5),
(5, 1),
(5, 2),
(5, 5);

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

CREATE TABLE IF NOT EXISTS `entreprise` (
  `idEntreprise` int(11) NOT NULL AUTO_INCREMENT,
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
  `etatEntreprise` int(11) NOT NULL DEFAULT '1' COMMENT '-1 => Supprimer / 0 => En attente / => 1 => Ok',
  PRIMARY KEY (`idEntreprise`),
  KEY `idPersonneDirigeant` (`idPersonneDirigeant`),
  KEY `siretEntreprise` (`siretEntreprise`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Contenu de la table `entreprise`
--

INSERT INTO `entreprise` (`idEntreprise`, `siretEntreprise`, `idPersonneDirigeant`, `rsEntreprise`, `adrRueEntreprise`, `adrCpEntreprise`, `adrVilleEntreprise`, `telEntreprise`, `emailEntreprise`, `loginEntreprise`, `mdpEntreprise`, `etatEntreprise`) VALUES
(1, 32467680800045, 8, 'INFORMATIQUE VERTE', 'Mas des Tilleuls', '66680', 'CANOHES', '04 68 51 48 48', 'infoverte@informatiqueverte.fr', 'infoverte', '8ceeea75f7d404732252066cbfa705f5', 1),
(2, 33092574400097, 1, 'PYRESCOM', 'Mas des Tilleuls', '66680', 'CANOHES', '04-68-68-39-68', 'direct@pyres.com', 'pyrescom', 'ea1b104d27edb766e2688fc604ed2843', 1),
(3, 42188379400032, 7, 'Square Partners', '4, rue Pierre Talrich', '66000', 'Perpignan', '04 68 34 11 77', 'contact@squarepartners.com', 'squarepartners', '497d39319ae5983d44df892e99e87faf', 1);

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
  `etatEtudiant` int(11) NOT NULL DEFAULT '1' COMMENT '-1 => Supprimer / 0 => En attente / => 1 => Ok',
  PRIMARY KEY (`ineEtudiant`),
  KEY `idFormation` (`idFormation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `etudiant`
--

INSERT INTO `etudiant` (`ineEtudiant`, `idFormation`, `nomEtudiant`, `prenomEtudiant`, `loginEtudiant`, `mdpEtudiant`, `emailEtudiant`, `etatEtudiant`) VALUES
('A48154G454', 1, 'CANO', 'Frederic', 'fcano', 'f51c17586e41aa94c14004d08dbcd4a1', 'frederic.cano@imerir.com', 1),
('F47856ADSS', 2, 'CAMPOY', 'Mickael', 'mcampoy', 'd3eae5c023b717be4a72405f88f275be', 'mickael.campoy@imerir.com', 1);

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
  `idEntrepriseTravail` int(11) DEFAULT NULL,
  `nomPersonne` varchar(250) COLLATE utf8_bin NOT NULL,
  `prenomPersonne` varchar(250) COLLATE utf8_bin NOT NULL,
  `fonctionPersonne` varchar(250) COLLATE utf8_bin DEFAULT NULL,
  `telPortPersonne` varchar(20) COLLATE utf8_bin NOT NULL,
  `telPostePersonne` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `emailPersonne` varchar(250) COLLATE utf8_bin NOT NULL,
  `etatPersonne` int(11) NOT NULL DEFAULT '1' COMMENT '-1 => Supprimer / 0 => En attente / => 1 => Ok',
  PRIMARY KEY (`idPersonne`),
  KEY `idEntrepriseTravail` (`idEntrepriseTravail`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=11 ;

--
-- Contenu de la table `personne`
--

INSERT INTO `personne` (`idPersonne`, `idEntrepriseTravail`, `nomPersonne`, `prenomPersonne`, `fonctionPersonne`, `telPortPersonne`, `telPostePersonne`, `emailPersonne`, `etatPersonne`) VALUES
(1, 2, 'Guichet', 'Danielle', 'Présidente', '04-68-68-39-68', '04-68-68-39-68', 'd.guichet@pyres.com', 1),
(7, 3, 'SESE', 'Stephane', 'Président', '04 68 34 11 77', '04 68 34 11 77', 'contact@squarepartners.com', 1),
(8, 1, 'Guichet', 'Danielle', 'Présidente', '04-68-68-39-68', '04-68-68-39-68', 'd.guichet@pyres.com', 1),
(9, 2, 'SENDRA', 'Marc', 'Cadre', '06.28.74.96.54', NULL, 'm-sendra@informatiqueverte.fr', 1),
(10, 3, 'Fauchil', 'Paul', 'Développeur', '06.48.75.66.32', '04-68-68-39-68', 'p-fauchil@square.com', 1);

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
('A48154G454', 7, 2, 15),
('A48154G454', 9, 2, 14);

-- --------------------------------------------------------

--
-- Structure de la table `soutenance`
--

CREATE TABLE IF NOT EXISTS `soutenance` (
  `idSoutenance` int(11) NOT NULL AUTO_INCREMENT,
  `dateSoutenance` datetime NOT NULL,
  `salleSoutenance` varchar(250) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`idSoutenance`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;

--
-- Contenu de la table `soutenance`
--

INSERT INTO `soutenance` (`idSoutenance`, `dateSoutenance`, `salleSoutenance`) VALUES
(14, '2014-06-20 15:00:00', 'B12'),
(15, '2014-06-25 18:00:00', 'B18');

-- --------------------------------------------------------

--
-- Structure de la table `soutenancejury`
--

CREATE TABLE IF NOT EXISTS `soutenancejury` (
  `idSoutenanceJury` int(11) NOT NULL AUTO_INCREMENT,
  `codeSoutenance` int(11) NOT NULL,
  `idPersonne` int(11) DEFAULT NULL,
  `idEnseignant` int(11) DEFAULT NULL,
  PRIMARY KEY (`idSoutenanceJury`),
  KEY `personnesoutenancejury` (`idPersonne`),
  KEY `enseignantsoutenancejury` (`idEnseignant`),
  KEY `codeSoutenance` (`codeSoutenance`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Composition du jury' AUTO_INCREMENT=22 ;

--
-- Contenu de la table `soutenancejury`
--

INSERT INTO `soutenancejury` (`idSoutenanceJury`, `codeSoutenance`, `idPersonne`, `idEnseignant`) VALUES
(17, 15, NULL, 2),
(19, 15, 9, NULL),
(20, 14, 10, NULL),
(21, 14, NULL, 2);

-- --------------------------------------------------------

--
-- Structure de la table `stage`
--

CREATE TABLE IF NOT EXISTS `stage` (
  `codeStage` int(11) NOT NULL AUTO_INCREMENT,
  `idEntreprise` int(11) NOT NULL,
  `idTuteur` int(11) NOT NULL,
  `libelleStage` varchar(250) COLLATE utf8_bin NOT NULL,
  `descriptionStage` text COLLATE utf8_bin NOT NULL,
  `dateDebutStage` date NOT NULL,
  `dateFinStage` date NOT NULL,
  `etatStage` int(11) NOT NULL DEFAULT '0' COMMENT '-1 Refuser, 0 En attente, 1 Valide',
  PRIMARY KEY (`codeStage`),
  KEY `EntrepriseStage` (`idEntreprise`),
  KEY `TuteurStage` (`idTuteur`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

--
-- Contenu de la table `stage`
--

INSERT INTO `stage` (`codeStage`, `idEntreprise`, `idTuteur`, `libelleStage`, `descriptionStage`, `dateDebutStage`, `dateFinStage`, `etatStage`) VALUES
(6, 2, 1, 'Développeur Windev', 'Stage pour du développement applicatif, afin de réaliser un logiciel de traçabilité pour la gestion des décheteries des PO.', '2014-03-01', '2014-03-31', -1),
(7, 2, 1, 'Développeur Delphi', 'Stage pour du développement applicatif, afin de réaliser un logiciel de traçabilité pour la gestion des décheteries des PO en Delphi.', '2014-04-01', '2014-05-31', 1),
(8, 3, 7, 'Développeur Web', 'Stage pour du développement web, afin de réaliser un site e-commerce pour la vente en ligne de livre, musique et film. Module de paiement en ligne compris (Paypal).', '2014-05-02', '2014-06-29', 0),
(9, 3, 7, 'Chef de projet informatique', 'Projet de gestion confié partiellement.\r\nAu seins d''une équipe de 5 collaborateurs, le chef de projet va devoir prendre en charge la totalité du management de façon optimal.', '2014-03-01', '2014-09-30', 1);

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
-- Contraintes pour la table `demandeetudiantstage`
--
ALTER TABLE `demandeetudiantstage`
  ADD CONSTRAINT `demandedestage` FOREIGN KEY (`idStage`) REFERENCES `stage` (`codeStage`),
  ADD CONSTRAINT `etudiantdemandestage` FOREIGN KEY (`idEtudiant`) REFERENCES `etudiant` (`ineEtudiant`);

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
  ADD CONSTRAINT `fk_entreprise_travail` FOREIGN KEY (`idEntrepriseTravail`) REFERENCES `entreprise` (`idEntreprise`);

--
-- Contraintes pour la table `realiseretudiantstage`
--
ALTER TABLE `realiseretudiantstage`
  ADD CONSTRAINT `foreignEnseignantTuteur` FOREIGN KEY (`idEnseignantTuteur`) REFERENCES `enseignant` (`idEnseignant`),
  ADD CONSTRAINT `foreignIneEtudiant` FOREIGN KEY (`idEtudiant`) REFERENCES `etudiant` (`ineEtudiant`),
  ADD CONSTRAINT `foreignSoutenance` FOREIGN KEY (`idSoutenance`) REFERENCES `soutenance` (`idSoutenance`),
  ADD CONSTRAINT `foreignStageEtudiant` FOREIGN KEY (`idStage`) REFERENCES `stage` (`codeStage`);

--
-- Contraintes pour la table `soutenancejury`
--
ALTER TABLE `soutenancejury`
  ADD CONSTRAINT `enseignantsoutenancejury` FOREIGN KEY (`idEnseignant`) REFERENCES `enseignant` (`idEnseignant`),
  ADD CONSTRAINT `personnesoutenancejury` FOREIGN KEY (`idPersonne`) REFERENCES `personne` (`idPersonne`),
  ADD CONSTRAINT `soutenancejury` FOREIGN KEY (`codeSoutenance`) REFERENCES `soutenance` (`idSoutenance`);

--
-- Contraintes pour la table `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `fk_entreprise_stage` FOREIGN KEY (`idEntreprise`) REFERENCES `entreprise` (`idEntreprise`),
  ADD CONSTRAINT `TuteurStage` FOREIGN KEY (`idTuteur`) REFERENCES `personne` (`idPersonne`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
