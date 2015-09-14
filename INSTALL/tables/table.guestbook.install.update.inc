<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_guestbook`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_guestbook` (
            `id` int(9) NOT NULL auto_increment,
            `name` varchar(50) NOT NULL default \'\',
            `email` varchar(60) NOT NULL default \'\',
            `url` varchar(70) NOT NULL default \'\',
            `date` int(11) NOT NULL default \'0\',
            `host` varchar(60) NOT NULL default \'\',
            `comment` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('guestbook');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('comment', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, comment FROM `'. $db_prefix .'_guestbook`';
        $dbsGuestbook = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsGuestbook)) {
            $row['comment'] = mysql_real_escape_string(bbcode(stripslashes($row['comment']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_guestbook` SET comment = \''. $row['comment'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>