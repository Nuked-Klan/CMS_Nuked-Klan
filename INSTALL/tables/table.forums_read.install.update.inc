<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_forums_read`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_forums_read` (
            `user_id` varchar(20) NOT NULL default \'\',
            `thread_id` text NOT NULL,
            `forum_id` text NOT NULL,
            PRIMARY KEY  (`user_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('forums_read');
    $update     = array();

    // update 1.7.9 RC6
    if (array_key_exists('id', $tableData)) {
        $sql = 'ALTER TABLE `nuked_forums_read` DROP `id`, DROP INDEX `user_id`, ADD PRIMARY KEY(`user_id`);';
        mysql_query($sql) or die(mysql_error());

/*
        $sql = 'SELECT * FROM `'. $db_prefix .'_forums_read`';
        $dbsForumRead = mysql_query($sql) or die(mysql_error());

        $forumReadData = array();

        while ($row = mysql_fetch_assoc($dbsForumRead)) {
            if (!isset($forumReadData[$row['user_id']]['thread_id']) || empty($forumReadData[$row['user_id']]['thread_id']))
                $forumReadData[$row['user_id']]['thread_id'] = ',';

            if (!isset($forumReadData[$row['user_id']]['forum_id']) || empty($forumReadData[$row['user_id']]['forum_id']))
                $forumReadData[$row['user_id']]['forum_id'] = ',';

            if (strrpos($forumReadData[$row['user_id']]['thread_id'], ',' . $row['thread_id'] . ',') === false)
                $forumReadData[$row['user_id']]['thread_id'] .= $row['thread_id'] .',';

            if (strrpos($forumReadData[$row['user_id']]['forum_id'], ',' . $row['forum_id'] . ',') === false)
                $forumReadData[$row['user_id']]['forum_id'] .= $row['forum_id'] .',';
        }

        if (! empty($forumReadData)) {
            $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_forums_read`';
            mysql_query($sql) or die(mysql_error());

            $sql = 'CREATE TABLE IF NOT EXISTS `'. $db_prefix .'_forums_read` (
                    `user_id` varchar(20) NOT NULL PRIMARY KEY,
                    `forum_id` text NOT NULL,
                    `thread_id` text NOT NULL
                ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
            mysql_query($sql) or die(mysql_error());

            $sql = 'INSERT INTO `'. $db_prefix .'_forums_read` (user_id, forum_id, thread_id) VALUES ';
            $i = 0;

            foreach ($forumReadData as $userId => $userData) {
                if ($i > 0)
                    $sql .= ', ';

                $sql .= '(\''. $userId .'\', \''. $userData['forum_id'] .'\', \''. $userData['thread_id'] .'\')';
                $i++;
            }

            mysql_query($sql) or die(mysql_error());
        }
*/

        $update[] = true;
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>