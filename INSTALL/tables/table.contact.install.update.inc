<?php

// install / update 1.7.9 RC3
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('contact'))) {
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
    $tableData  = $this->getTableData('contact');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('message', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, message FROM `'. $db_prefix .'_contact`';
        $dbsContact = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsContact)) {
            $row['message'] = mysql_real_escape_string(bbcode(stripslashes($row['message']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_contact` SET message = \''. $row['message'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>