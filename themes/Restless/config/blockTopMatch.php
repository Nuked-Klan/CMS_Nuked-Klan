<?php

$where = null;

$idTopMatch = $this->get('cfg')->get('blockTopMatch.id');

if(!empty($idTopMatch)){
    $where = 'WHERE A.warid = '.$idTopMatch;
}

$dbsTopMatch = 'SELECT A.warid, A.etat AS state, A.image_adv AS opponentLogo, A.adversaire AS opponentName, A.map,
                        A.tscore_team AS teamScore, A.tscore_adv AS opponentScore, B.titre AS teamName, B.image AS teamLogo,
                        UNIX_TIMESTAMP(DATE(CONCAT(date_an, \'-\', date_mois, \'-\', date_jour, \' \', heure, \':00\'))) AS date
                FROM '.WARS_TABLE.' AS A
                LEFT JOIN '.TEAM_TABLE.' AS B
                ON B.cid = A.team
                '.$where.'
                ORDER BY date DESC
                LIMIT 0, 1';

$dbeTopMatch = mysql_query($dbsTopMatch);

$dbrTopMatch = mysql_fetch_assoc($dbeTopMatch);

$noImage = 'themes/Restless/images/no_image_topmatch.png';

if(empty($dbrTopMatch['teamLogo'])){
    $dbrTopMatch['teamLogo'] = $noImage;
}

if(empty($dbrTopMatch['opponentLogo'])){
    $dbrTopMatch['opponentLogo'] = $noImage;
}

if(empty($dbrTopMatch['teamName'])){
    $dbrTopMatch['teamName'] = 'N/A';
}

$dbrTopMatch['map'] = explode('|', $dbrTopMatch['map']);
$dbrTopMatch['map'] = implode(', ', $dbrTopMatch['map']);

$dbrTopMatch['link'] = 'index.php?file=Wars&op=detail&war_id='.$dbrTopMatch['warid'];;

$dbrTopMatch['title'] = $this->get('cfg')->get('blockTopMatch.title');

$dbrTopMatch['date'] = nkDate($dbrTopMatch['date'], true);

$dbrTopMatch['scoreClass'] = 'matchWin';
$dbrTopMatch['score'] = $dbrTopMatch['teamScore'].' - '.$dbrTopMatch['opponentScore'];

$this->assign('topMatchContent', $dbrTopMatch);