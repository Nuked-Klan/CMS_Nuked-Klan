<?php

$where = '';

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

$dbeTopMatch = nkDB_execute($dbsTopMatch);

$dbrTopMatch = nkDB_fetchAssoc($dbeTopMatch);

$noImage = 'themes/Restless/images/no_image_topmatch.png';

if(empty($dbrTopMatch['teamLogo'])){
    $dbrTopMatch['teamLogo'] = $noImage;
}

if(empty($dbrTopMatch['opponentLogo'])){
    $dbrTopMatch['opponentLogo'] = $noImage;
}

if(empty($dbrTopMatch['teamName'])){
    $dbrTopMatch['teamName'] = __('NA');
}

if ($dbrTopMatch['map'] != '') {
    $dbrGameMaps = nkDB_selectMany(
        'SELECT name
        FROM '. GAMES_MAP_TABLE .'
        WHERE id IN ('. str_replace('|', ',', $dbrTopMatch['map']) .')'
    );

    if ($dbrGameMaps) {
        $dbrTopMatch['map'] = implode(', ', array_column($dbrGameMaps, 'name'));
    }
}

if ($dbrTopMatch['map'] == '') $dbrTopMatch['map'] = __('NA');

$dbrTopMatch['link'] = 'index.php?file=Wars&op=detail&war_id='.$dbrTopMatch['warid'];;

$dbrTopMatch['title'] = $this->get('cfg')->get('blockTopMatch.title');

$dbrTopMatch['date'] = nkDate($dbrTopMatch['date'], true);

$dbrTopMatch['scoreClass'] = 'matchWin';
$dbrTopMatch['score'] = $dbrTopMatch['teamScore'].' - '.$dbrTopMatch['opponentScore'];

$this->assign('topMatchContent', $dbrTopMatch);
