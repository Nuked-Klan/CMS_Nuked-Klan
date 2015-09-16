<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_sondage`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_sondage` (
            `sid` int(11) NOT NULL auto_increment,
            `titre` varchar(100) NOT NULL default \'\',
            `date` varchar(15) NOT NULL default \'0\',
            `niveau` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`sid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $db_prefix .'_sondage` VALUES
        (1, \''. $this->_i18n['LIKE_NK'] .'\', \''. time() .'\', 0);';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>