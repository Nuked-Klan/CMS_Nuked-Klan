<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_sections`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_sections` (
        `artid` int(11) NOT NULL auto_increment,
        `secid` int(11) NOT NULL default \'0\',
        `title` text NOT NULL,
        `content` text NOT NULL,
        `autor` text NOT NULL,
        `autor_id` varchar(20) NOT NULL default \'\',
        `counter` int(11) NOT NULL default \'0\',
        `bbcodeoff` int(1) NOT NULL default \'0\',
        `smileyoff` int(1) NOT NULL default \'0\',
        `date` varchar(12) NOT NULL default \'\',
        PRIMARY KEY  (`artid`),
        KEY `secid` (`secid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('sections');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('content', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'content';
    }

    if (! empty($update)) {
        $sql = 'SELECT artid, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_sections`';
        $dbsSections = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsSections)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['content'] = $bbcode->apply(stripslashes($row['content']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_sections`
                SET '. implode(', ', $data) .'
                WHERE artid = \''. $row['artid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>