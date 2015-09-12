<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_match`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_match` (
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
    // TODO : A FINIR
    $update = false;
    
    // BBcode
    //'_match' => array('warid', 'report')

    if (! $this->fieldExist('match', 'map')) {
        $sql = 'SELECT warid, map_1, map_2, map_3, score_team, score_adv FROM `'. $db_prefix .'_match`';
        $req = mysql_query($sql) or die(mysql_error());

        //$nbMatch = mysql_num_rows($req);

        //if ($nbMatch > 40 && ! isset($_SESSION['updateNbStep'])) {
        //    $nbStep = ceil($nbMatch / 40);
        //    $_SESSION['updateStep'] = 1;
        //    $_SESSION['updateNbStep'] = $nbStep;
        //}

        $matchsData = array();

        while ($row = mysql_fetch_array($sql)) {
            $matchsData[] = array(
                'id'           => $row['warid'],
                'map'          => $row['map_1'] .'|'. $row['map_2'] .'|'. $row['map_3'],
                'score_team'   => $row['score_team'],
                'score_adv'    => $row['score_adv']
            );
        }

        $sql = 'ALTER TABLE `'. $db_prefix .'_match` DROP `map_1`, DROP `map_2`, DROP `map_3`';
        mysql_query($sql) or die(mysql_error());

        $sql = 'ALTER TABLE `'. $db_prefix .'_match`
            ADD `map` TEXT NOT NULL,
            ADD `tscore_team` FLOAT DEFAULT NULL,
            ADD `tscore_adv` FLOAT DEFAULT NULL';
        mysql_query($sql) or die(mysql_error());

        $i = 0;

        foreach ($matchsData as $match) {
            $sql = 'UPDATE `'. $db_prefix .'_match` SET
                map = \''. mysql_real_escape_string($match['map']) .'\',
                tscore_team = \''. $match['score_team'] .'\',
                tscore_adv = \''. $match['score_adv'] .'\'
                WHERE warid = \''. $match['id'] .'\'';
            mysql_query($sql) or die(mysql_error());
            $i++;
        }

        $update = true;
    }
/*
    $sql = 'SELECT id, texte, suite FROM `'. $db_prefix .'_news`';
    $req = mysql_query($sql) or die(mysql_error());

    while ($row = mysql_fetch_row($req)) {
        $row['texte'] = mysql_real_escape_string(bbcode(stripslashes($row['texte']), $db_prefix));

        $sqlSuite = '';

        if ($row['suite'] != '') {
            $row['suite'] = mysql_real_escape_string(bbcode(stripslashes($row['suite']), $db_prefix));
            $sqlSuite = ', suite = \''. $row['suite'] .'\'';
        }

        $sql = 'UPDATE `'. $db_prefix .'_news` SET texte = \''. $row['texte'] .'\''. $sqlSuite .' WHERE id = \''. $row['id'] .'\'';
        mysql_query($sql) or die(mysql_error());
    }
*/

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>