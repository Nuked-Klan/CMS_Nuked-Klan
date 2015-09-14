<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_gallery`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_gallery` (
            `sid` int(11) NOT NULL auto_increment,
            `titre` text NOT NULL,
            `description` text NOT NULL,
            `url` varchar(200) NOT NULL default \'\',
            `url2` varchar(200) NOT NULL default \'\',
            `url_file` varchar(200) NOT NULL default \'\',
            `cat` int(11) NOT NULL default \'0\',
            `date` varchar(12) NOT NULL default \'\',
            `count` int(10) NOT NULL default \'0\',
            `autor` text NOT NULL,
            PRIMARY KEY  (`sid`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('gallery');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('description', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT sid, description FROM `'. $db_prefix .'_gallery`';
        $dbsGallery = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsGallery)) {
            $row['description'] = mysql_real_escape_string(bbcode(stripslashes($row['description']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_gallery` SET description = \''. $row['description'] .'\' WHERE sid = \''. $row['sid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>