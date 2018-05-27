<?php
/**
 * table.block.c.i.u.php
 *
 * `[PREFIX]_block` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(BLOCK_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$blockTableCfg = array(
    'fields' => array(
        'bid'      => array('type' => 'int(10)',      'null' => false,  'autoIncrement' => true),
        'active'   => array('type' => 'int(1)',       'null' => false,  'default' => '\'0\''),
        'position' => array('type' => 'int(2)',       'null' => false,  'default' => '\'0\''),
        'module'   => array('type' => 'varchar(100)', 'null' => false,  'default' => '\'\''),
        'titre'    => array('type' => 'text',         'null' => false),
        'content'  => array('type' => 'text',         'null' => false),
        'type'     => array('type' => 'varchar(30)',  'null' => false , 'default' => '\'0\''),
        'nivo'     => array('type' => 'int(1)',       'null' => false,  'default' => '\'0\''),
        'page'     => array('type' => 'text',         'null' => false)
    ),
    'primaryKey' => array('bid'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of block database table
 */
function updateBlockDbTableRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['content'] = $vars['bbcode']->apply(stripslashes($row['content']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('bid', 'active', 'position', 'module', 'titre', 'content', 'type', 'nivo', 'page');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->createTable($blockTableCfg);

    // TODO UPDATE BLOCK MENU
    // (2, 1, 1, \'\', \''. $this->_db->quote($this->_i18n['NAV']) .'\', \'[News]|'. $this->_db->quote($this->_i18n['NAV_NEWS']) .'||0|NEWLINE[Archives]|'. $this->_db->quote($this->_i18n['NAV_ARCHIV']) .'||0|NEWLINE[Forum]|'. $this->_db->quote($this->_i18n['NAV_FORUM']) .'||0|NEWLINE[Download]|'. $this->_db->quote($this->_i18n['NAV_DOWNLOAD']) .'||0|NEWLINE[Members]|'. $this->_db->quote($this->_i18n['NAV_MEMBERS']) .'||0|NEWLINE[Team]|'. $this->_db->quote($this->_i18n['NAV_TEAM']) .'||0|NEWLINE[Defy]|'. $this->_db->quote($this->_i18n['NAV_DEFY']) .'||0|NEWLINE[Recruit]|'. $this->_db->quote($this->_i18n['NAV_RECRUIT']) .'||0|NEWLINE[Sections]|'. $this->_db->quote($this->_i18n['NAV_ART']) .'||0|NEWLINE[Server]|'. $this->_db->quote($this->_i18n['NAV_SERVER']) .'||0|NEWLINE[Links]|'. $this->_db->quote($this->_i18n['NAV_LINKS']) .'||0|NEWLINE[Calendar]|'. $this->_db->quote($this->_i18n['NAV_CALENDAR']) .'||0|NEWLINE[Gallery]|'. $this->_db->quote($this->_i18n['NAV_GALLERY']) .'||0|NEWLINE[Wars]|'. $this->_db->quote($this->_i18n['NAV_MATCHS']) .'||0|NEWLINE[Irc]|'. $this->_db->quote($this->_i18n['NAV_IRC']) .'||0|NEWLINE[Guestbook]|'. $this->_db->quote($this->_i18n['NAV_GUESTBOOK']) .'||0|NEWLINE[Search]|'. $this->_db->quote($this->_i18n['NAV_SEARCH']) .'||0|NEWLINE|<b>'. $this->_db->quote($this->_i18n['MEMBER']) .'</b>||1|NEWLINE[User]|'. $this->_db->quote($this->_i18n['NAV_ACCOUNT']) .'||1|NEWLINE|<b>'. $this->_db->quote($this->_i18n['ADMIN']) .'</b>||2|NEWLINE[Admin]|'. $this->_db->quote($this->_i18n['NAV_ADMIN']) .'||2|\', \'menu\', 0, \'Tous\'),

    // TODO For Wars block module : Replace `Matches` by `Matchs`
    $sql = 'INSERT INTO `'. BLOCK_TABLE .'` VALUES
        (1, 2, 1, \'\', \''. $this->_db->quote($this->_i18n['BLOCK_LOGIN']) .'\', \'\', \'login\', 0, \'Tous\'),
        (2, 1, 1, \'\', \''. $this->_db->quote($this->_i18n['NAV']) .'\', \'|'. $this->_db->quote($this->_i18n['NAV_CONTENT']) .'||0|NEWLINE[News]|'. $this->_db->quote($this->_i18n['NAV_NEWS']) .'||0|NEWLINE[Archives]|'. $this->_db->quote($this->_i18n['NAV_ARCHIV']) .'||0|NEWLINE[Sections]|'. $this->_db->quote($this->_i18n['NAV_ART']) .'||0|NEWLINE[Calendar]|'. $this->_db->quote($this->_i18n['NAV_CALENDAR']) .'||0|NEWLINE[Stats]|'. $this->_db->quote($this->_i18n['NAV_STATS']) .'||0|NEWLINE|'. $this->_db->quote($this->_i18n['NAV_COMMUNITY']) .'||0|NEWLINE[Forum]|'. $this->_db->quote($this->_i18n['NAV_FORUM']) .'||0|NEWLINE[Guestbook]|'. $this->_db->quote($this->_i18n['NAV_GUESTBOOK']) .'||0|NEWLINE[Irc]|'. $this->_db->quote($this->_i18n['NAV_IRC']) .'||0|NEWLINE[Members]|'. $this->_db->quote($this->_i18n['NAV_MEMBERS']) .'||0|NEWLINE[Contact]|'. $this->_db->quote($this->_i18n['NAV_CONTACT_US']) .'||0|NEWLINE|'. $this->_db->quote($this->_i18n['NAV_MEDIAS']) .'||0|NEWLINE[Download]|'. $this->_db->quote($this->_i18n['NAV_DOWNLOAD']) .'||0|NEWLINE[Gallery]|'. $this->_db->quote($this->_i18n['NAV_GALLERY']) .'||0|NEWLINE[Links]|'. $this->_db->quote($this->_i18n['NAV_LINKS']) .'||0|NEWLINE|'. $this->_db->quote($this->_i18n['NAV_GAMES']) .'||0|NEWLINE[Team]|'. $this->_db->quote($this->_i18n['NAV_TEAM']) .'||0|NEWLINE[Defy]|'. $this->_db->quote($this->_i18n['NAV_DEFY']) .'||0|NEWLINE[Recruit]|'. $this->_db->quote($this->_i18n['NAV_RECRUIT']) .'||0|NEWLINE[Server]|'. $this->_db->quote($this->_i18n['NAV_SERVER']) .'||0|NEWLINE[Wars]|'. $this->_db->quote($this->_i18n['NAV_MATCHS']) .'||0|NEWLINE|'. $this->_db->quote($this->_i18n['MEMBER']) .'||1|NEWLINE[User]|'. $this->_db->quote($this->_i18n['NAV_ACCOUNT']) .'||1|NEWLINE[Admin]|'. $this->_db->quote($this->_i18n['NAV_ADMIN']) .'||2|\', \'menu\', 0, \'Tous\'),
        (3, 1, 2, \'Search\', \''. $this->_db->quote($this->_i18n['BLOCK_SEARCH']) .'\', \'\', \'module\', 0, \'Tous\'),
        (4, 2, 2, \'\', \''. $this->_db->quote($this->_i18n['POLL']) .'\', \'\', \'survey\', 0, \'Tous\'),
        (5, 2, 3, \'Wars\', \''. $this->_db->quote($this->_i18n['NAV_MATCHS']) .'\', \'\', \'module\', 0, \'Tous\'),
        (6, 1, 3, \'Stats\', \''. $this->_db->quote($this->_i18n['BLOCK_STATS']) .'\', \'\', \'module\', 0, \'Tous\'),
        (7, 0, 0, \'Irc\', \''. $this->_db->quote($this->_i18n['IRC_AWARD']) .'\', \'\', \'module\', 0, \'Tous\'),
        (8, 0, 0, \'Server\', \''. $this->_db->quote($this->_i18n['SERVER_MONITOR']) .'\', \'\', \'module\', 0, \'Tous\'),
        (9, 0, 0, \'\', \''. $this->_db->quote($this->_i18n['SUGGEST']) .'\', \'\', \'suggest\', 1, \'Tous\'),
        (10, 0, 0, \'Textbox\', \''. $this->_db->quote($this->_i18n['BLOCK_SHOUTBOX']) .'\', \'\', \'module\', 0, \'Tous\'),
        (11, 1, 4, \'\', \''. $this->_db->quote($this->_i18n['BLOCK_PARTNERS']) .'\', \'<div style="text-align: center;padding: 10px;"><a href="https://nuked-klan.fr" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/ban.png" alt="" title="Nuked-klaN CMS" /></a></div><div style="text-align: center;padding: 10px;"><a href="http://www.nitroserv.fr" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/nitroserv.png" alt="" title="'. $this->_db->quote($this->_i18n['GAME_SERVER_RENTING']) .'" /></a></div>\', \'html\', 0, \'Tous\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.5
    if (version_compare($this->_session['version'], '1.7.5', '<=')) {
        $sql = 'INSERT INTO `'. BLOCK_TABLE .'`
            (active, position, module, titre, content, type, nivo, page) VALUES
            (1, 4, \'\', \''. $this->_db->quote($this->_i18n['BLOCK_PARTNERS']) .'\', \'<div style="text-align: center;padding: 10px;"><a href="https://nuked-klan.fr" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/ban.png" alt="" title="Nuked-klaN CMS" /></a></div><div style="text-align: center;padding: 10px;"><a href="http://www.nitroserv.fr" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/nitroserv.png" alt="" title="'. $this->_db->quote($this->_i18n['GAME_SERVER_RENTING']) .'" /></a></div>\', \'html\', 0, \'Tous\');';

        $dbTable->insertData(array('INSERT_BLOCK', $this->_i18n['BLOCK_PARTNERS']), $sql);
    }

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'content');
    }

    $dbTable->applyUpdateFieldListToData();
}

?>
