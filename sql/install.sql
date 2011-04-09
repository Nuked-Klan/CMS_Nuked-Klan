# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Serveur: localhost
# Généré le : Vendredi 12 Novembre 2004 à 02:10
# Version du serveur: 4.0.15
# Version de PHP: 4.3.3
#
# Base de données: `nk17`
#

# --------------------------------------------------------

#
# Structure de la table `nuked_banned`
#

DROP TABLE IF EXISTS `nuked_banned`;
CREATE TABLE `nuked_banned` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(50) NOT NULL default '',
  `pseudo` varchar(50) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `texte` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_banned`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_block`
#

DROP TABLE IF EXISTS `nuked_block`;
CREATE TABLE `nuked_block` (
  `bid` int(10) NOT NULL auto_increment,
  `active` int(1) NOT NULL default '0',
  `position` int(2) NOT NULL default '0',
  `module` varchar(100) NOT NULL default '',
  `titre` text NOT NULL,
  `content` text NOT NULL,
  `type` varchar(30) NOT NULL default '0',
  `nivo` int(1) NOT NULL default '0',
  `page` text NOT NULL,
  PRIMARY KEY  (`bid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_block`
#

INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (1, 2, 1, '', 'Login', '', 'login', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (2, 1, 1, '', 'Menu', '[News]|News||0|NEWLINE[Archives]|Archives||0|NEWLINE[Forum]|Forum||0|NEWLINE[Download]|Download||0|NEWLINE[Members]|Membres||0|NEWLINE[Team]|Team||0|NEWLINE[Defy]|Defy Us||0|NEWLINE[Recruit]|Recruitement||0|NEWLINE[Sections]|Articles||0|NEWLINE[Server]|Servers||0|NEWLINE[Links]|Links||0|NEWLINE[Calendar]|Calendar||0|NEWLINE[Gallery]|Gallery||0|NEWLINE[Wars]|Matches||0|NEWLINE[Irc]|IrC||0|NEWLINE[Guestbook]|Guestbook||0|NEWLINE[Search]|Search||0|NEWLINE|<b>Member</b>||1|NEWLINE[User]|Account||1|NEWLINE|<b>Admin</b>||2|NEWLINE[Admin]|Administration||2|', 'menu', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (3, 1, 2, 'Search', 'Recherche', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (4, 2, 2, '', 'Sondage', '', 'survey', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (5, 2, 3, 'Wars', 'Matches', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (6, 1, 3, 'Stats', 'Stats', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (7, 0, 0, 'Irc', 'Irc Awards', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (8, 0, 0, 'Server', 'Serveur monitor', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (9, 0, 0, '', 'Suggestion', '', 'suggest', 1, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (10, 0, 0, 'Textbox', 'Tribune libre', '', 'module', 0, 'Tous');
INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES (11, 1, 4, '', 'partners', '<div style=\"text-align: center;padding: 10px;\" ><a href=\"http://www.nuked-klan.org\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"http://www.nuked-klan.org/ban.gif\" alt=\"\" title=\"Nuked-klaN CMS\" /></a></div><div style=\"text-align: center;padding: 10px;\"><a href=\"http://www.nitroserv.fr\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"http://www.nitroserv.com/images/logo_88x31.jpg\" alt=\"\" title=\"Location de serveurs de jeux\" /></a></div>', 'html', 0, 'Tous');

# --------------------------------------------------------

#
# Structure de la table `nuked_calendar`
#

DROP TABLE IF EXISTS `nuked_calendar`;
CREATE TABLE `nuked_calendar` (
  `id` int(11) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `date_jour` int(2) default NULL,
  `date_mois` int(2) default NULL,
  `date_an` int(4) default NULL,
  `heure` varchar(5) NOT NULL default '',
  `auteur` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_calendar`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_comment`
#

DROP TABLE IF EXISTS `nuked_comment`;
CREATE TABLE `nuked_comment` (
  `id` int(10) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL default '0',
  `im_id` int(100) default NULL,
  `autor` text,
  `autor_id` varchar(20) NOT NULL default '',
  `titre` text NOT NULL,
  `comment` text,
  `date` varchar(12) default NULL,
  `autor_ip` varchar(20) default NULL,
  PRIMARY KEY  (`id`),
  KEY `im_id` (`im_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_comment`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_config`
#

DROP TABLE IF EXISTS `nuked_config`;
CREATE TABLE `nuked_config` (
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_config`
#

INSERT INTO `nuked_config` (`name`, `value`) VALUES ('version', '1.7');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('date_install', '1100221773');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('langue', 'french');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('name', 'Nuked-klaN 1.7');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('slogan', 'PHP 4 Gamers');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('tag_pre', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('tag_suf', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('url', 'http://www.nuked-klan.org');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('mail', 'mail@hotmail.com');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('footmessage', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('nk_status', 'open');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('index_site', 'News');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('theme', 'deus_17');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('keyword', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('description', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('inscription', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('inscription_mail', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('inscription_avert', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('inscription_charte', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('validation', 'auto');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('user_delete', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('suggest_avert', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('irc_chan', 'nuked-klan');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('irc_serv', 'quakenet.eu.org');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('server_ip', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('server_port', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('server_pass', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('server_game', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_title', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_desc', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_rank_team', 'off');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_field_max', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_file', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_file_level', '1');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_file_maxsize', '1000');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('thread_forum_page', '20');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('mess_forum_page', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('hot_topic', '20');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('post_flood', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('gallery_title', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_img_line', '2');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_img', '6');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_news', '5');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_download', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_liens', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_sections', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_wars', '30');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_archives', '30');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_members', '30');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_shout', '20');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('mess_guest_page', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('sond_delay', '24');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('level_analys', '0');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('visit_delay', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('recrute', '1');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('recrute_charte', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('recrute_mail', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('recrute_inbox', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('defie_charte', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('defie_mail', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('defie_inbox', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('birthday', 'all');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('avatar_upload', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('avatar_url', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('cookiename', 'nuked');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('sess_inactivemins', '5');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('sess_days_limit', '999');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('nbc_timeout', '300');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('hide_download', 'on');

# --------------------------------------------------------

#
# Structure de la table `nuked_contact`
#

DROP TABLE IF EXISTS `nuked_contact`;
CREATE TABLE `nuked_contact` (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(200) NOT NULL default '',
  `message` text NOT NULL,
  `email` varchar(80) NOT NULL default '',
  `nom` varchar(200) NOT NULL default '',
  `ip` varchar(50) NOT NULL default '',
  `date` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `titre` (`titre`)
) TYPE=MyISAM;

INSERT INTO nuked_config (name, value) VALUES ('contact_mail', '');
INSERT INTO nuked_config (name, value) VALUES ('contact_flood', '60');

# --------------------------------------------------------

#
# Structure de la table `nuked_defie`
#

DROP TABLE IF EXISTS `nuked_defie`;
CREATE TABLE `nuked_defie` (
  `id` int(11) NOT NULL auto_increment,
  `send` varchar(12) NOT NULL default '',
  `pseudo` text NOT NULL,
  `clan` text NOT NULL,
  `mail` varchar(80) NOT NULL default '',
  `icq` varchar(50) NOT NULL default '',
  `irc` varchar(50) NOT NULL default '',
  `url` varchar(200) NOT NULL default '',
  `pays` text NOT NULL,
  `date` varchar(20) NOT NULL default '',
  `heure` varchar(10) NOT NULL default '',
  `serveur` text NOT NULL,
  `game` int(11) NOT NULL default '0',
  `type` text NOT NULL,
  `map` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_defie`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_downloads`
#

DROP TABLE IF EXISTS `nuked_downloads`;
CREATE TABLE `nuked_downloads` (
  `id` int(11) NOT NULL auto_increment,
  `date` varchar(12) NOT NULL default '',
  `taille` varchar(6) NOT NULL default '0',
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `type` int(11) NOT NULL default '0',
  `count` int(10) NOT NULL default '0',
  `url` varchar(200) NOT NULL default '',
  `url2` varchar(200) NOT NULL default '',
  `broke` int(11) NOT NULL default '0',
  `url3` varchar(200) NOT NULL default '',
  `level` int(1) NOT NULL default '0',
  `hit` int(11) NOT NULL default '0',
  `edit` varchar(12) NOT NULL default '',
  `screen` varchar(200) NOT NULL default '',
  `autor` text NOT NULL,
  `url_autor` varchar(200) NOT NULL default '',
  `comp` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `type` (`type`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_downloads`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_downloads_cat`
#

DROP TABLE IF EXISTS `nuked_downloads_cat`;
CREATE TABLE `nuked_downloads_cat` (
  `cid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `titre` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `level` int(1) NOT NULL default '0',
  `position` int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `parentid` (`parentid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_downloads_cat`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_fichiers_joins`
#

DROP TABLE IF EXISTS `nuked_fichiers_joins`;
CREATE TABLE `nuked_fichiers_joins` (
  `id` int(10) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL default '',
  `im_id` int(10) NOT NULL default '0',
  `type` varchar(30) NOT NULL default '',
  `url` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `im_id` (`im_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_fichiers_joins`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_forums`
#

DROP TABLE IF EXISTS `nuked_forums`;
CREATE TABLE `nuked_forums` (
  `id` int(5) NOT NULL auto_increment,
  `cat` int(11) NOT NULL default '0',
  `nom` text NOT NULL,
  `comment` text NOT NULL,
  `moderateurs` text NOT NULL,
  `niveau` int(1) NOT NULL default '0',
  `level` int(1) NOT NULL default '0',
  `ordre` int(5) NOT NULL default '0',
  `level_poll` int(1) NOT NULL default '0',
  `level_vote` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat` (`cat`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums`
#

INSERT INTO `nuked_forums` (`id`, `cat`, `nom`, `comment`, `moderateurs`, `niveau`, `level`, `ordre`, `level_poll`, `level_vote`) VALUES (1, 1, 'Forum', 'Test Forum', '', 0, 0, 0, 1, 1);

# --------------------------------------------------------

#
# Structure de la table `nuked_forums_cat`
#

DROP TABLE IF EXISTS `nuked_forums_cat`;
CREATE TABLE `nuked_forums_cat` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(100) default NULL,
  `ordre` int(5) NOT NULL default '0',
  `niveau` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums_cat`
#

INSERT INTO `nuked_forums_cat` (`id`, `nom`, `ordre`, `niveau`) VALUES (1, 'Catégorie 1', 0, 0);

# --------------------------------------------------------

#
# Structure de la table `nuked_forums_messages`
#

DROP TABLE IF EXISTS `nuked_forums_messages`;
CREATE TABLE `nuked_forums_messages` (
  `id` int(5) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `txt` text NOT NULL,
  `date` varchar(12) NOT NULL default '',
  `edition` text NOT NULL,
  `auteur` text NOT NULL,
  `auteur_id` varchar(20) NOT NULL default '',
  `auteur_ip` varchar(20) NOT NULL default '',
  `bbcodeoff` int(1) NOT NULL default '0',
  `smileyoff` int(1) NOT NULL default '0',
  `cssoff` int(1) NOT NULL default '0',
  `usersig` int(1) NOT NULL default '0',
  `emailnotify` int(1) NOT NULL default '0',
  `thread_id` int(5) NOT NULL default '0',
  `forum_id` mediumint(10) NOT NULL default '0',
  `file` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `auteur_id` (`auteur_id`),
  KEY `thread_id` (`thread_id`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums_messages`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_forums_options`
#

DROP TABLE IF EXISTS `nuked_forums_options`;
CREATE TABLE `nuked_forums_options` (
  `id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  `option_text` varchar(255) NOT NULL default '',
  `option_vote` int(11) NOT NULL default '0',
  KEY `poll_id` (`poll_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums_options`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_forums_poll`
#

DROP TABLE IF EXISTS `nuked_forums_poll`;
CREATE TABLE `nuked_forums_poll` (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) NOT NULL default '0',
  `titre` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `thread_id` (`thread_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums_poll`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_forums_rank`
#

DROP TABLE IF EXISTS `nuked_forums_rank`;
CREATE TABLE `nuked_forums_rank` (
  `id` int(10) NOT NULL auto_increment,
  `nom` varchar(100) NOT NULL default '',
  `type` int(1) NOT NULL default '0',
  `post` int(4) NOT NULL default '0',
  `image` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums_rank`
#

INSERT INTO `nuked_forums_rank` (`id`, `nom`, `type`, `post`, `image`) VALUES (1, 'Newbie', 0, 0, 'modules/Forum/images/rank/star1.gif');
INSERT INTO `nuked_forums_rank` (`id`, `nom`, `type`, `post`, `image`) VALUES (2, 'Junior Member', 0, 10, 'modules/Forum/images/rank/star2.gif');
INSERT INTO `nuked_forums_rank` (`id`, `nom`, `type`, `post`, `image`) VALUES (3, 'Member', 0, 100, 'modules/Forum/images/rank/star3.gif');
INSERT INTO `nuked_forums_rank` (`id`, `nom`, `type`, `post`, `image`) VALUES (4, 'Senior Member', 0, 500, 'modules/Forum/images/rank/star4.gif');
INSERT INTO `nuked_forums_rank` (`id`, `nom`, `type`, `post`, `image`) VALUES (5, 'Posting Freak', 0, 1000, 'modules/Forum/images/rank/star5.gif');
INSERT INTO `nuked_forums_rank` (`id`, `nom`, `type`, `post`, `image`) VALUES (6, 'Modérateur', 1, 0, 'modules/Forum/images/rank/mod.gif');
INSERT INTO `nuked_forums_rank` (`id`, `nom`, `type`, `post`, `image`) VALUES (7, 'Administrateur', 2, 0, 'modules/Forum/images/rank/mod.gif');

# --------------------------------------------------------

#
# Structure de la table `nuked_forums_read`
#

DROP TABLE IF EXISTS `nuked_forums_read`;
CREATE TABLE `nuked_forums_read` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` varchar(20) NOT NULL default '',
  `thread_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `thread_id` (`thread_id`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums_read`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_forums_threads`
#

DROP TABLE IF EXISTS `nuked_forums_threads`;
CREATE TABLE `nuked_forums_threads` (
  `id` int(5) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `date` varchar(10) default NULL,
  `closed` int(1) NOT NULL default '0',
  `auteur` text NOT NULL,
  `auteur_id` varchar(20) NOT NULL default '',
  `forum_id` int(5) NOT NULL default '0',
  `last_post` varchar(20) NOT NULL default '',
  `view` int(10) NOT NULL default '0',
  `annonce` int(1) NOT NULL default '0',
  `sondage` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `auteur_id` (`auteur_id`),
  KEY `forum_id` (`forum_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums_threads`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_forums_vote`
#

DROP TABLE IF EXISTS `nuked_forums_vote`;
CREATE TABLE `nuked_forums_vote` (
  `poll_id` int(11) NOT NULL default '0',
  `auteur_id` varchar(20) NOT NULL default '',
  `auteur_ip` varchar(20) NOT NULL default '',
  KEY `poll_id` (`poll_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_forums_vote`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_gallery`
#

DROP TABLE IF EXISTS `nuked_gallery`;
CREATE TABLE `nuked_gallery` (
  `sid` int(11) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL default '',
  `url2` varchar(200) NOT NULL default '',
  `url_file` varchar(200) NOT NULL default '',
  `cat` int(11) NOT NULL default '0',
  `date` varchar(12) NOT NULL default '',
  `count` int(10) NOT NULL default '0',
  `autor` text NOT NULL,
  PRIMARY KEY  (`sid`),
  KEY `cat` (`cat`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_gallery`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_gallery_cat`
#

DROP TABLE IF EXISTS `nuked_gallery_cat`;
CREATE TABLE `nuked_gallery_cat` (
  `cid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `titre` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `parentid` (`parentid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_gallery_cat`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_games`
#

DROP TABLE IF EXISTS `nuked_games`;
CREATE TABLE `nuked_games` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `titre` varchar(50) NOT NULL default '',
  `icon` varchar(150) NOT NULL default '',
  `pref_1` varchar(50) NOT NULL default '',
  `pref_2` varchar(50) NOT NULL default '',
  `pref_3` varchar(50) NOT NULL default '',
  `pref_4` varchar(50) NOT NULL default '',
  `pref_5` varchar(50) NOT NULL default '',
  `map` TEXT NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_games`
#

INSERT INTO `nuked_games` (`id`, `name`, `titre`, `icon`, `pref_1`, `pref_2`, `pref_3`, `pref_4`, `pref_5`) VALUES (1, 'Counter-Strike', 'Préférences CS', 'images/games/cs.gif', 'Autre pseudo', 'Map favorite', 'Arme favorite', 'Skin Terro', 'Skin CT');

# --------------------------------------------------------

#
# Structure de la table `nuked_games_prefs`
#

DROP TABLE IF EXISTS `nuked_games_prefs`;
CREATE TABLE `nuked_games_prefs` (
  `id` int(11) NOT NULL auto_increment,
  `game` int(11) NOT NULL default '0',
  `user_id` varchar(20) NOT NULL default '',
  `pref_1` text NOT NULL,
  `pref_2` text NOT NULL,
  `pref_3` text NOT NULL,
  `pref_4` text NOT NULL,
  `pref_5` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Structure de la table `nuked_guestbook`
#

DROP TABLE IF EXISTS `nuked_guestbook`;
CREATE TABLE `nuked_guestbook` (
  `id` int(9) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `url` varchar(70) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `host` varchar(60) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_guestbook`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_irc_awards`
#

DROP TABLE IF EXISTS `nuked_irc_awards`;
CREATE TABLE `nuked_irc_awards` (
  `id` int(20) NOT NULL auto_increment,
  `text` text NOT NULL,
  `date` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_irc_awards`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_liens`
#

DROP TABLE IF EXISTS `nuked_liens`;
CREATE TABLE `nuked_liens` (
  `id` int(10) NOT NULL auto_increment,
  `date` varchar(12) NOT NULL default '',
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL default '',
  `cat` int(11) NOT NULL default '0',
  `webmaster` text NOT NULL,
  `country` varchar(50) NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  `broke` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat` (`cat`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_liens`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_liens_cat`
#

DROP TABLE IF EXISTS `nuked_liens_cat`;
CREATE TABLE `nuked_liens_cat` (
  `cid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `titre` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `parentid` (`parentid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_liens_cat`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_match`
#

DROP TABLE IF EXISTS `nuked_match`;
CREATE TABLE `nuked_match` (
  `warid` int(10) NOT NULL auto_increment,
  `etat` int(1) NOT NULL default '0',
  `team` int(11) NOT NULL default '0',
  `game` int(11) NOT NULL default '0',
  `adversaire` text,
  `url_adv` varchar(60) default NULL,
  `pays_adv` varchar(50) NOT NULL default '',
  `type` varchar(100) default NULL,
  `style` varchar(100) NOT NULL default '',
  `date_jour` int(2) default NULL,
  `date_mois` int(2) default NULL,
  `date_an` int(4) default NULL,
  `heure` varchar(10) NOT NULL default '',
  `map_1` varchar(100) default NULL,
  `map_2` varchar(100) default NULL,
  `map_3` varchar(100) default NULL,
  `score_team` int(10) NOT NULL default '0',
  `score_adv` int(10) NOT NULL default '0',
  `report` text,
  `auteur` varchar(50) default NULL,
  `url_league` varchar(100) default NULL,
  `dispo` text,
  `pas_dispo` text,
  PRIMARY KEY  (`warid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_match`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_modules`
#

DROP TABLE IF EXISTS `nuked_modules`;
CREATE TABLE `nuked_modules` (
  `id` int(2) NOT NULL auto_increment,
  `nom` varchar(50) NOT NULL default '',
  `niveau` int(1) NOT NULL default '0',
  `admin` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_modules`
#

INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (1, 'News', 0, 2);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (2, 'Forum', 0, 2);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (3, 'Wars', 0, 2);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (4, 'Irc', 0, 2);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (5, 'Survey', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (6, 'Links', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (7, 'Sections', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (8, 'Server', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (9, 'Download', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (10, 'Gallery', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (11, 'Guestbook', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (12, 'Suggest', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (13, 'Textbox', 0, 9);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (14, 'Calendar', 0, 2);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (15, 'Members', 0, 9);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (16, 'Team', 0, 9);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (17, 'Defy', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (18, 'Recruit', 0, 3);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (19, 'Comment', 0, 9);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (20, 'Vote', 0, 9);
INSERT INTO `nuked_modules` (`id`, `nom`, `niveau`, `admin`) VALUES (21, 'Contact', 0, 3);

# --------------------------------------------------------

#
# Structure de la table `nuked_nbconnecte`
#

DROP TABLE IF EXISTS `nuked_nbconnecte`;
CREATE TABLE `nuked_nbconnecte` (
  `IP` varchar(15) NOT NULL default '',
  `type` int(10) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `user_id` varchar(20) NOT NULL default '',
  `username` varchar(40) NOT NULL default '',
  PRIMARY KEY  ( `IP` , `user_id` )
) TYPE=MyISAM;

#
# Contenu de la table `nuked_nbconnecte`
#

INSERT INTO `nuked_nbconnecte` (`IP`, `type`, `date`, `user_id`, `username`) VALUES ('169.254.139.30', 0, 1100222085, '', '');

# --------------------------------------------------------

#
# Structure de la table `nuked_news`
#

DROP TABLE IF EXISTS `nuked_news`;
CREATE TABLE `nuked_news` (
  `id` int(11) NOT NULL auto_increment,
  `cat` varchar(30) NOT NULL default '',
  `titre` text,
  `auteur` text,
  `auteur_id` varchar(20) NOT NULL default '',
  `texte` text,
  `suite` text,
  `date` varchar(30) NOT NULL default '',
  `bbcodeoff` int(1) NOT NULL default '0',
  `smileyoff` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat` (`cat`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_news`
#

INSERT INTO `nuked_news` (`id`, `cat`, `titre`, `auteur`, `texte`, `suite`, `date`, `bbcodeoff`, `smileyoff`) VALUES (1, '1', 'Bienvenue sur votre site NuKed-KlaN 1.7', 'golgot13', 'Bienvenue sur votre site NuKed-KlaN, votre installation s\'est, à priori, bien déroulée, rendez vous dans la partie administration pour commencer à utiliser votre site tout simplement en vous loguant avec le pseudo indiqué lors de l\'install. En cas de problèmes, veuillez le signaler sur  [url]http://www.nuked-klan.org[/url] dans le forum prévu a cet effet.', '', '1100221782', 0, 0);

# --------------------------------------------------------

#
# Structure de la table `nuked_news_cat`
#

DROP TABLE IF EXISTS `nuked_news_cat`;
CREATE TABLE `nuked_news_cat` (
  `nid` int(11) NOT NULL auto_increment,
  `titre` text,
  `description` text,
  `image` text,
  PRIMARY KEY  (`nid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_news_cat`
#

INSERT INTO `nuked_news_cat` (`nid`, `titre`, `description`, `image`) VALUES (1, 'Counter-Strike', 'Le meilleur MOD pour Halfe-Life', 'modules/News/images/cs.gif');

# --------------------------------------------------------

#
# Structure de la table `nuked_recrute`
#

DROP TABLE IF EXISTS `nuked_recrute`;
CREATE TABLE `nuked_recrute` (
  `id` int(11) NOT NULL auto_increment,
  `date` varchar(12) NOT NULL default '',
  `pseudo` text NOT NULL,
  `prenom` text NOT NULL,
  `age` int(3) NOT NULL default '0',
  `mail` varchar(80) NOT NULL default '',
  `icq` varchar(50) NOT NULL default '',
  `country` text NOT NULL,
  `game` int(11) NOT NULL default '0',
  `connection` text NOT NULL,
  `experience` text NOT NULL,
  `dispo` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `game` (`game`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_recrute`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_sections`
#

DROP TABLE IF EXISTS `nuked_sections`;
CREATE TABLE `nuked_sections` (
  `artid` int(11) NOT NULL auto_increment,
  `secid` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `content` text NOT NULL,
  `autor` text NOT NULL,
  `autor_id` varchar(20) NOT NULL default '',
  `counter` int(11) NOT NULL default '0',
  `bbcodeoff` int(1) NOT NULL default '0',
  `smileyoff` int(1) NOT NULL default '0',
  `date` varchar(12) NOT NULL default '',
  PRIMARY KEY  (`artid`),
  KEY `secid` (`secid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_sections`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_sections_cat`
#

DROP TABLE IF EXISTS `nuked_sections_cat`;
CREATE TABLE `nuked_sections_cat` (
  `secid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `secname` varchar(40) NOT NULL default '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`secid`),
  KEY `parentid` (`parentid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_sections_cat`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_serveur`
#

DROP TABLE IF EXISTS `nuked_serveur`;
CREATE TABLE `nuked_serveur` (
  `sid` int(30) NOT NULL auto_increment,
  `game` varchar(30) NOT NULL default '',
  `ip` varchar(30) NOT NULL default '',
  `port` varchar(10) NOT NULL default '',
  `pass` varchar(10) NOT NULL default '',
  `cat` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`sid`),
  KEY `game` (`game`),
  KEY `cat` (`cat`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_serveur`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_serveur_cat`
#

DROP TABLE IF EXISTS `nuked_serveur_cat`;
CREATE TABLE `nuked_serveur_cat` (
  `cid` int(30) NOT NULL auto_increment,
  `titre` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_serveur_cat`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_sessions`
#

DROP TABLE IF EXISTS `nuked_sessions`;
CREATE TABLE `nuked_sessions` (
  `id` varchar(50) NOT NULL default '0',
  `user_id` varchar(20) NOT NULL default '0',
  `date` varchar(30) NOT NULL default '',
  `last_used` varchar(30) NOT NULL default '',
  `ip` varchar(50) NOT NULL default '',
  `vars` blob NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_sessions`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_shoutbox`
#

DROP TABLE IF EXISTS `nuked_shoutbox`;
CREATE TABLE `nuked_shoutbox` (
  `id` int(11) NOT NULL auto_increment,
  `auteur` text,
  `ip` varchar(20) NOT NULL default '',
  `texte` text,
  `date` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_shoutbox`
#

INSERT INTO `nuked_shoutbox` (`id`, `auteur`, `ip`, `texte`, `date`) VALUES (1, 'golgot13', '169.254.139.30', 'Bienvenue sur votre site NuKed-KlaN 1.7', '1100221782');

# --------------------------------------------------------

#
# Structure de la table `nuked_smilies`
#

DROP TABLE IF EXISTS `nuked_smilies`;
CREATE TABLE `nuked_smilies` (
  `id` int(5) NOT NULL auto_increment,
  `code` varchar(50) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_smilies`
#

INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (1, ':D', 'biggrin.gif', 'Very Happy');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (2, ':)', 'smile.gif', 'Smile');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (3, ':(', 'frown.gif', 'Sad');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (4, ':o', 'eek.gif', 'Surprised');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (5, ':?', 'confused.gif', 'Confused');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (6, '8)', 'cool.gif', 'Cool');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (7, ':P', 'tongue.gif', 'Razz');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (8, ':x', 'mad.gif', 'Mad');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (9, ';)', 'wink.gif', 'Wink');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (10, ':red:', 'redface.gif', 'Embarassed');
INSERT INTO `nuked_smilies` (`id`, `code`, `url`, `name`) VALUES (11, ':roll:', 'rolleyes.gif', 'Rolling Eyes');

# --------------------------------------------------------

#
# Structure de la table `nuked_sondage`
#

DROP TABLE IF EXISTS `nuked_sondage`;
CREATE TABLE `nuked_sondage` (
  `sid` int(11) NOT NULL auto_increment,
  `titre` varchar(100) NOT NULL default '',
  `date` varchar(15) NOT NULL default '0',
  `niveau` int(1) NOT NULL default '0',
  PRIMARY KEY  (`sid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_sondage`
#

INSERT INTO `nuked_sondage` (`sid`, `titre`, `date`, `niveau`) VALUES (1, 'Vous aimez Nuked-klan ?', '1100221774', 0);

# --------------------------------------------------------

#
# Structure de la table `nuked_sondage_check`
#

DROP TABLE IF EXISTS `nuked_sondage_check`;
CREATE TABLE `nuked_sondage_check` (
  `ip` varchar(20) NOT NULL default '',
  `pseudo` varchar(50) NOT NULL default '',
  `heurelimite` int(14) NOT NULL default '0',
  `sid` varchar(30) NOT NULL default '',
  KEY `sid` (`sid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_sondage_check`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_sondage_data`
#

DROP TABLE IF EXISTS `nuked_sondage_data`;
CREATE TABLE `nuked_sondage_data` (
  `sid` int(11) NOT NULL default '0',
  `optionText` char(50) NOT NULL default '',
  `optionCount` int(11) NOT NULL default '0',
  `voteID` int(11) NOT NULL default '0',
  KEY `sid` (`sid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_sondage_data`
#

INSERT INTO `nuked_sondage_data` (`sid`, `optionText`, `optionCount`, `voteID`) VALUES (1, 'Ca déchire continuez !', 0, 1);
INSERT INTO `nuked_sondage_data` (`sid`, `optionText`, `optionCount`, `voteID`) VALUES (1, 'Mouais pas mal', 0, 2);
INSERT INTO `nuked_sondage_data` (`sid`, `optionText`, `optionCount`, `voteID`) VALUES (1, 'C\'est naze arrêtez-vous !', 0, 3);
INSERT INTO `nuked_sondage_data` (`sid`, `optionText`, `optionCount`, `voteID`) VALUES (1, 'C\'est quoi nuked-klan ?', 0, 4);

# --------------------------------------------------------

#
# Structure de la table `nuked_stats`
#

DROP TABLE IF EXISTS `nuked_stats`;
CREATE TABLE `nuked_stats` (
  `nom` varchar(50) NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`nom`,`type`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_stats`
#

INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Gallery', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Archives', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Calendar', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Defy', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Download', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Guestbook', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Irc', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Links', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Wars', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('News', 'pages', 1);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Search', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Recruit', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Sections', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Server', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Members', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Team', 'pages', 0);
INSERT INTO `nuked_stats` (`nom`, `type`, `count`) VALUES ('Forum', 'pages', 0);

# --------------------------------------------------------

#
# Structure de la table `nuked_stats_visitor`
#

DROP TABLE IF EXISTS `nuked_stats_visitor`;
CREATE TABLE `nuked_stats_visitor` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` varchar(20) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `host` varchar(100) NOT NULL default '',
  `browser` varchar(50) NOT NULL default '',
  `os` varchar(50) NOT NULL default '',
  `referer` varchar(200) NOT NULL default '',
  `day` int(2) NOT NULL default '0',
  `month` int(2) NOT NULL default '0',
  `year` int(4) NOT NULL default '0',
  `hour` int(2) NOT NULL default '0',
  `date` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `host` (`host`),
  KEY `browser` (`browser`),
  KEY `os` (`os`),
  KEY `referer` (`referer`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_stats_visitor`
#

INSERT INTO `nuked_stats_visitor` (`id`, `user_id`, `ip`, `host`, `browser`, `os`, `referer`, `day`, `month`, `year`, `hour`, `date`) VALUES (1, '', '169.254.139.30', 'loki', 'Firefox', 'Windows XP', '', 12, 11, 2004, 2, '1100222385');

# --------------------------------------------------------

#
# Structure de la table `nuked_suggest`
#

DROP TABLE IF EXISTS `nuked_suggest`;
CREATE TABLE `nuked_suggest` (
  `id` int(11) NOT NULL auto_increment,
  `module` mediumtext NOT NULL,
  `user_id` varchar(20) NOT NULL default '',
  `proposition` longtext NOT NULL,
  `date` varchar(14) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_suggest`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_team`
#

DROP TABLE IF EXISTS `nuked_team`;
CREATE TABLE `nuked_team` (
  `cid` int(11) NOT NULL auto_increment,
  `titre` varchar(50) NOT NULL default '',
  `tag` text NOT NULL,
  `tag2` text NOT NULL,
  `ordre` int(5) NOT NULL default '0',
  `game` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_team`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_team_rank`
#

DROP TABLE IF EXISTS `nuked_team_rank`;
CREATE TABLE `nuked_team_rank` (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(80) NOT NULL default '',
  `ordre` int(5) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_team_rank`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_userbox`
#

DROP TABLE IF EXISTS `nuked_userbox`;
CREATE TABLE `nuked_userbox` (
  `mid` int(50) NOT NULL auto_increment,
  `user_from` varchar(30) NOT NULL default '',
  `user_for` varchar(30) NOT NULL default '',
  `titre` varchar(50) NOT NULL default '',
  `message` text NOT NULL,
  `date` varchar(30) NOT NULL default '',
  `status` int(1) NOT NULL default '0',
  PRIMARY KEY  (`mid`),
  KEY `user_from` (`user_from`),
  KEY `user_for` (`user_for`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_userbox`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_users`
#

DROP TABLE IF EXISTS `nuked_users`;
CREATE TABLE `nuked_users` (
  `id` varchar(20) NOT NULL default '',
  `team` varchar(80) NOT NULL default '',
  `team2` varchar(80) NOT NULL default '',
  `team3` varchar(80) NOT NULL default '',
  `rang` int(11) NOT NULL default '0',
  `ordre` int(5) NOT NULL default '0',
  `pseudo` text NOT NULL,
  `mail` varchar(80) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `icq` varchar(50) NOT NULL default '',
  `msn` varchar(80) NOT NULL default '',
  `aim` varchar(50) NOT NULL default '',
  `yim` varchar(50) NOT NULL default '',
  `url` varchar(150) NOT NULL default '',
  `pass` varchar(80) NOT NULL default '',
  `niveau` int(1) NOT NULL default '0',
  `date` varchar(30) NOT NULL default '',
  `avatar` varchar(100) NOT NULL default '',
  `signature` text NOT NULL,
  `user_theme` varchar(30) NOT NULL default '',
  `user_langue` varchar(30) NOT NULL default '',
  `game` int(11) NOT NULL default '0',
  `country` varchar(50) NOT NULL default '',
  `count` int(10) NOT NULL default '0',
  `erreur` INT(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `team` (`team`),
  KEY `team2` (`team2`),
  KEY `team3` (`team3`),
  KEY `rang` (`rang`),
  KEY `game` (`game`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_users`
#

INSERT INTO `nuked_users` (`id`, `team`, `team2`, `team3`, `rang`, `ordre`, `pseudo`, `mail`, `email`, `icq`, `msn`, `aim`, `yim`, `url`, `pass`, `niveau`, `date`, `avatar`, `signature`, `user_theme`, `user_langue`, `game`, `country`, `count`) VALUES ('7GpU3whezC1efsdi3sli', '', '', '', 0, 0, 'God', 'God@nuked-klan.org', '', '', '', '', '', '', 'dc647eb65e6711e155375218212b3964', 9, ' 1100221782', '', '', '', '', 1, 'France.gif', 0);

# --------------------------------------------------------

#
# Structure de la table `nuked_users_detail`
#

DROP TABLE IF EXISTS `nuked_users_detail`;
CREATE TABLE `nuked_users_detail` (
  `user_id` varchar(20) NOT NULL default '0',
  `prenom` text,
  `age` varchar(10) NOT NULL default '',
  `sexe` varchar(20) NOT NULL default '',
  `ville` text,
  `photo` varchar(150) NOT NULL default '',
  `motherboard` text,
  `cpu` varchar(50) default NULL,
  `ram` varchar(10) NOT NULL default '',
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
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_users_detail`
#


# --------------------------------------------------------

#
# Structure de la table `nuked_vote`
#

DROP TABLE IF EXISTS `nuked_vote`;
CREATE TABLE `nuked_vote` (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL default '0',
  `vid` int(100) default NULL,
  `ip` varchar(20) NOT NULL default '',
  `vote` int(2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `vid` (`vid`)
) TYPE=MyISAM;

#
# Contenu de la table `nuked_vote`
#


