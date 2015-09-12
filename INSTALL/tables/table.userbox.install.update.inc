<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_userbox`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_userbox` (
            `mid` int(50) NOT NULL auto_increment,
            `user_from` varchar(30) NOT NULL default \'\',
            `user_for` varchar(30) NOT NULL default \'\',
            `titre` varchar(50) NOT NULL default \'\',
            `message` text NOT NULL,
            `date` varchar(30) NOT NULL default \'\',
            `status` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`mid`),
            KEY `user_from` (`user_from`),
            KEY `user_for` (`user_for`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT mid, message FROM `'. $db_prefix .'_userbox`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['message'] = mysql_real_escape_string(bbcode(stripslashes($row['message']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_userbox` SET message = \''. $row['message'] .'\' WHERE mid = \''. $row['mid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>