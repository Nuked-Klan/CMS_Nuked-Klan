<?php
/**
 * table.user_social.c.i.u.php
 *
 * `[PREFIX]_user_social` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(USER_SOCIAL_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$userSocialTableCfg = array(
    'fields' => array(
        'id'            => array('type' => 'int(11)',      'null' => false, 'unsigned' => true, 'autoIncrement' => true),
        'name'          => array('type' => 'varchar(30)',  'null' => false),
        'translateName' => array('type' => 'tinyint(1)',   'null' => false, 'unsigned' => true),
        'cssClass'      => array('type' => 'varchar(30)',  'null' => false),
        'field'         => array('type' => 'varchar(30)',  'null' => false),
        'format'        => array('type' => 'varchar(150)', 'null' => true),
        'protect'       => array('type' => 'tinyint(1)',   'null' => false, 'unsigned' => true),
        'openUrl'       => array('type' => 'tinyint(1)',   'null' => false, 'unsigned' => true),
        'active'        => array('type' => 'tinyint(1)',   'null' => false, 'unsigned' => true)
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    if ($dbTable->tableExist())
        $dbTable->checkIntegrity();
    else
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_CHECK_INTEGRITY');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation') {
    if ($dbTable->tableExist())
        $dbTable->checkAndConvertCharsetAndCollation();
    else
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_CONVERT');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install / update 1.8
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $dbTable->createTable($userSocialTableCfg);

    $sql = 'INSERT INTO `'. USER_SOCIAL_TABLE .'`
        (`id`, `name`, `translateName`, `cssClass`, `field`, `format`, `protect`, `openUrl`, `active`)
        VALUES
        (1, \'Email\', 1, \'email\', \'email\', \'mailto:%s\', 1, 0, 0),
        (2, \'ICQ\', 0, \'icq\', \'icq\', \'http://web.icq.com/whitepages/add_me?uin=%s&amp;action=add\', 0, 1, 0),
        (3, \'MSN\', 0, \'msn\', \'msn\', \'mailto:%s\', 1, 0, 0),
        (4, \'AIM\', 0, \'aim\', \'aim\', \'aim:goim?screenname=%1$s&amp;message=Hi+%1$s+Are+you+there+?\', 0, 0, 0),
        (5, \'Yim\', 0, \'yim\', \'yim\', \'http://edit.yahoo.com/config/send_webmesg?.target=%s&amp;.src=pg\', 0, 1, 0),
        (6, \'Xfire\', 0, \'xfire\', \'xfire\', \'xfire:add_friend?user=%s\', 0, 0, 0),
        (7, \'Facebook\', 0, \'facebook\', \'facebook\', \'http://www.facebook.com/%s\', 0, 1, 1),
        (8, \'Origin\', 0, \'origin\', \'origin\', null, 0, 0, 0),
        (9, \'Steam\', 0, \'steam\', \'steam\', \'http://steamcommunity.com/actions/AddFriend/%s\', 0, 1, 0),
        (10, \'Twitter\', 0, \'twitter\', \'twitter\', \'http://twitter.com/#!/%s\', 0, 1, 0),
        (11, \'Skype\', 0, \'skype\', \'skype\', \'skype:%s?call\', 0, 0, 1),
        (12, \'Url\', 1, \'website\', \'url\', \'%s\', 0, 1, 1);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>