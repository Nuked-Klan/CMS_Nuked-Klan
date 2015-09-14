<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_liens`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_liens` (
            `id` int(10) NOT NULL auto_increment,
            `date` varchar(12) NOT NULL default \'\',
            `titre` text NOT NULL,
            `description` text NOT NULL,
            `url` varchar(200) NOT NULL default \'\',
            `cat` int(11) NOT NULL default \'0\',
            `webmaster` text NOT NULL,
            `country` varchar(50) NOT NULL default \'\',
            `count` int(11) NOT NULL default \'0\',
            `broke` int(11) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('liens');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('description', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, description FROM `'. $db_prefix .'_liens`';
        $dbsLinks = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsLinks)) {
            $row['description'] = mysql_real_escape_string(bbcode(stripslashes($row['description']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_liens` SET description = \''. $row['description'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>