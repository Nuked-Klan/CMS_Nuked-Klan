<?php

// install / update 1.7.9 RC3
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('contact'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_contact`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_contact` (
            `id` int(11) NOT NULL auto_increment,
            `titre` varchar(200) NOT NULL default \'\',
            `message` text NOT NULL,
            `email` varchar(80) NOT NULL default \'\',
            `nom` varchar(200) NOT NULL default \'\',
            `ip` varchar(50) NOT NULL default \'\',
            `date` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `titre` (`titre`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('contact');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('message', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'message';
    }

    if (! empty($update)) {
        $sql = 'SELECT id, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_contact`';
        $dbsContact = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsContact)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $row['message'] = $bbcode->apply(stripslashes($row['message']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_contact`
                SET '. implode(', ', $data) .'
                WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>