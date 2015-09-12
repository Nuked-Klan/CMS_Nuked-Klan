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
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT id, comment FROM `'. $db_prefix .'_guestbook`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['comment'] = mysql_real_escape_string(bbcode(stripslashes($row['comment']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_guestbook` SET comment = \''. $row['comment'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>