<?php

$this->assign('blockMatchTitle', $this->get('cfg')->get('blockMatch.title'));

$this->assign('nbMatchs', $this->get('cfg')->get('blockMatch.nbItems'));

$this->assign('blockMatchActive', $this->get('cfg')->get('blockMatch.active'));

$dbsMatch = 'SELECT A.warid, A.etat, A.pays_adv AS country, A.adversaire AS opponentName, A.tscore_team AS teamScore, A.tscore_adv AS opponentScore, A.team, A.game, B.titre AS teamName, C.name AS gameName, C.icon, UNIX_TIMESTAMP(DATE(CONCAT(date_an, \'-\', date_mois, \'-\', date_jour, \' \', heure, \':00\'))) AS date
             FROM '.WARS_TABLE.' AS A
             LEFT JOIN '.TEAM_TABLE.' AS B
             ON B.cid = A.team
             LEFT JOIN '.GAMES_TABLE.' AS C
             ON C.id = A.game
             ORDER BY date DESC
             LIMIT 0, '.$this->get('nbMatchs');
$dbeMatch = mysql_query($dbsMatch);

$arrayTemp = array();
$i = 0;

while ($row = mysql_fetch_assoc($dbeMatch)) {
    $arrayTemp[$i]['icon'] = $row['icon'];
    $arrayTemp[$i]['flag'] = 'images/flags/'.$row['country'];
    $arrayTemp[$i]['matchLink'] = 'index.php?file=Wars&amp;op=detail&amp;war_id='.$row['warid'];
    $arrayTemp[$i]['teamName'] = empty($row['teamName']) ? 'N/A' : $row['teamName'];
    $arrayTemp[$i]['opponentName'] = $row['opponentName'];
    $arrayTemp[$i]['score'] = $row['teamScore'].' - '.$row['opponentScore'];
    $arrayTemp[$i]['link'] = 'index.php?file=Wars&op=detail&war_id='.$row['warid'];
    if($row['etat'] == 0){
        $arrayTemp[$i]['score'] = TO_COME;
    }
    if ($row['teamScore'] > $row['opponentScore']) {
        $arrayTemp[$i][scoreClass] = 'Win';
    }
    else if ($row['teamScore'] < $row['opponentScore']) {
        $arrayTemp[$i][scoreClass] = 'Lose';
    }
    else {
        $arrayTemp[$i][scoreClass] = 'Draw';
    }
    $i++;
}

$this->assign('blockMatchContent', $arrayTemp);