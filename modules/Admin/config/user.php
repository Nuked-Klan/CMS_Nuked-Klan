<?php

/* nkList configuration */

// Define the list of user
$userList = array(
    'classPrefix' => 'user',
    'limit' => 30,
    'sqlQuery' => 'SELECT U.id, U.pseudo, U.niveau, U.date, S.last_used FROM '. USER_TABLE .' AS U LEFT OUTER JOIN '. SESSIONS_TABLE .' AS S ON U.id = S.user_id WHERE U.niveau > 0',
    'defaultSortables' => array(
        'order'     => array('U.niveau', 'U.date'),
        'dir'       => array('DESC', 'DESC')
    ),
    'sortables' => array(
        'date'      => array('U.date'),// DESC
        'niveau'    => array('U.niveau', 'U.date'),// DESC, DESC
        'last_used' => array('S.last_used'),// DESC
        'pseudo'    => array('U.pseudo'),
    ),
    'fields' => array(
        'pseudo'    => array('label' => _NICK),
        'niveau'    => array('label' => _LEVEL),
        'date'      => array('label' => _DATEUSER),
        'last_used' => array('label' => _LAST.' '._VISIT)
    ),
    'edit' => array(
        'op'                => 'editUser',
        'text'              => _EDITUSER
    ),
    'delete' => array(
        'op'                => 'deleteUser',
        'text'              => _DELETEUSER,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'pseudo',
        //'notDeletableId'   => $user['id']
    ),
    'emptytable' => _NOUSERINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatUserRow'
    )
);

// Define the list of team
$teamList = array(
    'classPrefix' => 'team',
    'sqlQuery' => 'SELECT cid, titre, ordre, game FROM '. TEAM_TABLE,
    'defaultSortables' => array(
        'order'     => array('game', 'ordre')
    ),
    'fields' => array(
        'titre'     => array('label' => _NAME),
        'game'      => array('label' => _GAME),
        'ordre'     => array('label' => _ORDER)
    ),
    'edit' => array(
        'op'                => 'editTeam',
        'text'              => _EDITTHISTEAM
    ),
    'delete' => array(
        'op'                => 'deleteTeam',
        'text'              => _DELTHISTEAM,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NOTEAMINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatTeamRow'
    )
);

// Define the list of banned user
$bannedUserList = array(
    'classPrefix' => 'bannedUser',
    'sqlQuery' => 'SELECT id, ip, pseudo, email FROM '. BANNED_TABLE,
    'defaultSortables' => array(
        'order'     => array('id'),
        'dir'       => array('DESC')
    ),
    'fields' => array(
        'pseudo'    => array('label' => _NICK),
        'email'     => array('label' => _MAIL),
        'ip'        => array('label' => _IP)
    ),
    'edit' => array(
        'op'                => 'editBan',
        'text'              => _EDITTHISIP
    ),
    'delete' => array(
        'op'                => 'deleteBan',
        'text'              => _DELTHISIP,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'ip'
    ),
    'emptytable' => _NOIPINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatBannedUserRow'
    )
);

// Define the list of user rank
$userRankList = array(
    'classPrefix' => 'userRank',
    'sqlQuery' => 'SELECT id, titre, ordre FROM '. TEAM_RANK_TABLE,
    'defaultSortables' => array(
        'order'     => array('ordre', 'titre')
    ),
    'fields' => array(
        'titre'     => array('label' => _TITLE),
        'ordre'     => array('label' => _ORDER)
    ),
    'edit' => array(
        'op'                => 'editRank',
        'text'              => _EDITTHISRANK
    ),
    'delete' => array(
        'op'                => 'deleteRank',
        'text'              => _DELTHISRANK,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NORANKINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatUserRankRow'
    )
);

// Define the list of user validation
$userValidationList = array(
    'classPrefix' => 'userValidation',
    'sqlQuery' => 'SELECT id, pseudo, mail, date FROM '. USER_TABLE .' WHERE niveau = 0',
    'defaultSortables' => array(
        'order'     => array('date')
    ),
    'fields' => array(
        'pseudo'    => array('label' => _NICK),
        'mail'      => array('label' => _MAIL),
        'date'      => array('label' => _DATEUSER),
        'validate'  => array('label' => _VALIDUSER)
    ),
    'edit' => array(
        'op'                => 'editUser',
        'text'              => _EDITUSER
    ),
    'delete' => array(
        'op'                => 'deleteUser',
        'text'              => _DELETEUSER,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'pseudo'
    ),
    'emptytable' => _NOUSERVALIDATION,
    'callbackRowFunction' => array(
        'functionName'      => 'formatUserValidationRow'
    )
);

?>