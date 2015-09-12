<?php

if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('discussion'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_discussion`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_discussion` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(30) NOT NULL default \'0\',
            `pseudo`  text NOT NULL,
            `texte`  text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

if ($this->data['process'] == 'update') {
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT id, texte FROM `'. $db_prefix .'_discussion`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['texte'] = mysql_real_escape_string(bbcode(stripslashes($row['texte']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_discussion` SET texte = \''. $row['texte'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>