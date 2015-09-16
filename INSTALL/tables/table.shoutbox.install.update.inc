<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_shoutbox`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_shoutbox` (
            `id` int(11) NOT NULL auto_increment,
            `auteur` text,
            `ip` varchar(20) NOT NULL default \'\',
            `texte` text,
            `date` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('shoutbox');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('texte', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, texte FROM `'. $db_prefix .'_shoutbox`';
        $dbsShoutbox = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsShoutbox)) {
            $row['texte'] = mysql_real_escape_string(bbcode(stripslashes($row['texte']), $db_prefix, $this->_i18n));

            $sql = 'UPDATE `'. $db_prefix .'_shoutbox` SET texte = \''. $row['texte'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>