<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_users`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_users` (
            `id` varchar(20) NOT NULL default \'\',
            `team` varchar(80) NOT NULL default \'\',
            `team2` varchar(80) NOT NULL default \'\',
            `team3` varchar(80) NOT NULL default \'\',
            `rang` int(11) NOT NULL default \'0\',
            `ordre` int(5) NOT NULL default \'0\',
            `pseudo` text NOT NULL,
            `mail` varchar(80) NOT NULL default \'\',
            `email` varchar(80) NOT NULL default \'\',
            `icq` varchar(50) NOT NULL default \'\',
            `msn` varchar(80) NOT NULL default \'\',
            `aim` varchar(50) NOT NULL default \'\',
            `yim` varchar(50) NOT NULL default \'\',
            `url` varchar(150) NOT NULL default \'\',
            `pass` varchar(80) NOT NULL default \'\',
            `niveau` int(1) NOT NULL default \'0\',
            `date` varchar(30) NOT NULL default \'\',
            `avatar` varchar(100) NOT NULL default \'\',
            `signature` text NOT NULL,
            `user_theme` varchar(30) NOT NULL default \'\',
            `user_langue` varchar(30) NOT NULL default \'\',
            `game` int(11) NOT NULL default \'0\',
            `country` varchar(50) NOT NULL default \'\',
            `count` int(10) NOT NULL default \'0\',
            `erreur` INT(10) NOT NULL default \'0\',
            `token` varchar(13)  DEFAULT NULL,
            `token_time` varchar(10) NOT NULL DEFAULT \'0\',
            PRIMARY KEY  (`id`),
            KEY `team` (`team`),
            KEY `team2` (`team2`),
            KEY `team3` (`team3`),
            KEY `rang` (`rang`),
            KEY `game` (`game`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('userbox');
    $update = $alterTable = $fields = array();

    // install 1.7.9 RC3 - update 1.7.9 RC1
    if (! array_key_exists('erreur', $tableData)) {
        $alterTable[] = 'ADD `erreur` INT(10) NOT NULL default \'0\'';
    }

    // install / update 1.7.9
    if (! array_key_exists('token', $tableData)) {
        $alterTable[] = 'ADD `token` VARCHAR(13) NULL DEFAULT NULL';
        $alterTable[] = 'ADD `token_time` varchar(10) NOT NULL DEFAULT \'0\'';
    }

    if (! empty($alterTable)) {
        $sql = 'ALTER TABLE `'. $db_prefix .'_users` '. implode(', ', $alterTable);
        mysql_query($sql) or die(mysql_error());
        $update[] = 'alter table';
    }

    $sql = 'SELECT pass FROM `'. $db_prefix .'_users` ORDER BY RAND() LIMIT 1';
    $req = mysql_query($sql);
    $row = mysql_fetch_assoc($req);

    $firstChr = substr($row['pass'], 0, 1);

    if ($firstChr != '%' && $firstChr != '#') {
        $update[] = 'update password';
        $fields[] = 'pass';

        if (! isset($_SESSION['hash'])) {
            include_once '../conf.inc.php';

            // install / update 1.7.9 RC1
            if (defined('HASHKEY')) {
                $_SESSION['hash'] = HASHKEY;
            }
            else {
                $_SESSION['hash'] = install::generateHashKey();
            }
        }
    }
    else
        $updatePassword = false;

    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('signature', $tableData)) {
        include 'bbcode.php';
        $update[] = 'bbcode';
        $fields[] = 'signature';
    }

    if (! empty($update)) {
        $sql = 'SELECT id'. implode(', ', $fields) .' FROM `'. $db_prefix .'_users`';
        $dbsUsers = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsUsers)) {
            $data = array();

            if ($updatePassword)
                $data[] = 'pass=\''. install::hashPassword($_SESSION['hash'], $row['pass']) .'\'';

            if ($updateBBcode)
                $data[] = 'signature=\''. mysql_real_escape_string(bbcode(stripslashes($row['signature']), $db_prefix)) .'\'';

            $sql = 'UPDATE `'. $db_prefix .'_users` SET '. implode(', ', $data) .' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>