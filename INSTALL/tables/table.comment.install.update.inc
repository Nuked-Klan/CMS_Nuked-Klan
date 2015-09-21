<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_comment`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_comment` (
            `id` int(10) NOT NULL auto_increment,
            `module` varchar(30) NOT NULL default \'0\',
            `im_id` int(100) default NULL,
            `autor` text,
            `autor_id` varchar(20) NOT NULL default \'\',
            `titre` text NOT NULL,
            `comment` text,
            `date` varchar(12) default NULL,
            `autor_ip` varchar(20) default NULL,
            PRIMARY KEY  (`id`),
            KEY `im_id` (`im_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('comment');
    $update = $fields = array();

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('comment', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'comment';
    }

    if (! empty($update)) {
        $sql = 'SELECT id, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_comment`';
        $dbsComment = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsComment)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $row['comment'] = $bbcode->apply(stripslashes($row['comment']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_comment`
                SET '. implode(', ', $data) .'
                WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>