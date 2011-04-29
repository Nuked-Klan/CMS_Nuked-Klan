/* RC1 -> RC2 */

INSERT INTO nuked_config (name, value) VALUES ('screen', 'on');

/* RC2 -> RC3 */

INSERT INTO nuked_config (name, value) VALUES ('contact_mail', '');
INSERT INTO nuked_config (name, value) VALUES ('contact_flood', '60');
ALTER TABLE nuked_users ADD  erreur INT(10) NOT NULL default '0';
CREATE TABLE nuked_contact (
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

/* RC3.3 -> RC4 */
ALTER TABLE  `nuked_games` ADD  `map` TEXT NOT NULL;

/* RC3.4 -> RC5 */
CREATE TABLE IF NOT EXISTS `nuked_packages` (
		  `file` varchar(100) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `author` varchar(255) NOT NULL,
		  `link` varchar(255) NOT NULL,
		  `active` tinyint(1) NOT NULL,
		  PRIMARY KEY (`file`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `nuked_tmpses` (
		  `session_id` varchar(64) NOT NULL,
		  `session_vars` text NOT NULL,
		  `session_start` bigint(20) NOT NULL,
		  PRIMARY KEY (`session_id`)
		  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

UPDATE nuked_config SET value = 'quakenet.org' WHERE name = 'irc_serv'