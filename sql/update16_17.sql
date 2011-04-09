# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Serveur: localhost
# Généré le : Dimanche 18 Juillet 2004 à 11:07
# Version du serveur: 4.0.15
# Version de PHP: 4.3.3
# 
# Base de données: `nuked`
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
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;


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
  KEY `id` (`id`)
) TYPE=MyISAM;

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

ALTER TABLE `nuked_sondage` ADD `niveau` INT(1) DEFAULT '0' NOT NULL AFTER `date`;
ALTER TABLE `nuked_sondage_check` ADD `pseudo` VARCHAR (50) NOT NULL AFTER `ip`;
ALTER TABLE `nuked_forums` ADD `level_poll` INT(1) DEFAULT '0' NOT NULL AFTER `ordre`;
ALTER TABLE `nuked_forums` ADD `level_vote` INT(1) DEFAULT '0' NOT NULL AFTER `level_poll`;
ALTER TABLE `nuked_forums_threads` ADD `sondage` INT(1) DEFAULT '0' NOT NULL AFTER `annonce`;
ALTER TABLE `nuked_forums_messages` ADD `file` VARCHAR (200) NOT NULL AFTER `forum_id`;
ALTER TABLE `nuked_team` ADD `game` INT(11) NOT NULL AFTER `ordre`;
ALTER TABLE `nuked_comment` ADD `autor_id` VARCHAR (20) NOT NULL AFTER `autor`;
ALTER TABLE `nuked_news` ADD `auteur_id` varchar (20) not null AFTER `auteur`;
ALTER TABLE `nuked_users_detail` ADD `sexe` VARCHAR (20) NOT NULL AFTER `age`;
ALTER TABLE `nuked_liens` ADD `webmaster` TEXT NOT NULL AFTER `cat`;
ALTER TABLE `nuked_liens` ADD `country` VARCHAR (50) NOT NULL AFTER `webmaster`;
ALTER TABLE `nuked_liens` ADD `broke` INT(11) DEFAULT '0' NOT NULL AFTER `count`;
ALTER TABLE `nuked_match` CHANGE `score_team` `score_team` INT( 10 ) NOT NULL;
ALTER TABLE `nuked_match` CHANGE `score_adv` `score_adv` INT( 10 ) NOT NULL;

