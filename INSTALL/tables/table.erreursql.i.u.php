<?php
/**
 * table.erreursql.i.u.php
 *
 * `[PREFIX]_erreursql` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_erreursql');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$sqlErrorTableCfg = array(
    'fields' => array(
        'id'    => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'date'  => array('type' => 'varchar(30)', 'null' => false, 'default' => '\'0\''),
        'url'   => array('type' => 'text',        'null' => false),
        'error' => array('type' => 'text',        'null' => false),
        'code'  => array('type' => 'smallint(5)', 'null' => false, 'unsigned' => true),
        'line'  => array('type' => 'smallint(5)', 'null' => false, 'unsigned' => true),
        'file'  => array('type' => 'text',        'null' => false)
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

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

// install / update 1.7.9 RC1
$sqlErrorTableCreated = false;

if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $dbTable->createTable($sqlErrorTableCfg);

    $sqlErrorTableCreated = true;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    if ($sqlErrorTableCreated)
        return;

    // install / update 1.8
    if ($dbTable->fieldExist('lien'))
        $dbTable->modifyField('lien', array_merge(array('newField' => 'url'), $sqlErrorTableCfg['fields']['url']));

    if ($dbTable->fieldExist('texte'))
        $dbTable->modifyField('texte', array_merge(array('newField' => 'error'), $sqlErrorTableCfg['fields']['error']));

    if (! $dbTable->fieldExist('code'))
        $dbTable->addField('code', $sqlErrorTableCfg['fields']['code']);

    if (! $dbTable->fieldExist('line'))
        $dbTable->addField('line', $sqlErrorTableCfg['fields']['line']);

    if (! $dbTable->fieldExist('file'))
        $dbTable->addField('file', $sqlErrorTableCfg['fields']['file']);
}

?>