<?php
/**
 * table.users.c.i.u.php
 *
 * `[PREFIX]_users` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(USER_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$usersTableCfg = array(
    'fields' => array(
        'id'          => array('type' => 'varchar(20)',  'null' => false, 'default' => '\'\''),
        'team'        => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'team2'       => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'team3'       => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'rang'        => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'ordre'       => array('type' => 'int(5)',       'null' => false, 'default' => '\'0\''),
        'pseudo'      => array('type' => 'varchar(30)',  'null' => false),
        'mail'        => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'email'       => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'icq'         => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'msn'         => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'aim'         => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'yim'         => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'xfire'       => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'facebook'    => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'origin'      => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'steam'       => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'twitter'     => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'skype'       => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'url'         => array('type' => 'varchar(150)', 'null' => false, 'default' => '\'\''),
        'pass'        => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'niveau'      => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'date'        => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\''),
        'avatar'      => array('type' => 'varchar(100)', 'null' => false, 'default' => '\'\''),
        'signature'   => array('type' => 'text',         'null' => true),
        'user_theme'  => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\''),
        'user_langue' => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\''),
        'game'        => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'country'     => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'count'       => array('type' => 'int(10)',      'null' => false, 'default' => '\'0\''),
        'erreur'      => array('type' => 'INT(10)',      'null' => false, 'default' => '\'0\''),
        'token'       => array('type' => 'varchar(13)',  'null' => true,  'default' => 'NULL'),
        'token_time'  => array('type' => 'varchar(10)',  'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'team'   => 'team',
        'team2'  => 'team2',
        'team3'  => 'team3',
        'rang'   => 'rang',
        'game'   => 'game',
        'pseudo' => 'pseudo'
    ),
    'engine' => 'InnoDB'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of users database table
 */
function updateUsersDbTableRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('UPDATE_PASSWORD', $updateList))
        $setFields['pass'] = hash::apply($vars['HASHKEY'], $row['pass']);

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['signature'] = $vars['bbcode']->apply(stripslashes($row['signature']));

    if (in_array('UPDATE_COUNTRY', $updateList)) {
        if ($row['country'] == 'czech.gif')
            $setFields['pass'] = 'Czech.gif';
    }

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'pass', 'signature');
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

if ($process == 'install')
    $dbTable->createTable($usersTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install 1.7.9 RC3 - update 1.7.9 RC1
    if (! $dbTable->fieldExist('erreur'))
        $dbTable->addField('erreur', $usersTableCfg['fields']['erreur']);

    // install / update 1.7.9
    if (! $dbTable->fieldExist('token')) {
        $dbTable->addField('token', $usersTableCfg['fields']['token'])
            ->addField('token_time', $usersTableCfg['fields']['token_time']);
    }

    // install / update 1.8
    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine(USER_TABLE) == 'MyISAM')
        $this->_db->execute('ALTER TABLE `'. USER_TABLE .'` ENGINE=InnoDB;');

    if ($dbTable->fieldExist('pseudo')) {
        if ($dbTable->getFieldType('pseudo') != 'varchar(30)')
            $dbTable->modifyField('pseudo', $usersTableCfg['fields']['pseudo']);

        if (! $dbTable->checkFieldIsIndex('pseudo'))
            $dbTable->addFieldIndex('pseudo');
    }

    if (! $dbTable->fieldExist('xfire'))
        $dbTable->addField('xfire', $usersTableCfg['fields']['xfire']);

    if (! $dbTable->fieldExist('facebook'))
        $dbTable->addField('facebook', $usersTableCfg['fields']['facebook']);

    if (! $dbTable->fieldExist('origin'))
        $dbTable->addField('origin', $usersTableCfg['fields']['origin']);

    if (! $dbTable->fieldExist('steam'))
        $dbTable->addField('steam', $usersTableCfg['fields']['steam']);

    if (! $dbTable->fieldExist('twitter'))
        $dbTable->addField('twitter', $usersTableCfg['fields']['twitter']);

    if (! $dbTable->fieldExist('skype'))
        $dbTable->addField('skype', $usersTableCfg['fields']['skype']);

    if ($dbTable->fieldExist('signature') && ! $dbTable->checkFieldIsNull('signature'))
        $dbTable->modifyField('signature', $usersTableCfg['fields']['signature']);

    if (! isset($this->_session['updateUserPassword'])) {
        $sql = 'SELECT pass
            FROM `'. USER_TABLE .'`
            ORDER BY RAND()
            LIMIT 1';

        $dbsUsers = $this->_db->selectOne($sql);
        $firstChr = substr($dbsUsers['pass'], 0, 1);

        if ($firstChr != '%' && $firstChr != '#')
            $this->_session['updateUserPassword'] = true;
        else
            $this->_session['updateUserPassword'] = false;
    }

    if ($this->_session['updateUserPassword']) {
        $dbTable->setCallbackFunctionVars(array('HASHKEY' => $this->_session['HASHKEY']))
            ->setUpdateFieldData('UPDATE_PASSWORD', 'pass');
    }

    // update 1.7.9 RC3
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'signature');
    }

    if (version_compare($this->_session['version'], '1.8', '<')) {
        $dbTable->setUpdateFieldData('UPDATE_COUNTRY', 'country');
    }

    $dbTable->applyUpdateFieldListToData();
}

?>