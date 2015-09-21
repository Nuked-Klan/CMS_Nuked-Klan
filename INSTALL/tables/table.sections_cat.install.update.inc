<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_sections_cat`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_sections_cat` (
            `secid` int(11) NOT NULL auto_increment,
            `parentid` int(11) NOT NULL default \'0\',
            `secname` varchar(40) NOT NULL default \'\',
            `description` text NOT NULL,
            `position` int(2) unsigned NOT NULL default \'0\',
            PRIMARY KEY  (`secid`),
            KEY `parentid` (`parentid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('sections_cat');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('description', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'description';
    }

    if (! empty($update)) {
        $sql = 'SELECT secid, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_sections_cat`';
        $dbsSectionsCat = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsSectionsCat)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['description'] = $bbcode->apply(stripslashes($row['description']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_sections_cat`
                SET '. implode(', ', $data) .'
                WHERE secid = \''. $row['secid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>