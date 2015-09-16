<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_sondage_data`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_sondage_data` (
            `sid` int(11) NOT NULL default \'0\',
            `optionText` char(50) NOT NULL default \'\',
            `optionCount` int(11) NOT NULL default \'0\',
            `voteID` int(11) NOT NULL default \'0\',
            KEY `sid` (`sid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $db_prefix .'_sondage_data` VALUES
        (1, \''. $this->_i18n['ROXX'] .'\', 0, 1),
        (1, \''. $this->_i18n['NOT_BAD'] .'\', 0, 2),
        (1, \''. $this->_i18n['SHIET'] .'\', 0, 3),
        (1, \''. $this->_i18n['WHATS_NK'] .'\', 0, 4);';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>