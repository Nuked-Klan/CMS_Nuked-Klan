<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_guestbook`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_guestbook` (
            `id` int(9) NOT NULL auto_increment,
            `name` varchar(50) NOT NULL default \'\',
            `email` varchar(60) NOT NULL default \'\',
            `url` varchar(70) NOT NULL default \'\',
            `date` int(11) NOT NULL default \'0\',
            `host` varchar(60) NOT NULL default \'\',
            `comment` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('guestbook');
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
            FROM `'. $this->data['db_prefix'] .'_guestbook`';
        $dbsGuestbook = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsGuestbook)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['comment'] = $bbcode->apply(stripslashes($row['comment']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_guestbook`
                SET '. implode(', ', $data) .'
                WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>