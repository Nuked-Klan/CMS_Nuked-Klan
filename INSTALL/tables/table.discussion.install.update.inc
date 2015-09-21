<?php

// install / update 1.7.9 RC1
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('discussion'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_discussion`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_discussion` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(30) NOT NULL default \'0\',
            `pseudo`  text NOT NULL,
            `texte`  text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

if ($this->data['process'] == 'update') {
    $tableData = $this->getTableData('discussion');
    $update = $fields = array();

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('texte', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'texte';
    }

    if (! empty($update)) {
        $sql = 'SELECT id, '. implode(', ', $fields) .'
            FROM `'. $this->data['db_prefix'] .'_discussion`';
        $dbsDiscussion = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsDiscussion)) {
            $data = $setFields = array();

            if (in_array('bbcode', $update))
                $setFields['texte'] = $bbcode->apply(stripslashes($row['texte']));

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_discussion`
                SET '. implode(', ', $data) .'
                WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>