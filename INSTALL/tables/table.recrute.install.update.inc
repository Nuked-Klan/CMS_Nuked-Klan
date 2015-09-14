<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_recrute`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_recrute` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(12) NOT NULL default \'\',
            `pseudo` text NOT NULL,
            `prenom` text NOT NULL,
            `age` int(3) NOT NULL default \'0\',
            `mail` varchar(80) NOT NULL default \'\',
            `icq` varchar(50) NOT NULL default \'\',
            `country` text NOT NULL,
            `game` int(11) NOT NULL default \'0\',
            `connection` text NOT NULL,
            `experience` text NOT NULL,
            `dispo` text NOT NULL,
            `comment` text NOT NULL,
            PRIMARY KEY  (`id`),
            KEY `game` (`game`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('recrute');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('comment', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, comment FROM `'. $db_prefix .'_recrute`';
        $dbsRecruit = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsRecruit)) {
            $row['comment'] = mysql_real_escape_string(bbcode(stripslashes($row['comment']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_recrute` SET comment = \''. $row['comment'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>