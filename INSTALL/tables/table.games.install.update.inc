<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_games`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_games` (
            `id` int(11) NOT NULL auto_increment,
            `name` varchar(50) NOT NULL default \'\',
            `titre` varchar(50) NOT NULL default \'\',
            `icon` varchar(150) NOT NULL default \'\',
            `pref_1` varchar(50) NOT NULL default \'\',
            `pref_2` varchar(50) NOT NULL default \'\',
            `pref_3` varchar(50) NOT NULL default \'\',
            `pref_4` varchar(50) NOT NULL default \'\',
            `pref_5` varchar(50) NOT NULL default \'\',
            `map` TEXT NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_games` VALUES
        (1, \'Counter Strike Source\', \''. mysql_real_escape_string($this->_i18n['PREF_CS']) .'\', \'images/games/cs.gif\', \''. mysql_real_escape_string($this->_i18n['OTHER_NICK']) .'\', \''. mysql_real_escape_string($this->_i18n['FAV_MAP']) .'\', \''. mysql_real_escape_string($this->_i18n['FAV_WEAPON']) .'\', \''. mysql_real_escape_string($this->_i18n['SKIN_T']) .'\', \''. mysql_real_escape_string($this->_i18n['SKIN_CT']) .'\', \'de_dust2|de_inferno\');';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_games');

    // install 1.7.9 RC4
    // update 1.7.9 RC5.2
    if (! $udt->fieldExist('map'))
        $udt->addField('map', array('type' => 'TEXT', 'null' => false));

    $udt->modifyTable();

    if ($udt->isUpdated())
        return 'UPDATED';
    else
        return 'NOTHING_TO_DO';
}

?>