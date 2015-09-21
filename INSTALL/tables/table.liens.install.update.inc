<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_liens`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_liens` (
            `id` int(10) NOT NULL auto_increment,
            `date` varchar(12) NOT NULL default \'\',
            `titre` text NOT NULL,
            `description` text NOT NULL,
            `url` varchar(200) NOT NULL default \'\',
            `cat` int(11) NOT NULL default \'0\',
            `webmaster` text NOT NULL,
            `country` varchar(50) NOT NULL default \'\',
            `count` int(11) NOT NULL default \'0\',
            `broke` int(11) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('liens');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('description', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'description';
    }

    if (! empty($update)) {
        $sql = 'SELECT id, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_liens`';
        $dbsLinks = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsLinks)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['description'] = $bbcode->apply(stripslashes($row['description']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_liens`
                SET '. implode(', ', $data) .'
                WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>