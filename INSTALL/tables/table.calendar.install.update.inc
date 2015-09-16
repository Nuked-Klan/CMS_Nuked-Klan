<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_calendar`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_calendar` (
            `id` int(11) NOT NULL auto_increment,
            `titre` text NOT NULL,
            `description` text NOT NULL,
            `date_jour` int(2) default NULL,
            `date_mois` int(2) default NULL,
            `date_an` int(4) default NULL,
            `heure` varchar(5) NOT NULL default \'\',
            `auteur` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('calendar');
    $update     = array();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('description', $tableData)) {
        include 'bbcode.php';

        $sql = 'SELECT id, description FROM `'. $db_prefix .'_calendar`';
        $dbsCalendar = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_assoc($dbsCalendar)) {
            $row['description'] = mysql_real_escape_string(bbcode(stripslashes($row['description']), $db_prefix, $this->_i18n));

            $sql = 'UPDATE `'. $db_prefix .'_calendar` SET description = \''. $row['description'] .'\' WHERE id = \''. $row['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update[] = 'bbcode';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>