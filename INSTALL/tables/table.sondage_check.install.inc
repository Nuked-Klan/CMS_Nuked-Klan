<?php

// TODO : champ ip a revoir

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_sondage_check`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_sondage_check` (
            `ip` varchar(20) NOT NULL default \'\',
            `pseudo` varchar(50) NOT NULL default \'\',
            `heurelimite` int(14) NOT NULL default \'0\',
            `sid` varchar(30) NOT NULL default \'\',
            KEY `sid` (`sid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>