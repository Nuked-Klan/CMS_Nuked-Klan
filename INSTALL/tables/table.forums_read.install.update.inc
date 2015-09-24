<?php

function createForumsReadTable($table, $charset, $collate) {
    $sql = 'DROP TABLE IF EXISTS `'. $table .'`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $table .'` (
            `user_id` varchar(20) NOT NULL default \'\',
            `thread_id` text NOT NULL,
            `forum_id` text NOT NULL,
            PRIMARY KEY  (`user_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());
}


if ($this->data['process'] == 'install') {
    createForumsReadTable($this->data['db_prefix'] .'_forums_read', $charset, $collate);

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_forums_read');

    // update 1.7.9 RC6
    if ($udt->fieldExist('id')) {
        // Read forum read data and store it in PHP session
        if (! isset($_SESSION['step'])) {
            $sql = 'SELECT * FROM `'. $this->data['db_prefix'] .'_forums_read`';
            $dbsForumRead = mysql_query($sql) or die(mysql_error());

            if (mysql_num_rows($dbsForumRead) == 0) {
                createForumsReadTable($this->data['db_prefix'] .'_forums_read', $charset, $collate);

                return 'UPDATED';
            }

            $_SESSION['forumReadData'] = array();

            while ($row = mysql_fetch_assoc($dbsForumRead)) {
                if (! array_key_exists($row['user_id'], $_SESSION['forumReadData'])) {
                    $_SESSION['forumReadData'][$row['user_id']] = array(
                        'thread_id' => ',',
                        'forum_id'  => ','
                    );
                }

                if (strrpos($_SESSION['forumReadData'][$row['user_id']]['thread_id'], ','. $row['thread_id'] .',') === false)
                    $_SESSION['forumReadData'][$row['user_id']]['thread_id'] .= $row['thread_id'] .',';

                if (strrpos($_SESSION['forumReadData'][$row['user_id']]['forum_id'], ','. $row['forum_id'] .',') === false)
                    $_SESSION['forumReadData'][$row['user_id']]['forum_id'] .= $row['forum_id'] .',';
            }

            $_SESSION['nbTableEntries'] = count($_SESSION['forumReadData']);
            $_SESSION['step']           = 2;

            return 'STEP_1_TOTAL_STEP_4';
        }
        // Create temporary table
        else if ($_SESSION['step'] == 2) {
            createForumsReadTable($this->data['db_prefix'] .'_forums_read_tmp', $charset, $collate);
            $_SESSION['step'] = 3;

            return 'STEP_2_TOTAL_STEP_4';
        }
        // Insert forum read data in temporary table
        else if ($_SESSION['step'] == 3) {
            if (! isset($_SESSION['offset']))
                $_SESSION['offset'] = 0;
            else
                $_SESSION['offset'] = $_SESSION['offset'] + 400;

            for ($c = 0; $c < 2; $c++) {
                $i      = 0;
                $insert = array();

                foreach ($_SESSION['forumReadData'] as $userId => $userData) {
                    if ($i < $_SESSION['offset']) {
                        $insert[] = '(\''. $userId .'\', \''. $userData['forum_id'] .'\', \''. $userData['thread_id'] .'\')';

                        if ($i == $_SESSION['offset'] + 200) break;
                    }

                    $i++;
                }

                $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_forums_read`
                    (user_id, forum_id, thread_id) VALUES '. implode(', ', $insert);
                mysql_query($sql) or die(mysql_error());
            }

            if ($_SESSION['offset'] + 400 < $_SESSION['nbTableEntries']) {
                return 'STEP_'. ($_SESSION['offset'] + 400) .'_TOTAL_STEP_'. $_SESSION['nbTableEntries'];
            }
            else {
                unset($_SESSION['offset']);
                unset($_SESSION['nbTableEntries']);
                unset($_SESSION['forumReadData']);

                return 'STEP_3_TOTAL_STEP_4';
            }
        }
        // Drop old forum read table and rename temporary table
        else if ($_SESSION['step'] == 4) {
            $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_read`';
            mysql_query($sql) or die(mysql_error());

            $sql = 'ALTER TABLE `'. $this->data['db_prefix'] .'_forums_read_tmp`
                RENAME AS `'. $this->data['db_prefix'] .'_forums_read`';
            mysql_query($sql) or die(mysql_error());

            unset($_SESSION['step']);

            return 'UPDATED';
        }
    }

    return 'NOTHING_TO_DO';
}

?>