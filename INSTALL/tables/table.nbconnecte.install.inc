<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_nbconnecte`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_nbconnecte` (
            `IP` varchar(30) NOT NULL default \'\',
            `type` int(10) NOT NULL default \'0\',
            `date` int(14) NOT NULL default \'0\',
            `user_id` varchar(20) NOT NULL default \'\',
            `username` varchar(40) NOT NULL default \'\',
            PRIMARY KEY  ( `IP` , `user_id` )
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $tableData  = $this->getTableData('nbconnecte');
    $update     = array();

    // update 1.7.9 RC6
    if (array_key_exists('IP', $tableData)) {
        if (array_key_exists('type', $tableData['IP'])
            && $tableData['IP']['type'] == 'varchar(15)'
        ) {
            $sql = 'ALTER TABLE `'. $db_prefix .'_nbconnecte` MODIFY `IP` VARCHAR(30) NOT NULL DEFAULT \'\';';
            mysql_query($sql) or die(mysql_error());
            $update = true;
        }
    }
    else {
        die('Missing IP field in `'. $db_prefix .'_nbconnecte` table !');
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>