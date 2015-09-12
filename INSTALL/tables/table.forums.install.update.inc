<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_forums`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_forums` (
            `id` int(5) NOT NULL auto_increment,
            `cat` int(11) NOT NULL default \'0\',
            `nom` text NOT NULL,
            `comment` text NOT NULL,
            `moderateurs` text NOT NULL,
            `niveau` int(1) NOT NULL default \'0\',
            `level` int(1) NOT NULL default \'0\',
            `ordre` int(5) NOT NULL default \'0\',
            `level_poll` int(1) NOT NULL default \'0\',
            `level_vote` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $db_prefix .'_forums` VALUES (1, 1, \'Forum\', \'Test Forum\', \'\', 0, 0, 0, 1 ,1);';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $removeEditor = (version_compare($this->data['version'], '1.7.9', '=')) ? true : false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '='))
        $updateBBcode = true;
    else
        $updateBBcode = false;

    if ($removeEditor || $updateBBcode) {
        $sql = 'SELECT id, comment FROM `'. $db_prefix .'_forums`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($req)) {
            if ($removeEditor)
                $row['comment'] = str_replace(array('<p>', '</p>'), '', $row['comment']);

            if ($updateBBcode) 
                $row['comment'] = mysql_real_escape_string(bbcode(stripslashes($row['comment']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_forums` set comment = \''. $row['comment'] .'\' WHERE id = '. $row['id'];
            mysql_query($sql) or die(mysql_error());
        }

        return 'UPDATED';
    }
}

return 'NOTHING_TO_DO';

?>