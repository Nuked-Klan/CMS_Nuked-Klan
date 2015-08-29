<?php

$this->assign('blockTeamTitle', $this->get('cfg')->get('blockTeam.title'));

$this->assign('blockTeamActive', $this->get('cfg')->get('blockTeam.active'));

$dbsTeam = 'SELECT CONCAT(\'index.php?file=Team&cid=\', cid) AS link, titre AS name, image
            FROM '.TEAM_TABLE.'
            ORDER BY ordre, name';

$dbeTeam = mysql_query($dbsTeam);

$arrayTemp = array();
$i = 0;
while($dbrTeam = mysql_fetch_assoc($dbeTeam)){
    $arrayTemp[$i] = $dbrTeam;
    $i++;
}

$this->assign('blockTeamContent', $arrayTemp);