-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Lun 05 Décembre 2011 à 07:19
-- Version du serveur: 5.5.16
-- Version de PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES latin1 */;

--
-- Base de données: `1`
--

-- --------------------------------------------------------

--
-- Structure de la table `nuked_action`
--

DROP TABLE IF EXISTS `nuked_action`;
CREATE TABLE IF NOT EXISTS `nuked_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(30) NOT NULL DEFAULT '0',
  `pseudo` text NOT NULL,
  `action` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_banned`
--

DROP TABLE IF EXISTS `nuked_banned`;
CREATE TABLE IF NOT EXISTS `nuked_banned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL DEFAULT '',
  `pseudo` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(80) NOT NULL DEFAULT '',
  `date` varchar(20) DEFAULT NULL,
  `dure` varchar(20) DEFAULT NULL,
  `texte` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_block`
--

DROP TABLE IF EXISTS `nuked_block`;
CREATE TABLE IF NOT EXISTS `nuked_block` (
  `bid` int(10) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '0',
  `position` int(2) NOT NULL DEFAULT '0',
  `module` varchar(100) NOT NULL DEFAULT '',
  `titre` text NOT NULL,
  `content` text NOT NULL,
  `type` varchar(30) NOT NULL DEFAULT '0',
  `nivo` int(1) NOT NULL DEFAULT '0',
  `page` text NOT NULL,
  PRIMARY KEY (`bid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Contenu de la table `nuked_block`
--

INSERT INTO `nuked_block` VALUES(1, 2, 1, '', 'Login', '', 'login', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(2, 1, 1, '', 'Menu', '[News]|News||0|NEWLINE[Archives]|Archives||0|NEWLINE[Forum]|Forum||0|NEWLINE[Download]|Téléchargements||0|NEWLINE[Members]|Membres||0|NEWLINE[Team]|Team||0|NEWLINE[Defy]|Nous Défier||0|NEWLINE[Recruit]|Recrutement||0|NEWLINE[Sections]|Articles||0|NEWLINE[Server]|Serveurs||0|NEWLINE[Links]|Liens Web||0|NEWLINE[Calendar]|Calendrier||0|NEWLINE[Gallery]|Galerie||0|NEWLINE[Wars]|Matchs||0|NEWLINE[Irc]|IrC||0|NEWLINE[Guestbook]|Livre d''Or||0|NEWLINE[Search]|Recherche||0|NEWLINE|<b>Membre</b>||1|NEWLINE[User]|Compte||1|NEWLINE|<b>Admin</b>||2|NEWLINE[Admin]|Administration||2|', 'menu', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(3, 1, 2, 'Search', 'Recherche', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(4, 2, 2, '', 'Sondage', '', 'survey', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(5, 2, 3, 'Wars', 'Matchs', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(6, 1, 3, 'Stats', 'Stats', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(7, 0, 0, 'Irc', 'Irc Awards', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(8, 0, 0, 'Server', 'Serveur monitor', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(9, 0, 0, '', 'Suggestion', '', 'suggest', 1, 'Tous');
INSERT INTO `nuked_block` VALUES(10, 0, 0, 'Textbox', 'Tribune libre', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` VALUES(11, 1, 4, '', 'Partenaires', '<div style="text-align: center;padding: 10px;"><a href="http://www.nuked-klan.org" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/ban.png" alt="" title="Nuked-klaN CMS" /></a></div><div style="text-align: center;padding: 10px;"><a href="http://www.nitroserv.fr" onclick="window.open(this.href); return false;"><img style="border: 0;" src="http://www.nitroserv.com/images/logo_88x31.jpg" alt="" title="Location de serveurs de jeux" /></a></div>', 'html', 0, 'Tous');

-- --------------------------------------------------------

--
-- Structure de la table `nuked_calendar`
--

DROP TABLE IF EXISTS `nuked_calendar`;
CREATE TABLE IF NOT EXISTS `nuked_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `date_jour` int(2) DEFAULT NULL,
  `date_mois` int(2) DEFAULT NULL,
  `date_an` int(4) DEFAULT NULL,
  `heure` varchar(5) NOT NULL DEFAULT '',
  `auteur` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_comment`
--

DROP TABLE IF EXISTS `nuked_comment`;
CREATE TABLE IF NOT EXISTS `nuked_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL DEFAULT '0',
  `im_id` int(100) DEFAULT NULL,
  `autor` text,
  `autor_id` varchar(20) NOT NULL DEFAULT '',
  `titre` text NOT NULL,
  `comment` text,
  `date` varchar(12) DEFAULT NULL,
  `autor_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `im_id` (`im_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_comment_mod`
--

DROP TABLE IF EXISTS `nuked_comment_mod`;
CREATE TABLE IF NOT EXISTS `nuked_comment_mod` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` text NOT NULL,
  `active` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `nuked_comment_mod`
--

INSERT INTO `nuked_comment_mod` VALUES(1, 'news', 1);
INSERT INTO `nuked_comment_mod` VALUES(2, 'download', 1);
INSERT INTO `nuked_comment_mod` VALUES(3, 'links', 1);
INSERT INTO `nuked_comment_mod` VALUES(4, 'survey', 1);
INSERT INTO `nuked_comment_mod` VALUES(5, 'wars', 1);
INSERT INTO `nuked_comment_mod` VALUES(6, 'gallery', 1);
INSERT INTO `nuked_comment_mod` VALUES(7, 'sections', 1);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_config`
--

DROP TABLE IF EXISTS `nuked_config`;
CREATE TABLE IF NOT EXISTS `nuked_config` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `nuked_config`
--

INSERT INTO `nuked_config` VALUES('time_generate', 'on');
INSERT INTO `nuked_config` VALUES('dateformat', '%d/%m/%Y - %H:%M:%S');
INSERT INTO `nuked_config` VALUES('datezone', '+0100');
INSERT INTO `nuked_config` VALUES ('video_editeur', 'on');
INSERT INTO `nuked_config` VALUES ('scayt_editeur', 'on');
INSERT INTO `nuked_config` VALUES('version', '1.7.9');
INSERT INTO `nuked_config` VALUES('date_install', '1323049522');
INSERT INTO `nuked_config` VALUES('langue', 'french');
INSERT INTO `nuked_config` VALUES('name', 'Nuked-klaN 1.7.9');
INSERT INTO `nuked_config` VALUES('slogan', 'PHP 4 Gamers');
INSERT INTO `nuked_config` VALUES('tag_pre', '');
INSERT INTO `nuked_config` VALUES('tag_suf', '');
INSERT INTO `nuked_config` VALUES('url', 'http://localhost/nk');
INSERT INTO `nuked_config` VALUES('mail', 'mail@hotmail.com');
INSERT INTO `nuked_config` VALUES('footmessage', '');
INSERT INTO `nuked_config` VALUES('nuked_status', 'open');
INSERT INTO `nuked_config` VALUES('index_site', 'News');
INSERT INTO `nuked_config` VALUES('theme', 'Impact_Nk');
INSERT INTO `nuked_config` VALUES('keyword', '');
INSERT INTO `nuked_config` VALUES('description', '');
INSERT INTO `nuked_config` VALUES('inscription', 'on');
INSERT INTO `nuked_config` VALUES('inscription_mail', '');
INSERT INTO `nuked_config` VALUES('inscription_avert', 'off');
INSERT INTO `nuked_config` VALUES('inscription_charte', '');
INSERT INTO `nuked_config` VALUES('validation', 'mail');
INSERT INTO `nuked_config` VALUES('user_delete', 'on');
INSERT INTO `nuked_config` VALUES('video_editeur', 'on');
INSERT INTO `nuked_config` VALUES('suggest_avert', '');
INSERT INTO `nuked_config` VALUES('irc_chan', 'nuked-klan');
INSERT INTO `nuked_config` VALUES('irc_serv', 'quakenet.org');
INSERT INTO `nuked_config` VALUES('server_ip', '');
INSERT INTO `nuked_config` VALUES('server_port', '');
INSERT INTO `nuked_config` VALUES('server_pass', '');
INSERT INTO `nuked_config` VALUES('server_game', '');
INSERT INTO `nuked_config` VALUES('forum_title', '');
INSERT INTO `nuked_config` VALUES('forum_desc', '');
INSERT INTO `nuked_config` VALUES('forum_ranuked_team', 'off');
INSERT INTO `nuked_config` VALUES('forum_field_max', '10');
INSERT INTO `nuked_config` VALUES('forum_file', 'on');
INSERT INTO `nuked_config` VALUES('forum_file_level', '1');
INSERT INTO `nuked_config` VALUES('forum_file_maxsize', '1000');
INSERT INTO `nuked_config` VALUES('thread_forum_page', '20');
INSERT INTO `nuked_config` VALUES('mess_forum_page', '10');
INSERT INTO `nuked_config` VALUES('hot_topic', '20');
INSERT INTO `nuked_config` VALUES('post_flood', '10');
INSERT INTO `nuked_config` VALUES('gallery_title', '');
INSERT INTO `nuked_config` VALUES('max_img_line', '2');
INSERT INTO `nuked_config` VALUES('max_img', '6');
INSERT INTO `nuked_config` VALUES('max_news', '5');
INSERT INTO `nuked_config` VALUES('max_download', '10');
INSERT INTO `nuked_config` VALUES('hide_download', 'on');
INSERT INTO `nuked_config` VALUES('max_liens', '10');
INSERT INTO `nuked_config` VALUES('max_sections', '10');
INSERT INTO `nuked_config` VALUES('max_wars', '30');
INSERT INTO `nuked_config` VALUES('max_archives', '30');
INSERT INTO `nuked_config` VALUES('max_members', '30');
INSERT INTO `nuked_config` VALUES('max_shout', '20');
INSERT INTO `nuked_config` VALUES('mess_guest_page', '10');
INSERT INTO `nuked_config` VALUES('sond_delay', '24');
INSERT INTO `nuked_config` VALUES('level_analys', '-1');
INSERT INTO `nuked_config` VALUES('visit_delay', '10');
INSERT INTO `nuked_config` VALUES('recrute', '1');
INSERT INTO `nuked_config` VALUES('recrute_charte', '');
INSERT INTO `nuked_config` VALUES('recrute_mail', '');
INSERT INTO `nuked_config` VALUES('recrute_inbox', '');
INSERT INTO `nuked_config` VALUES('defie_charte', '');
INSERT INTO `nuked_config` VALUES('defie_mail', '');
INSERT INTO `nuked_config` VALUES('defie_inbox', '');
INSERT INTO `nuked_config` VALUES('birthday', 'all');
INSERT INTO `nuked_config` VALUES('avatar_upload', 'on');
INSERT INTO `nuked_config` VALUES('avatar_url', 'on');
INSERT INTO `nuked_config` VALUES('cookiename', 'nuked');
INSERT INTO `nuked_config` VALUES('sess_inactivemins', '5');
INSERT INTO `nuked_config` VALUES('sess_days_limit', '365');
INSERT INTO `nuked_config` VALUES('nbc_timeout', '300');
INSERT INTO `nuked_config` VALUES('screen', 'on');
INSERT INTO `nuked_config` VALUES('contact_mail', 'contact@nuked-klan.org');
INSERT INTO `nuked_config` VALUES('contact_flood', '60');

-- --------------------------------------------------------

--
-- Structure de la table `nuked_contact`
--

DROP TABLE IF EXISTS `nuked_contact`;
CREATE TABLE IF NOT EXISTS `nuked_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `email` varchar(80) NOT NULL DEFAULT '',
  `nom` varchar(200) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `date` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `titre` (`titre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_defie`
--

DROP TABLE IF EXISTS `nuked_defie`;
CREATE TABLE IF NOT EXISTS `nuked_defie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `send` varchar(12) NOT NULL DEFAULT '',
  `pseudo` text NOT NULL,
  `clan` text NOT NULL,
  `mail` varchar(80) NOT NULL DEFAULT '',
  `icq` varchar(50) NOT NULL DEFAULT '',
  `irc` varchar(50) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  `pays` text NOT NULL,
  `date` varchar(20) NOT NULL DEFAULT '',
  `heure` varchar(10) NOT NULL DEFAULT '',
  `serveur` text NOT NULL,
  `game` int(11) NOT NULL DEFAULT '0',
  `type` text NOT NULL,
  `map` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_discussion`
--

DROP TABLE IF EXISTS `nuked_discussion`;
CREATE TABLE IF NOT EXISTS `nuked_discussion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(30) NOT NULL DEFAULT '0',
  `pseudo` text NOT NULL,
  `texte` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_downloads`
--

DROP TABLE IF EXISTS `nuked_downloads`;
CREATE TABLE IF NOT EXISTS `nuked_downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(12) NOT NULL DEFAULT '',
  `taille` varchar(6) NOT NULL DEFAULT '0',
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `count` int(10) NOT NULL DEFAULT '0',
  `url` varchar(200) NOT NULL DEFAULT '',
  `url2` varchar(200) NOT NULL DEFAULT '',
  `broke` int(11) NOT NULL DEFAULT '0',
  `url3` varchar(200) NOT NULL DEFAULT '',
  `level` int(1) NOT NULL DEFAULT '0',
  `hit` int(11) NOT NULL DEFAULT '0',
  `edit` varchar(12) NOT NULL DEFAULT '',
  `screen` varchar(200) NOT NULL DEFAULT '',
  `autor` text NOT NULL,
  `url_autor` varchar(200) NOT NULL DEFAULT '',
  `comp` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_downloads_cat`
--

DROP TABLE IF EXISTS `nuked_downloads_cat`;
CREATE TABLE IF NOT EXISTS `nuked_downloads_cat` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL DEFAULT '0',
  `titre` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `level` int(1) NOT NULL DEFAULT '0',
  `position` int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_erreursql`
--

DROP TABLE IF EXISTS `nuked_erreursql`;
CREATE TABLE IF NOT EXISTS `nuked_erreursql` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(30) NOT NULL DEFAULT '0',
  `lien` text NOT NULL,
  `texte` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_fichiers_joins`
--

DROP TABLE IF EXISTS `nuked_fichiers_joins`;
CREATE TABLE IF NOT EXISTS `nuked_fichiers_joins` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL DEFAULT '',
  `im_id` int(10) NOT NULL DEFAULT '0',
  `type` varchar(30) NOT NULL DEFAULT '',
  `url` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `im_id` (`im_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums`
--

DROP TABLE IF EXISTS `nuked_forums`;
CREATE TABLE IF NOT EXISTS `nuked_forums` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `cat` int(11) NOT NULL DEFAULT '0',
  `nom` text NOT NULL,
  `comment` text NOT NULL,
  `moderateurs` text NOT NULL,
  `niveau` int(1) NOT NULL DEFAULT '0',
  `level` int(1) NOT NULL DEFAULT '0',
  `ordre` int(5) NOT NULL DEFAULT '0',
  `level_poll` int(1) NOT NULL DEFAULT '0',
  `level_vote` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `nuked_forums`
--

INSERT INTO `nuked_forums` VALUES(1, 1, 'Forum', 'Test Forum', '', 0, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums_cat`
--

DROP TABLE IF EXISTS `nuked_forums_cat`;
CREATE TABLE IF NOT EXISTS `nuked_forums_cat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `ordre` int(5) NOT NULL DEFAULT '0',
  `niveau` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `nuked_forums_cat`
--

INSERT INTO `nuked_forums_cat` VALUES(1, 'Categorie 1', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums_messages`
--

DROP TABLE IF EXISTS `nuked_forums_messages`;
CREATE TABLE IF NOT EXISTS `nuked_forums_messages` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `titre` text NOT NULL,
  `txt` text NOT NULL,
  `date` varchar(12) NOT NULL DEFAULT '',
  `edition` text NOT NULL,
  `auteur` text NOT NULL,
  `auteur_id` varchar(20) NOT NULL DEFAULT '',
  `auteur_ip` varchar(20) NOT NULL DEFAULT '',
  `bbcodeoff` int(1) NOT NULL DEFAULT '0',
  `smileyoff` int(1) NOT NULL DEFAULT '0',
  `cssoff` int(1) NOT NULL DEFAULT '0',
  `usersig` int(1) NOT NULL DEFAULT '0',
  `emailnotify` int(1) NOT NULL DEFAULT '0',
  `thread_id` int(5) NOT NULL DEFAULT '0',
  `forum_id` mediumint(10) NOT NULL DEFAULT '0',
  `file` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `auteur_id` (`auteur_id`),
  KEY `thread_id` (`thread_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums_options`
--

DROP TABLE IF EXISTS `nuked_forums_options`;
CREATE TABLE IF NOT EXISTS `nuked_forums_options` (
  `id` int(11) NOT NULL DEFAULT '0',
  `poll_id` int(11) NOT NULL DEFAULT '0',
  `option_text` varchar(255) NOT NULL DEFAULT '',
  `option_vote` int(11) NOT NULL DEFAULT '0',
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums_poll`
--

DROP TABLE IF EXISTS `nuked_forums_poll`;
CREATE TABLE IF NOT EXISTS `nuked_forums_poll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL DEFAULT '0',
  `titre` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums_rank`
--

DROP TABLE IF EXISTS `nuked_forums_rank`;
CREATE TABLE IF NOT EXISTS `nuked_forums_rank` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL DEFAULT '',
  `type` int(1) NOT NULL DEFAULT '0',
  `post` int(4) NOT NULL DEFAULT '0',
  `image` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `nuked_forums_rank`
--

INSERT INTO `nuked_forums_rank` VALUES(1, 'Newbie', 0, 0, 'modules/Forum/images/rank/star1.gif');
INSERT INTO `nuked_forums_rank` VALUES(2, 'Junior Member', 0, 10, 'modules/Forum/images/rank/star2.gif');
INSERT INTO `nuked_forums_rank` VALUES(3, 'Member', 0, 100, 'modules/Forum/images/rank/star3.gif');
INSERT INTO `nuked_forums_rank` VALUES(4, 'Senior Member', 0, 500, 'modules/Forum/images/rank/star4.gif');
INSERT INTO `nuked_forums_rank` VALUES(5, 'Posting Freak', 0, 1000, 'modules/Forum/images/rank/star5.gif');
INSERT INTO `nuked_forums_rank` VALUES(6, 'Moderator', 1, 0, 'modules/Forum/images/rank/mod.gif');
INSERT INTO `nuked_forums_rank` VALUES(7, 'Administrator', 2, 0, 'modules/Forum/images/rank/mod.gif');

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums_read`
--

DROP TABLE IF EXISTS `nuked_forums_read`;
CREATE TABLE IF NOT EXISTS `nuked_forums_read` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) NOT NULL DEFAULT '',
  `thread_id` int(11) NOT NULL DEFAULT '0',
  `forum_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `thread_id` (`thread_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums_threads`
--

DROP TABLE IF EXISTS `nuked_forums_threads`;
CREATE TABLE IF NOT EXISTS `nuked_forums_threads` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `titre` text NOT NULL,
  `date` varchar(10) DEFAULT NULL,
  `closed` int(1) NOT NULL DEFAULT '0',
  `auteur` text NOT NULL,
  `auteur_id` varchar(20) NOT NULL DEFAULT '',
  `forum_id` int(5) NOT NULL DEFAULT '0',
  `last_post` varchar(20) NOT NULL DEFAULT '',
  `view` int(10) NOT NULL DEFAULT '0',
  `annonce` int(1) NOT NULL DEFAULT '0',
  `sondage` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `auteur_id` (`auteur_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_forums_vote`
--

DROP TABLE IF EXISTS `nuked_forums_vote`;
CREATE TABLE IF NOT EXISTS `nuked_forums_vote` (
  `poll_id` int(11) NOT NULL DEFAULT '0',
  `auteur_id` varchar(20) NOT NULL DEFAULT '',
  `auteur_ip` varchar(20) NOT NULL DEFAULT '',
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_gallery`
--

DROP TABLE IF EXISTS `nuked_gallery`;
CREATE TABLE IF NOT EXISTS `nuked_gallery` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL DEFAULT '',
  `url2` varchar(200) NOT NULL DEFAULT '',
  `url_file` varchar(200) NOT NULL DEFAULT '',
  `cat` int(11) NOT NULL DEFAULT '0',
  `date` varchar(12) NOT NULL DEFAULT '',
  `count` int(10) NOT NULL DEFAULT '0',
  `autor` text NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_gallery_cat`
--

DROP TABLE IF EXISTS `nuked_gallery_cat`;
CREATE TABLE IF NOT EXISTS `nuked_gallery_cat` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL DEFAULT '0',
  `titre` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_games`
--

DROP TABLE IF EXISTS `nuked_games`;
CREATE TABLE IF NOT EXISTS `nuked_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `titre` varchar(50) NOT NULL DEFAULT '',
  `icon` varchar(150) NOT NULL DEFAULT '',
  `pref_1` varchar(50) NOT NULL DEFAULT '',
  `pref_2` varchar(50) NOT NULL DEFAULT '',
  `pref_3` varchar(50) NOT NULL DEFAULT '',
  `pref_4` varchar(50) NOT NULL DEFAULT '',
  `pref_5` varchar(50) NOT NULL DEFAULT '',
  `map` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `nuked_games`
--

INSERT INTO `nuked_games` VALUES(1, 'Counter-Strike', 'Préférences CS', 'images/games/cs.gif', 'Autre pseudo', 'Map favorite', 'Arme favorite', 'Skin Terro', 'Skin CT', 'de_dust|de_inferno');

-- --------------------------------------------------------

--
-- Structure de la table `nuked_games_prefs`
--

DROP TABLE IF EXISTS `nuked_games_prefs`;
CREATE TABLE IF NOT EXISTS `nuked_games_prefs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game` int(11) NOT NULL DEFAULT '0',
  `user_id` varchar(20) NOT NULL DEFAULT '',
  `pref_1` text NOT NULL,
  `pref_2` text NOT NULL,
  `pref_3` text NOT NULL,
  `pref_4` text NOT NULL,
  `pref_5` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_guestbook`
--

DROP TABLE IF EXISTS `nuked_guestbook`;
CREATE TABLE IF NOT EXISTS `nuked_guestbook` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `url` varchar(70) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  `host` varchar(60) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_irc_awards`
--

DROP TABLE IF EXISTS `nuked_irc_awards`;
CREATE TABLE IF NOT EXISTS `nuked_irc_awards` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `date` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_liens`
--

DROP TABLE IF EXISTS `nuked_liens`;
CREATE TABLE IF NOT EXISTS `nuked_liens` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` varchar(12) NOT NULL DEFAULT '',
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL DEFAULT '',
  `cat` int(11) NOT NULL DEFAULT '0',
  `webmaster` text NOT NULL,
  `country` varchar(50) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  `broke` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_liens_cat`
--

DROP TABLE IF EXISTS `nuked_liens_cat`;
CREATE TABLE IF NOT EXISTS `nuked_liens_cat` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL DEFAULT '0',
  `titre` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_match`
--

DROP TABLE IF EXISTS `nuked_match`;
CREATE TABLE IF NOT EXISTS `nuked_match` (
  `warid` int(10) NOT NULL AUTO_INCREMENT,
  `etat` int(1) NOT NULL DEFAULT '0',
  `team` int(11) NOT NULL DEFAULT '0',
  `game` int(11) NOT NULL DEFAULT '0',
  `adversaire` text,
  `url_adv` varchar(60) DEFAULT NULL,
  `pays_adv` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(100) DEFAULT NULL,
  `style` varchar(100) NOT NULL DEFAULT '',
  `date_jour` int(2) DEFAULT NULL,
  `date_mois` int(2) DEFAULT NULL,
  `date_an` int(4) DEFAULT NULL,
  `heure` varchar(10) NOT NULL DEFAULT '',
  `map` text,
  `tscore_team` float DEFAULT NULL,
  `tscore_adv` float DEFAULT NULL,
  `score_team` text NOT NULL,
  `score_adv` text NOT NULL,
  `report` text,
  `auteur` varchar(50) DEFAULT NULL,
  `url_league` varchar(100) DEFAULT NULL,
  `dispo` text,
  `pas_dispo` text,
  PRIMARY KEY (`warid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_modules`
--

DROP TABLE IF EXISTS `nuked_modules`;
CREATE TABLE IF NOT EXISTS `nuked_modules` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL DEFAULT '',
  `niveau` int(1) NOT NULL DEFAULT '0',
  `admin` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Contenu de la table `nuked_modules`
--

INSERT INTO `nuked_modules` VALUES(1, 'News', 0, 2);
INSERT INTO `nuked_modules` VALUES(2, 'Forum', 0, 2);
INSERT INTO `nuked_modules` VALUES(3, 'Wars', 0, 2);
INSERT INTO `nuked_modules` VALUES(4, 'Irc', 0, 2);
INSERT INTO `nuked_modules` VALUES(5, 'Survey', 0, 3);
INSERT INTO `nuked_modules` VALUES(6, 'Links', 0, 3);
INSERT INTO `nuked_modules` VALUES(7, 'Sections', 0, 3);
INSERT INTO `nuked_modules` VALUES(8, 'Server', 0, 3);
INSERT INTO `nuked_modules` VALUES(9, 'Download', 0, 3);
INSERT INTO `nuked_modules` VALUES(10, 'Gallery', 0, 3);
INSERT INTO `nuked_modules` VALUES(11, 'Guestbook', 0, 3);
INSERT INTO `nuked_modules` VALUES(12, 'Suggest', 0, 3);
INSERT INTO `nuked_modules` VALUES(13, 'Textbox', 0, 9);
INSERT INTO `nuked_modules` VALUES(14, 'Calendar', 0, 2);
INSERT INTO `nuked_modules` VALUES(15, 'Members', 0, 9);
INSERT INTO `nuked_modules` VALUES(16, 'Team', 0, 9);
INSERT INTO `nuked_modules` VALUES(17, 'Defy', 0, 3);
INSERT INTO `nuked_modules` VALUES(18, 'Recruit', 0, 3);
INSERT INTO `nuked_modules` VALUES(19, 'Comment', 0, 9);
INSERT INTO `nuked_modules` VALUES(20, 'Vote', 0, 9);
INSERT INTO `nuked_modules` VALUES(21, 'Stats', 0, 2);
INSERT INTO `nuked_modules` VALUES(22, 'Contact', 0, 3);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_nbconnecte`
--

DROP TABLE IF EXISTS `nuked_nbconnecte`;
CREATE TABLE IF NOT EXISTS `nuked_nbconnecte` (
  `IP` varchar(30) NOT NULL DEFAULT '',
  `type` int(10) NOT NULL DEFAULT '0',
  `date` int(14) NOT NULL DEFAULT '0',
  `user_id` varchar(20) NOT NULL DEFAULT '',
  `username` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`IP`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_news`
--

DROP TABLE IF EXISTS `nuked_news`;
CREATE TABLE IF NOT EXISTS `nuked_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cat` varchar(30) NOT NULL DEFAULT '',
  `titre` text,
  `auteur` text,
  `auteur_id` varchar(20) NOT NULL DEFAULT '',
  `texte` text,
  `suite` text,
  `date` varchar(30) NOT NULL DEFAULT '',
  `bbcodeoff` int(1) NOT NULL DEFAULT '0',
  `smileyoff` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `nuked_news`
--

INSERT INTO `nuked_news` VALUES(1, '1', 'Bienvenue sur votre site NuKed-KlaN 1.7.9', 'Sekuline', 'C5ijwtyzw527o9m7oX2f', 'Bienvenue sur votre site NuKed-KlaN, votre installation s''est, à priori, bien déroulée, rendez-vous dans la partie administration pour commencer à utiliser votre site tout simplement en vous loguant avec le pseudo indiqué lors de l''install. En cas de problèmes, veuillez le signaler sur  <a href="http://www.nuked-klan.org">http://www.nuked-klan.org</a> dans le forum prévu à cet effet.', '', '1323049529', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_news_cat`
--

DROP TABLE IF EXISTS `nuked_news_cat`;
CREATE TABLE IF NOT EXISTS `nuked_news_cat` (
  `nid` int(11) NOT NULL AUTO_INCREMENT,
  `titre` text,
  `description` text,
  `image` text,
  PRIMARY KEY (`nid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `nuked_news_cat`
--

INSERT INTO `nuked_news_cat` VALUES(1, 'Counter-Strike', 'Le meilleur MOD pour Half-Life', 'modules/News/images/cs.gif');

-- --------------------------------------------------------

--
-- Structure de la table `nuked_notification`
--

DROP TABLE IF EXISTS `nuked_notification`;
CREATE TABLE IF NOT EXISTS `nuked_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(30) NOT NULL DEFAULT '0',
  `type` text NOT NULL,
  `texte` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_packages`
--

DROP TABLE IF EXISTS `nuked_packages`;
CREATE TABLE IF NOT EXISTS `nuked_packages` (
  `file` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`file`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_recrute`
--

DROP TABLE IF EXISTS `nuked_recrute`;
CREATE TABLE IF NOT EXISTS `nuked_recrute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(12) NOT NULL DEFAULT '',
  `pseudo` text NOT NULL,
  `prenom` text NOT NULL,
  `age` int(3) NOT NULL DEFAULT '0',
  `mail` varchar(80) NOT NULL DEFAULT '',
  `icq` varchar(50) NOT NULL DEFAULT '',
  `country` text NOT NULL,
  `game` int(11) NOT NULL DEFAULT '0',
  `connection` text NOT NULL,
  `experience` text NOT NULL,
  `dispo` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `game` (`game`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_sections`
--

DROP TABLE IF EXISTS `nuked_sections`;
CREATE TABLE IF NOT EXISTS `nuked_sections` (
  `artid` int(11) NOT NULL AUTO_INCREMENT,
  `secid` int(11) NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `content` text NOT NULL,
  `autor` text NOT NULL,
  `autor_id` varchar(20) NOT NULL DEFAULT '',
  `counter` int(11) NOT NULL DEFAULT '0',
  `bbcodeoff` int(1) NOT NULL DEFAULT '0',
  `smileyoff` int(1) NOT NULL DEFAULT '0',
  `date` varchar(12) NOT NULL DEFAULT '',
  PRIMARY KEY (`artid`),
  KEY `secid` (`secid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_sections_cat`
--

DROP TABLE IF EXISTS `nuked_sections_cat`;
CREATE TABLE IF NOT EXISTS `nuked_sections_cat` (
  `secid` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL DEFAULT '0',
  `secname` varchar(40) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`secid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_serveur`
--

DROP TABLE IF EXISTS `nuked_serveur`;
CREATE TABLE IF NOT EXISTS `nuked_serveur` (
  `sid` int(30) NOT NULL AUTO_INCREMENT,
  `game` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(30) NOT NULL DEFAULT '',
  `port` varchar(10) NOT NULL DEFAULT '',
  `pass` varchar(10) NOT NULL DEFAULT '',
  `cat` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`sid`),
  KEY `game` (`game`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_serveur_cat`
--

DROP TABLE IF EXISTS `nuked_serveur_cat`;
CREATE TABLE IF NOT EXISTS `nuked_serveur_cat` (
  `cid` int(30) NOT NULL AUTO_INCREMENT,
  `titre` varchar(30) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_sessions`
--

DROP TABLE IF EXISTS `nuked_sessions`;
CREATE TABLE IF NOT EXISTS `nuked_sessions` (
  `id` varchar(50) NOT NULL DEFAULT '0',
  `user_id` varchar(20) NOT NULL DEFAULT '0',
  `date` varchar(30) NOT NULL DEFAULT '',
  `last_used` varchar(30) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `vars` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_shoutbox`
--

DROP TABLE IF EXISTS `nuked_shoutbox`;
CREATE TABLE IF NOT EXISTS `nuked_shoutbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auteur` text,
  `ip` varchar(20) NOT NULL DEFAULT '',
  `texte` text,
  `date` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `nuked_shoutbox`
--

INSERT INTO `nuked_shoutbox` VALUES(1, 'Admin', '127.0.0.1', 'Bienvenue sur votre site NuKed-KlaN 1.7.9', '1323049529');

-- --------------------------------------------------------

--
-- Structure de la table `nuked_smilies`
--

DROP TABLE IF EXISTS `nuked_smilies`;
CREATE TABLE IF NOT EXISTS `nuked_smilies` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Contenu de la table `nuked_smilies`
--

INSERT INTO `nuked_smilies` VALUES(1, ':D', 'biggrin.gif', 'Very Happy');
INSERT INTO `nuked_smilies` VALUES(2, ':)', 'smile.gif', 'Smile');
INSERT INTO `nuked_smilies` VALUES(3, ':(', 'frown.gif', 'Sad');
INSERT INTO `nuked_smilies` VALUES(4, ':o', 'eek.gif', 'Surprised');
INSERT INTO `nuked_smilies` VALUES(5, ':?', 'confused.gif', 'Confused');
INSERT INTO `nuked_smilies` VALUES(6, '8)', 'cool.gif', 'Cool');
INSERT INTO `nuked_smilies` VALUES(7, ':P', 'tongue.gif', 'Razz');
INSERT INTO `nuked_smilies` VALUES(8, ':x', 'mad.gif', 'Mad');
INSERT INTO `nuked_smilies` VALUES(9, ';)', 'wink.gif', 'Wink');
INSERT INTO `nuked_smilies` VALUES(10, ':red:', 'redface.gif', 'Embarassed');
INSERT INTO `nuked_smilies` VALUES(11, ':roll:', 'rolleyes.gif', 'Rolling Eyes');

-- --------------------------------------------------------

--
-- Structure de la table `nuked_sondage`
--

DROP TABLE IF EXISTS `nuked_sondage`;
CREATE TABLE IF NOT EXISTS `nuked_sondage` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) NOT NULL DEFAULT '',
  `date` varchar(15) NOT NULL DEFAULT '0',
  `niveau` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `nuked_sondage`
--

INSERT INTO `nuked_sondage` VALUES(1, 'Aimez-vous Nuked-klan ?', '1323049521', 0);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_sondage_check`
--

DROP TABLE IF EXISTS `nuked_sondage_check`;
CREATE TABLE IF NOT EXISTS `nuked_sondage_check` (
  `ip` varchar(20) NOT NULL DEFAULT '',
  `pseudo` varchar(50) NOT NULL DEFAULT '',
  `heurelimite` int(14) NOT NULL DEFAULT '0',
  `sid` varchar(30) NOT NULL DEFAULT '',
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_sondage_data`
--

DROP TABLE IF EXISTS `nuked_sondage_data`;
CREATE TABLE IF NOT EXISTS `nuked_sondage_data` (
  `sid` int(11) NOT NULL DEFAULT '0',
  `optionText` char(50) NOT NULL DEFAULT '',
  `optionCount` int(11) NOT NULL DEFAULT '0',
  `voteID` int(11) NOT NULL DEFAULT '0',
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `nuked_sondage_data`
--

INSERT INTO `nuked_sondage_data` VALUES(1, 'Ca déchire, continuez !', 0, 1);
INSERT INTO `nuked_sondage_data` VALUES(1, 'Mouais, pas mal...', 0, 2);
INSERT INTO `nuked_sondage_data` VALUES(1, 'C''est naze, arrêtez-vous !', 0, 3);
INSERT INTO `nuked_sondage_data` VALUES(1, 'C''est quoi Nuked-Klan ?', 0, 4);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_stats`
--

DROP TABLE IF EXISTS `nuked_stats`;
CREATE TABLE IF NOT EXISTS `nuked_stats` (
  `nom` varchar(50) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nom`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `nuked_stats`
--

INSERT INTO `nuked_stats` VALUES('Gallery', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Archives', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Calendar', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Defy', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Download', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Guestbook', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Irc', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Links', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Wars', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('News', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Search', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Recruit', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Sections', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Server', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Members', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Team', 'pages', 0);
INSERT INTO `nuked_stats` VALUES('Forum', 'pages', 0);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_stats_visitor`
--

DROP TABLE IF EXISTS `nuked_stats_visitor`;
CREATE TABLE IF NOT EXISTS `nuked_stats_visitor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `host` varchar(100) NOT NULL DEFAULT '',
  `browser` varchar(50) NOT NULL DEFAULT '',
  `os` varchar(50) NOT NULL DEFAULT '',
  `referer` varchar(200) NOT NULL DEFAULT '',
  `day` int(2) NOT NULL DEFAULT '0',
  `month` int(2) NOT NULL DEFAULT '0',
  `year` int(4) NOT NULL DEFAULT '0',
  `hour` int(2) NOT NULL DEFAULT '0',
  `date` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `host` (`host`),
  KEY `browser` (`browser`),
  KEY `os` (`os`),
  KEY `referer` (`referer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_suggest`
--

DROP TABLE IF EXISTS `nuked_suggest`;
CREATE TABLE IF NOT EXISTS `nuked_suggest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` mediumtext NOT NULL,
  `user_id` varchar(20) NOT NULL DEFAULT '',
  `proposition` longtext NOT NULL,
  `date` varchar(14) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_team`
--

DROP TABLE IF EXISTS `nuked_team`;
CREATE TABLE IF NOT EXISTS `nuked_team` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) NOT NULL DEFAULT '',
  `tag` text NOT NULL,
  `tag2` text NOT NULL,
  `ordre` int(5) NOT NULL DEFAULT '0',
  `game` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_team_rank`
--

DROP TABLE IF EXISTS `nuked_team_rank`;
CREATE TABLE IF NOT EXISTS `nuked_team_rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(80) NOT NULL DEFAULT '',
  `ordre` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_tmpses`
--

DROP TABLE IF EXISTS `nuked_tmpses`;
CREATE TABLE IF NOT EXISTS `nuked_tmpses` (
  `session_id` varchar(64) NOT NULL,
  `session_vars` text NOT NULL,
  `session_start` bigint(20) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_userbox`
--

DROP TABLE IF EXISTS `nuked_userbox`;
CREATE TABLE IF NOT EXISTS `nuked_userbox` (
  `mid` int(50) NOT NULL AUTO_INCREMENT,
  `user_from` varchar(30) NOT NULL DEFAULT '',
  `user_for` varchar(30) NOT NULL DEFAULT '',
  `titre` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `date` varchar(30) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mid`),
  KEY `user_from` (`user_from`),
  KEY `user_for` (`user_for`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_users`
--

DROP TABLE IF EXISTS `nuked_users`;
CREATE TABLE IF NOT EXISTS `nuked_users` (
  `id` varchar(20) NOT NULL DEFAULT '',
  `team` varchar(80) NOT NULL DEFAULT '',
  `team2` varchar(80) NOT NULL DEFAULT '',
  `team3` varchar(80) NOT NULL DEFAULT '',
  `rang` int(11) NOT NULL DEFAULT '0',
  `ordre` int(5) NOT NULL DEFAULT '0',
  `pseudo` text NOT NULL,
  `mail` varchar(80) NOT NULL DEFAULT '',
  `email` varchar(80) NOT NULL DEFAULT '',
  `icq` varchar(50) NOT NULL DEFAULT '',
  `msn` varchar(80) NOT NULL DEFAULT '',
  `aim` varchar(50) NOT NULL DEFAULT '',
  `yim` varchar(50) NOT NULL DEFAULT '',
  `url` varchar(150) NOT NULL DEFAULT '',
  `pass` varchar(80) NOT NULL DEFAULT '',
  `niveau` int(1) NOT NULL DEFAULT '0',
  `date` varchar(30) NOT NULL DEFAULT '',
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `signature` text NOT NULL,
  `user_theme` varchar(30) NOT NULL DEFAULT '',
  `user_langue` varchar(30) NOT NULL DEFAULT '',
  `game` int(11) NOT NULL DEFAULT '0',
  `country` varchar(50) NOT NULL DEFAULT '',
  `count` int(10) NOT NULL DEFAULT '0',
  `erreur` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `team` (`team`),
  KEY `team2` (`team2`),
  KEY `team3` (`team3`),
  KEY `rang` (`rang`),
  KEY `game` (`game`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `nuked_users`
--

--
-- Tip : Regenerate new password with "Lost password".
-- Info : Regénérez un nouveau pass avec la fonction mot de passe perdu.
--

INSERT INTO `nuked_users` VALUES('C5ijwtyzw527o9m7oX2f', '', '', '', 0, 0, 'Admin', 'mail@hotmail.com', '', '', '', '', '', '', '#000000000000000000000000000000000', 9, '1323049529', '', '', '', '', 1, 'France.gif', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `nuked_users_detail`
--

DROP TABLE IF EXISTS `nuked_users_detail`;
CREATE TABLE IF NOT EXISTS `nuked_users_detail` (
  `user_id` varchar(20) NOT NULL DEFAULT '0',
  `prenom` text,
  `age` varchar(10) NOT NULL DEFAULT '',
  `sexe` varchar(20) NOT NULL DEFAULT '',
  `ville` text,
  `photo` varchar(150) NOT NULL DEFAULT '',
  `motherboard` text,
  `cpu` varchar(50) DEFAULT NULL,
  `ram` varchar(10) NOT NULL DEFAULT '',
  `video` text,
  `resolution` text,
  `son` text,
  `ecran` text,
  `souris` text,
  `clavier` text,
  `connexion` text,
  `system` text,
  `pref_1` text NOT NULL,
  `pref_2` text NOT NULL,
  `pref_3` text NOT NULL,
  `pref_4` text NOT NULL,
  `pref_5` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `nuked_vote`
--

DROP TABLE IF EXISTS `nuked_vote`;
CREATE TABLE IF NOT EXISTS `nuked_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(30) NOT NULL DEFAULT '0',
  `vid` int(100) DEFAULT NULL,
  `ip` varchar(20) NOT NULL DEFAULT '',
  `vote` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vid` (`vid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------