INSERT INTO `nuked_config` (`name`, `value`) VALUES ('validation', 'auto');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_field_max', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_file', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_file_level', '1');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_file_maxsize', '1000');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('forum_rank_team', 'off');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('level_analys', '0');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('visit_delay', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_sections', '10');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('max_wars', '30');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('birthday', 'all');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('avatar_upload', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('avatar_url', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('user_delete', 'on');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('nk_status', 'open');

INSERT INTO `nuked_config` (`name`, `value`) VALUES ('recrute', '1');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('recrute_charte', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('recrute_mail', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('recrute_inbox', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('defie_charte', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('defie_mail', '');
INSERT INTO `nuked_config` (`name`, `value`) VALUES ('defie_inbox', '');
DROP TABLE IF EXISTS `nuked_defie_pref`;
DROP TABLE IF EXISTS `nuked_recrute_pref`;

UPDATE `nuked_config` SET `value`='1.7' WHERE `name`='version';
UPDATE `nuked_forums` SET `level_poll`='1', `level_vote`='1';

ALTER TABLE `nuked_sections` RENAME TO `nuked_sections_cat`;
ALTER TABLE `nuked_seccont` RENAME TO `nuked_sections`;
ALTER TABLE `nuked_sections` ADD `date` VARCHAR( 12 ) NOT NULL ;
ALTER TABLE `nuked_sections` ADD `autor` TEXT NOT NULL AFTER `content`;
ALTER TABLE `nuked_sections` ADD `autor_id` VARCHAR( 20 ) NOT NULL AFTER `autor`;
ALTER TABLE `nuked_sections_cat` ADD `position` INT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ;
UPDATE `nuked_sections` SET `date`='1081775144';

ALTER TABLE `nuked_downloads_cat` ADD `position` INT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ;
UPDATE nuked_downloads set taille = 1000*taille;

ALTER TABLE `nuked_liens_cat` ADD `position` INT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ;

ALTER TABLE `nuked_gallery_cat` ADD `position` INT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ;

ALTER TABLE `nuked_gallery` ADD `autor` varchar(12) default '' NOT NULL;
ALTER TABLE `nuked_gallery` ADD `date` varchar(12) default '' NOT NULL;
ALTER TABLE `nuked_gallery` ADD `count` varchar(10) default '0' NOT NULL;
ALTER TABLE `nuked_gallery` ADD `autor` TEXT NOT NULL AFTER `count`;
UPDATE `nuked_gallery` SET `date`='1081775144';
UPDATE `nuked_users_detail` SET `age`='';

ALTER TABLE `nuked_banned` ADD `pseudo` VARCHAR (50) NOT NULL AFTER `ip`;
ALTER TABLE `nuked_banned` ADD `email` VARCHAR (80) NOT NULL AFTER `pseudo`;
ALTER TABLE `nuked_banned` ADD `texte` text NOT NULL AFTER `email`;

ALTER TABLE `nuked_team` CHANGE `tag` `tag` TEXT NOT NULL;
ALTER TABLE `nuked_team` ADD `tag2` TEXT NOT NULL AFTER `tag` ;

DELETE FROM `nuked_block` WHERE type='who_on_line';
DELETE FROM `nuked_block` WHERE module='Calendar';
DELETE FROM `nuked_block` WHERE module='User';

INSERT INTO `nuked_block` (`bid`, `active`, `position`, `module`, `titre`, `content`, `type`, `nivo`, `page`) VALUES ('', 1, 4, '', 'partners', '<div style=\"text-align: center;padding: 10px;\" ><a href=\"http://www.nuked-klan.org\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"http://www.nuked-klan.org/ban.gif\" alt=\"\" title=\"Nuked-klaN CMS\" /></a></div><div style=\"text-align: center;padding: 10px;\"><a href=\"http://www.nitroserv.fr\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"http://www.nitroserv.com/images/logo_88x31.jpg\" alt=\"\" title=\"Location de serveurs de jeux\" /></a></div>', 'html', 0, 'Tous');
# --------------------------------------------------------

#
# Optimisation MySQL
#

ALTER TABLE `nuked_banned` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;

ALTER TABLE `nuked_block` DROP INDEX `bid` ,
ADD PRIMARY KEY ( `bid` ) ;

ALTER TABLE `nuked_calendar` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;

ALTER TABLE `nuked_comment` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_comment` ADD INDEX ( `im_id` ) ;
ALTER TABLE `nuked_comment` CHANGE `autor_ip` `autor_ip` VARCHAR( 20 ) DEFAULT NULL ;

ALTER TABLE `nuked_config` ADD PRIMARY KEY ( `name` ) ;

ALTER TABLE `nuked_downloads` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_downloads` ADD INDEX ( `type` ) ;
ALTER TABLE `nuked_downloads_cat` ADD INDEX ( `parentid` ) ;

ALTER TABLE `nuked_fichiers_joins` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_fichiers_joins` ADD INDEX ( `im_id` ) ;

ALTER TABLE `nuked_forums` DROP INDEX `id` ;
ALTER TABLE `nuked_forums` ADD INDEX ( `cat` ) ;

ALTER TABLE `nuked_forums_messages` DROP INDEX `id` ;
ALTER TABLE `nuked_forums_messages` ADD INDEX ( `auteur_id` ) ;
ALTER TABLE `nuked_forums_messages` ADD INDEX ( `thread_id` ) ;
ALTER TABLE `nuked_forums_messages` ADD INDEX ( `forum_id` ) ;

ALTER TABLE `nuked_forums_options` ADD INDEX ( `poll_id` ) ;

ALTER TABLE `nuked_forums_poll` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_forums_poll` ADD INDEX ( `thread_id` ) ;

ALTER TABLE `nuked_forums_rank` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;

ALTER TABLE `nuked_forums_read` ADD INDEX ( `user_id` ) ;
ALTER TABLE `nuked_forums_read` ADD INDEX ( `thread_id` ) ;
ALTER TABLE `nuked_forums_read` ADD INDEX ( `forum_id` ) ;

ALTER TABLE `nuked_forums_threads` DROP INDEX `id` ;
ALTER TABLE `nuked_forums_threads` ADD INDEX ( `auteur_id` ) ;
ALTER TABLE `nuked_forums_threads` ADD INDEX ( `forum_id` ) ;

ALTER TABLE `nuked_gallery` ADD INDEX ( `cat` ) ;
ALTER TABLE `nuked_gallery_cat` ADD INDEX ( `parentid` ) ;

ALTER TABLE `nuked_irc_awards` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;

ALTER TABLE `nuked_liens` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_liens` ADD INDEX ( `cat` ) ;

ALTER TABLE `nuked_liens_cat` ADD INDEX ( `parentid` ) ;

ALTER TABLE `nuked_modules` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;

ALTER TABLE `nuked_nbconnecte` DROP PRIMARY KEY ;
ALTER TABLE `nuked_nbconnecte` ADD PRIMARY KEY ( `IP` , `user_id` ) ;

ALTER TABLE `nuked_news` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_news` ADD INDEX ( `cat` ) ;

ALTER TABLE `nuked_news_cat` DROP INDEX `nid` ,
ADD PRIMARY KEY ( `nid` ) ;

ALTER TABLE `nuked_recrute` ADD INDEX ( `game` ) ;

ALTER TABLE `nuked_sections` ADD INDEX ( `secid` ) ;

ALTER TABLE `nuked_sections_cat` ADD INDEX ( `parentid` ) ;

ALTER TABLE `nuked_serveur` ADD INDEX ( `game` ) ;
ALTER TABLE `nuked_serveur` ADD INDEX ( `cat` ) ;

ALTER TABLE `nuked_serveur_cat` DROP INDEX `id` ,
ADD PRIMARY KEY ( `cid` ) ;

ALTER TABLE `nuked_sessions` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_sessions` ADD INDEX ( `user_id` ) ;

ALTER TABLE `nuked_shoutbox` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;

ALTER TABLE `nuked_stats` CHANGE `nom` `nom` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE `nuked_stats` CHANGE `type` `type` VARCHAR( 50 ) NOT NULL ;
ALTER TABLE `nuked_stats` ADD PRIMARY KEY ( `nom` , `type` ) ;

ALTER TABLE `nuked_stats_visitor` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_stats_visitor` ADD INDEX ( `user_id` ) ;

ALTER TABLE `nuked_suggest` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_suggest` ADD INDEX ( `user_id` ) ;

ALTER TABLE `nuked_userbox` ADD INDEX ( `user_from` ) ;
ALTER TABLE `nuked_userbox` ADD INDEX ( `user_for` ) ;

ALTER TABLE `nuked_users` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_users` ADD INDEX ( `team` ) ;
ALTER TABLE `nuked_users` ADD INDEX ( `team2` ) ;
ALTER TABLE `nuked_users` ADD INDEX ( `team3` ) ;
ALTER TABLE `nuked_users` ADD INDEX ( `rang` ) ;
ALTER TABLE `nuked_users` ADD INDEX ( `game` ) ;

ALTER TABLE `nuked_users_detail` ADD PRIMARY KEY ( `user_id` ) ;

ALTER TABLE `nuked_vote` DROP INDEX `id` ,
ADD PRIMARY KEY ( `id` ) ;
ALTER TABLE `nuked_vote` ADD INDEX ( `vid` ) ;

