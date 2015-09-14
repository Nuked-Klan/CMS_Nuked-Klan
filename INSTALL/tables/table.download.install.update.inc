<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_downloads`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_downloads` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(12) NOT NULL default \'\',
            `taille` varchar(6) NOT NULL default \'0\',
            `titre` text NOT NULL,
            `description` text NOT NULL,
            `type` int(11) NOT NULL default \'0\',
            `count` int(10) NOT NULL default \'0\',
            `url` varchar(200) NOT NULL default \'\',
            `url2` varchar(200) NOT NULL default \'\',
            `broke` int(11) NOT NULL default \'0\',
            `url3` varchar(200) NOT NULL default \'\',
            `level` int(1) NOT NULL default \'0\',
            `hit` int(11) NOT NULL default \'0\',
            `edit` varchar(12) NOT NULL default \'\',
            `screen` varchar(200) NOT NULL default \'\',
            `autor` text NOT NULL,
            `url_autor` varchar(200) NOT NULL default \'\',
            `comp` text NOT NULL,
            PRIMARY KEY  (`id`),
            KEY `type` (`type`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('downloads');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('description', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, description FROM `'. $db_prefix .'_downloads`';
        $dbsDownloads = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsDownloads)) {
            $row['description'] = mysql_real_escape_string(bbcode(stripslashes($row['description']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_downloads` SET description = \''. $row['description'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>