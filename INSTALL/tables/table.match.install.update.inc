<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_match`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_match` (
            `warid` int(10) NOT NULL auto_increment,
            `etat` int(1) NOT NULL default \'0\',
            `team` int(11) NOT NULL default \'0\',
            `game` int(11) NOT NULL default \'0\',
            `adversaire` text,
            `url_adv` varchar(60) default NULL,
            `pays_adv` varchar(50) NOT NULL default \'\',
            `type` varchar(100) default NULL,
            `style` varchar(100) NOT NULL default \'\',
            `date_jour` int(2) default NULL,
            `date_mois` int(2) default NULL,
            `date_an` int(4) default NULL,
            `heure` varchar(10) NOT NULL default \'\',
            `map` text,
            `tscore_team` float default NULL,
            `tscore_adv` float default NULL,
            `score_team` text NOT NULL,
            `score_adv` text NOT NULL,
            `report` text,
            `auteur` varchar(50) default NULL,
            `url_league` varchar(100) default NULL,
            `dispo` text,
            `pas_dispo` text,
            PRIMARY KEY  (`warid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of match database table
     */
    function updateMatchRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('UPDATE_FIELD map', $updateList))
            $setFields['map'] = $row['map_1'] .'|'. $row['map_2'] .'|'. $row['map_3'];

        if (in_array('UPDATE_FIELD tscore_team', $updateList))
            $setFields['tscore_team'] = $row['score_team'];

        if (in_array('UPDATE_FIELD tscore_adv', $updateList))
            $setFields['tscore_adv'] = $row['score_adv'];

        if (in_array('BBCODE', $updateList))
            $setFields['report'] = $vars['bbcode']->apply(stripslashes($row['report']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_match');

    // install / update 1.7.9 RC1
    if (! $udt->fieldExist('map')) {
        $udt->addField('map', array('type' => 'TEXT', 'null' => false))
            ->updateFieldData('UPDATE_FIELD map', array('map_1', 'map_2', 'map_3'));
    }

    // install / update 1.7.9 RC6
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('report')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'report');
    }

    // install / update 1.7.9 RC1
    if (! $udt->fieldExist('tscore_team')) {
        $udt->addField('tscore_team', array('type' => 'FLOAT', 'default' => 'NULL'))
            ->updateFieldData('UPDATE_FIELD tscore_team', 'score_team');
    }

    // install / update 1.7.9 RC1
    if (! $udt->fieldExist('tscore_adv')) {
        $udt->addField('tscore_adv', array('type' => 'FLOAT', 'default' => 'NULL'))
            ->updateFieldData('UPDATE_FIELD tscore_adv', 'score_adv');
    }

    $udt->modifyTable();

    $result = $udt->execute('warid', 'updateMatchRow');

    if ($result == 'UPDATED') {
        if ($udt->fieldExist('map_1'))
            $udt->dropField('map_1');

        if ($udt->fieldExist('map_2'))
            $udt->dropField('map_2');

        if ($udt->fieldExist('map_3'))
            $udt->dropField('map_3');

        $udt->modifyTable();
    }

    return $result;
}

?>