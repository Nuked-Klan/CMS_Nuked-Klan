<?php

// install / update 1.7.9 RC1
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('discussion'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_discussion`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_discussion` (
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
    $tableData  = $this->getTableData('discussion');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('texte', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, texte FROM `'. $db_prefix .'_discussion`';
        $dbsDiscussion = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsDiscussion)) {
            $row['texte'] = mysql_real_escape_string(bbcode(stripslashes($row['texte']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_discussion` SET texte = \''. $row['texte'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>