<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_defie`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_defie` (
            `id` int(11) NOT NULL auto_increment,
            `send` varchar(12) NOT NULL default \'\',
            `pseudo` text NOT NULL,
            `clan` text NOT NULL,
            `mail` varchar(80) NOT NULL default \'\',
            `icq` varchar(50) NOT NULL default \'\',
            `irc` varchar(50) NOT NULL default \'\',
            `url` varchar(200) NOT NULL default \'\',
            `pays` text NOT NULL,
            `date` varchar(20) NOT NULL default \'\',
            `heure` varchar(10) NOT NULL default \'\',
            `serveur` text NOT NULL,
            `game` int(11) NOT NULL default \'0\',
            `type` text NOT NULL,
            `map` text NOT NULL,
            `comment` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT id, comment FROM `'. $db_prefix .'_defie`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['comment'] = mysql_real_escape_string(bbcode(stripslashes($row['comment']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_defie` SET comment = \''. $row['comment'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>