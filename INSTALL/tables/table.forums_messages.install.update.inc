<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_messages`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums_messages` (
            `id` int(5) NOT NULL auto_increment,
            `titre` text NOT NULL,
            `txt` text NOT NULL,
            `date` varchar(12) NOT NULL default \'\',
            `edition` text NOT NULL,
            `auteur` text NOT NULL,
            `auteur_id` varchar(20) NOT NULL default \'\',
            `auteur_ip` varchar(20) NOT NULL default \'\',
            `bbcodeoff` int(1) NOT NULL default \'0\',
            `smileyoff` int(1) NOT NULL default \'0\',
            `cssoff` int(1) NOT NULL default \'0\',
            `usersig` int(1) NOT NULL default \'0\',
            `emailnotify` int(1) NOT NULL default \'0\',
            `thread_id` int(5) NOT NULL default \'0\',
            `forum_id` mediumint(10) NOT NULL default \'0\',
            `file` varchar(200) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `auteur_id` (`auteur_id`),
            KEY `thread_id` (`thread_id`),
            KEY `forum_id` (`forum_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('forums_messages');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('txt', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'txt';
    }

    if (! empty($update)) {
        $sql = 'SELECT id, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_forums_messages`';
        $dbsForumMessages = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsForumMessages)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['txt'] = $bbcode->apply(stripslashes($row['txt']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_forums_messages`
                SET '. implode(', ', $data) .'
                WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>