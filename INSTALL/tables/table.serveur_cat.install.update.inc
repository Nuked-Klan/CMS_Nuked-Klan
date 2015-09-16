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
    $tableData  = $this->getTableData('serveur_cat');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('description', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT cid, description FROM `'. $db_prefix .'_serveur_cat`';
        $dbsServerCat = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsServerCat)) {
            $row['description'] = mysql_real_escape_string(bbcode(stripslashes($row['description']), $db_prefix, $this->_i18n));

            $sql = 'UPDATE `'. $db_prefix .'_serveur_cat` SET description = \''. $row['description'] .'\' WHERE cid = \''. $row['cid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>