<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_irc_awards`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_irc_awards` (
            `id` int(20) NOT NULL auto_increment,
            `text` text NOT NULL,
            `date` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('irc_awards');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('text', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, text FROM `'. $db_prefix .'_irc_awards`';
        $dbsIrcAwards = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsIrcAwards)) {
            $row['text'] = mysql_real_escape_string(bbcode(stripslashes($row['text']), $db_prefix, $this->_i18n));

            $sql = 'UPDATE `'. $db_prefix .'_irc_awards` SET text = \''. $row['text'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>