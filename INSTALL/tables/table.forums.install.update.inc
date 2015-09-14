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
    $tableData  = $this->getTableData('forums');
    $update     = array();

    // TODO : Version ???
    if (version_compare($this->data['version'], '1.7.9', '='))
        $update[] = 'remove editor';

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=')) {
        include 'bbcode.php';
        $update[] = 'bbcode';
    }

    if (! empty($update) && array_key_exists('comment', $tableData)) {
        $sql = 'SELECT id, comment FROM `'. $db_prefix .'_forums`';
        $dbsForum = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsForum)) {
            if (in_array('remove editor', $update))
                $row['comment'] = str_replace(array('<p>', '</p>'), '', $row['comment']);

            if (in_array('bbcode', $update))
                $row['comment'] = mysql_real_escape_string(bbcode(stripslashes($row['comment']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_forums` set comment = \''. $row['comment'] .'\' WHERE id = '. $row['id'];
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>