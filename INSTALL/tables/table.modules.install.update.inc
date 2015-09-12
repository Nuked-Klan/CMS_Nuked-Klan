<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_modules`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_modules` (
            `id` int(2) NOT NULL auto_increment,
            `nom` varchar(50) NOT NULL default \'\',
            `niveau` int(1) NOT NULL default \'0\',
            `admin` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $db_prefix .'_modules` VALUES
        (1, \'News\', 0, 2),
        (2, \'Forum\', 0, 2),
        (3, \'Wars\', 0, 2),
        (4, \'Irc\', 0, 2),
        (5, \'Survey\', 0, 3),
        (6, \'Links\', 0, 3),
        (7, \'Sections\', 0, 3),
        (8, \'Server\', 0, 3),
        (9, \'Download\', 0, 3),
        (10, \'Gallery\', 0, 3),
        (11, \'Guestbook\', 0, 3),
        (12, \'Suggest\', 0, 3),
        (13, \'Textbox\', 0, 9),
        (14, \'Calendar\', 0, 2),
        (15, \'Members\', 0, 9),
        (16, \'Team\', 0, 9),
        (17, \'Defy\', 0, 3),
        (18, \'Recruit\', 0, 3),
        (19, \'Comment\', 0, 9),
        (20, \'Vote\', 0, 9),
        (21, \'Stats\', 0, 2),
        (22, \'Contact\', 0, 3);';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    $sql = 'SELECT value FROM `'. $db_prefix .'_config` WHERE name = \'version\'';
    $req = mysql_query($sql)or die (mysql_error());
    list($currentVersion) = mysql_fetch_array($req);

    if (version_compare($currentVersion, install::NK_VERSION) === -1) {
        if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
            $sql = 'INSERT INTO `'. $db_prefix .'_modules` (`nom`, `niveau`, `admin`) VALUES (\'Stats\', 0, 2)';
            mysql_query($sql)or die(mysql_error());

            $sql = 'INSERT INTO `'. $db_prefix .'_modules` (`nom`, `niveau`, `admin`) VALUES (\'Contact\', 0, 3)';
            mysql_query($sql)or die(mysql_error());

            $update = true;
        }
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>