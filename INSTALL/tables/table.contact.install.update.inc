<?php

if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('comment_mod'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_contact`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_contact` (
            `id` int(11) NOT NULL auto_increment,
            `titre` varchar(200) NOT NULL default \'\',
            `message` text NOT NULL,
            `email` varchar(80) NOT NULL default \'\',
            `nom` varchar(200) NOT NULL default \'\',
            `ip` varchar(50) NOT NULL default \'\',
            `date` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `titre` (`titre`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

if ($this->data['process'] == 'update') {
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT id, message FROM `'. $db_prefix .'_contact`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['message'] = mysql_real_escape_string(bbcode(stripslashes($row['message']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_contact` SET message = \''. $row['message'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>