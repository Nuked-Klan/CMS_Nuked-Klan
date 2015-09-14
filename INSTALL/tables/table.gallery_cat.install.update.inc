<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_gallery_cat`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_gallery_cat` (
            `cid` int(11) NOT NULL auto_increment,
            `parentid` int(11) NOT NULL default \'0\',
            `titre` varchar(50) NOT NULL default \'\',
            `description` text NOT NULL,
            `position` int(2) unsigned NOT NULL default \'0\',
            PRIMARY KEY  (`cid`),
            KEY `parentid` (`parentid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('gallery_cat');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('description', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT cid, description FROM `'. $db_prefix .'_gallery_cat`';
        $req = mysql_query($dbsGalleryCat) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsGalleryCat)) {
            $row['description'] = mysql_real_escape_string(bbcode(stripslashes($row['description']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_gallery_cat` SET description = \''. $row['description'] .'\' WHERE cid = \''. $row['cid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>