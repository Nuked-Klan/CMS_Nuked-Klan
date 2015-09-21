<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_irc_awards`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_irc_awards` (
            `id` int(20) NOT NULL auto_increment,
            `text` text NOT NULL,
            `date` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('irc_awards');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('text', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'text';
    }

    if (! empty($update)) {
        $sql = 'SELECT id, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_irc_awards`';
        $dbsIrcAwards = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsIrcAwards)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['text'] = $bbcode->apply(stripslashes($row['text']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_irc_awards`
                SET '. implode(', ', $data) .'
                WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>