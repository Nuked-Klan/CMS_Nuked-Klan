<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_serveur_cat`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_serveur_cat` (
            `cid` int(30) NOT NULL auto_increment,
            `titre` varchar(30) NOT NULL default \'\',
            `description` text NOT NULL,
            PRIMARY KEY  (`cid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT cid, description FROM `'. $db_prefix .'_serveur_cat`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['description'] = mysql_real_escape_string(bbcode(stripslashes($row['description']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_serveur_cat` SET description = \''. $row['description'] .'\' WHERE cid = \''. $row['cid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>