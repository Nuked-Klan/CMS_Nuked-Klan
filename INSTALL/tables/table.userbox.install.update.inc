<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_userbox`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_userbox` (
            `mid` int(50) NOT NULL auto_increment,
            `user_from` varchar(30) NOT NULL default \'\',
            `user_for` varchar(30) NOT NULL default \'\',
            `titre` varchar(50) NOT NULL default \'\',
            `message` text NOT NULL,
            `date` varchar(30) NOT NULL default \'\',
            `status` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`mid`),
            KEY `user_from` (`user_from`),
            KEY `user_for` (`user_for`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('userbox');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('message', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'message';
    }

    if (! empty($update)) {
        $sql = 'SELECT mid, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_userbox`';
        $dbsUserbox = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsUserbox)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['message'] = $bbcode->apply(stripslashes($row['message']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_userbox`
                SET '. implode(', ', $data) .'
                WHERE mid = \''. $row['mid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>