/**
 * Author:  Chris Mancuso
 * Created: Jan 26, 2017
 */

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for `Admins`
--

CREATE TABLE IF NOT EXISTS `Admins` (
    `id` int(10) NOT NULL AUTO_INCREMENT,
    `username` varchar(65) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `password` varchar(65) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
    `characterID` int(11) DEFAULT NULL,
    `securityLevel` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `admins` VALUES(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, 1);

--
-- Table structure for `Alliances`
--

CREATE TABLE IF NOT EXISTS `Alliances` (
    `Alliance` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `AllianceID` int(11) NOT NULL DEFAULT 0,
    `Ticker` varchar(10) NOT NULL DEFAULT "",
    PRIMARY KEY (`AllianceID`),
    UNIQUE KEY `AllianceID` (`AllianceID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for Corporations
--

CREATE TABLE IF NOT EXISTS `Corporations` (
    `AllianceID` varchar(20) NOT NULL DEFAULT 0,
    `Corporation` varchar(255) NOT NULL DEFAULT "",
    `CorporationID` varchar(20) NOT NULL DEFAULT 0,
    `MemberCount` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`CorporationID`),
    UNIQUE KEY `CorporationID` (`CorporationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for Members
--

CREATE TABLE IF NOT EXISTS `Members` (
    `Corporation` varchar(255) NOT NULL,
    `CorporationID` varchar(20) NOT NULL,
    `Character` varchar(255) NOT NULL,
    `CharacterID` varchar(20) NOT NULL,
    PRIMARY KEY (`CharacterID`),
    UNIQUE KEY `CharacterID` (`CharacterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for Logs
--

CREATE TABLE IF NOT EXISTS `Logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `time` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
    `entry` varchar(255) COLLATE utf9_unicode_ci NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for Users
--

CREATE TABLE IF NOT EXISTS `Users` (
    `entry` int(11) NOT NULL AUTO_INCREMENT,
    `characterID` varchar(20) DEFAULT NULL,
    `blue` varchar(255) DEFAULT NULL,
    `TSDatabaseID` int(45) DEFAULT NULL,
    `TSUniqueID` varchar(255) DEFAULT NULL,
    `TSName` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;