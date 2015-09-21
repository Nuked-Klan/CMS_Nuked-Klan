<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_defie`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_defie` (
            `id` int(11) NOT NULL auto_increment,
            `send` varchar(12) NOT NULL default \'\',
            `pseudo` text NOT NULL,
            `clan` text NOT NULL,
            `mail` varchar(80) NOT NULL default \'\',
            `icq` varchar(50) NOT NULL default \'\',
            `irc` varchar(50) NOT NULL default \'\',
            `url` varchar(200) NOT NULL default \'\',
            `pays` text NOT NULL,
            `date` varchar(20) NOT NULL default \'\',
            `heure` varchar(10) NOT NULL default \'\',
            `serveur` text NOT NULL,
            `game` int(11) NOT NULL default \'0\',
            `type` text NOT NULL,
            `map` text NOT NULL,
            `comment` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('defie');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('comment', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'comment';
    }

    if (! empty($update)) {
        $sql = 'SELECT id, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_defie`';
        $dbsDefy = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsDefy)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['comment'] = $bbcode->apply(stripslashes($row['comment']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_defie`
                SET '. implode(', ', $data) .'
                WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>