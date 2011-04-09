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

/* Rc3.3 -> rc3.4 */
ALTER TABLE  `nuked_games` ADD  `map` TEXT NOT NULL;