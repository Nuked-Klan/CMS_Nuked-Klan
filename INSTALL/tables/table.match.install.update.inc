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
    $update = $alterTable = $fields = array();

    $tableData = $this->getTableData('match');

    // install / update 1.7.9 RC1
    if (! array_key_exists('map', $tableData)) {
        $alterTable[] = 'ADD `map` TEXT NOT NULL';

        if (array_key_exists('map_1', $tableData)) {
            $update[] = 'map';
            $fields[] = 'map_1';
            $fields[] = 'map_2';
            $fields[] = 'map_3';
        }
    }

    // install / update 1.7.9 RC6
    if (version_compare($this->data['version'], '1.7.9', '<=') && array_key_exists('report', $tableData)) {
        $bbcode = $this->_loadBBcode();
        $update[] = 'bbcode';
        $fields[] = 'report';
    }

    // install / update 1.7.9 RC1
    if (! array_key_exists('tscore_team', $tableData)) {
        $alterTable[] = 'ADD `tscore_team` FLOAT DEFAULT NULL';
        $update[] = 'tscore_team';
        $fields[] = 'score_team';
    }

    // install / update 1.7.9 RC1
    if (! array_key_exists('tscore_adv', $tableData)) {
        $alterTable[] = 'ADD `tscore_adv` FLOAT DEFAULT NULL';
        $update[] = 'tscore_adv';
        $fields[] = 'score_adv';
    }

    if (! empty($alterTable)) {
        $sql = 'ALTER TABLE `'. $this->data['db_prefix'] .'_match` '
            . implode(', ', $alterTable);
        mysql_query($sql) or die(mysql_error());
    }

    if (! empty($update)) {
        $sql = 'SELECT warid, '. implode(', ', $fields) .' 
            FROM `'. $this->data['db_prefix'] .'_match`';
        $dbsMatch = mysql_query($sql) or die(mysql_error());

        //$nbMatch = mysql_num_rows($dbsMatch);

        //if ($nbMatch > 40 && ! isset($_SESSION['updateNbStep'])) {
        //    $nbStep = ceil($nbMatch / 40);
        //    $_SESSION['updateStep'] = 1;
        //    $_SESSION['updateNbStep'] = $nbStep;
        //}

        //$i = 0;

        while ($row = mysql_fetch_assoc($dbsMatch)) {
            $data = $setFields = array();

            if (in_array('map', $update))
                $setFields['map'] = $row['map_1'] .'|'. $row['map_2'] .'|'. $row['map_3'];

            if (in_array('bbcode', $update))
                $setFields['report'] = $bbcode->apply(stripslashes($row['report']));

            if (in_array('tscore_team', $update))
                $setFields['tscore_team'] = $row['score_team'];

            if (in_array('tscore_adv', $update))
                $setFields['tscore_adv'] = $row['score_adv'];

            foreach ($setFields as $field)
                $data[] = $field .' = \''. mysql_real_escape_string($row[$field]) .'\'';

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_match`
                SET '. implode(', ', $data) .'
                WHERE warid = \''. $row['warid'] .'\'';
            mysql_query($sql) or die(mysql_error());
            //$i++;
        }

        if (array_key_exists('map_1', $tableData)) {
            $sql = 'ALTER TABLE `'. $this->data['db_prefix'] .'_match`
                DROP `map_1`, DROP `map_2`, DROP `map_3`';
            mysql_query($sql) or die(mysql_error());
            $update[] = 'delete fields map_1, map_2, map_3';
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>