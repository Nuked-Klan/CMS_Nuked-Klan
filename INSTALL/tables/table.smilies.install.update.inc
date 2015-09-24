<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_smilies`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_smilies` (
            `id` int(5) NOT NULL auto_increment,
            `code` varchar(50) NOT NULL default \'\',
            `url` varchar(100) NOT NULL default \'\',
            `name` varchar(100) NOT NULL default \'\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    // TODO : Traduction ?
    $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_smilies` VALUES
        (1, \':D\', \'biggrin.gif\', \'Very Happy\'),
        (2, \':)\', \'smile.gif\', \'Smile\'),
        (3, \':(\', \'frown.gif\', \'Sad\'),
        (4, \':o\', \'eek.gif\', \'Surprised\'),
        (5, \':?\', \'confused.gif\', \'Confused\'),
        (6, \'8)\', \'cool.gif\', \'Cool\'),
        (7, \':P\', \'tongue.gif\', \'Razz\'),
        (8, \':x\', \'mad.gif\', \'Mad\'),
        (9, \';)\', \'wink.gif\', \'Wink\'),
        (10, \':red:\', \'redface.gif\', \'Embarassed\'),
        (11, \':roll:\', \'rolleyes.gif\', \'Rolling Eyes\');';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    // TODO Preciser la version
    // update 1.7.9
    $sql = 'SELECT id
        FROM `'. $this->data['db_prefix'] .'_smilies`
        WHERE code LIKE \'%\\\'%\'';
    $dbsSmilies = mysql_query($sql) or die(mysql_error());

    $i = 0;

    while ($row = mysql_fetch_assoc($dbsSmilies)) {
        $i++;
        $sql = 'UPDATE `'. $this->data['db_prefix'] .'_smilies`
            SET code = \'#smiley'. $i .'#\'
            WHERE id = \''. $row['id'] .'\'';
        mysql_query($sql) or die(mysql_error());
    }

    return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>