<?php
/**
 * table._forums_rank.c.i.php
 *
 * `[PREFIX]_forums_rank` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums_rank');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$forumRankTableCfg = array(
    'fields' => array(
        'id'    => array('type' => 'int(10)',      'null' => false, 'autoIncrement' => true),
        'nom'   => array('type' => 'varchar(100)', 'null' => false, 'default' => '\'\''),
        'type'  => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'post'  => array('type' => 'int(4)',       'null' => false, 'default' => '\'0\''),
        'image' => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\'')
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of forum rank database table
 */
function updateForumRankRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('UPDATE_RANK_IMG', $updateList)) {
        if (array_key_exists($row['image'], $vars['oldRankImgList']) && is_file($row['image']) && is_readable($row['image'])) {
            $md5sum = md5_file($row['image']);

            if ($md5sum == $vars['oldRankImgList'][$row['image']])
                $setFields['image'] = substr($row['image'], 0, strrpos($row['image'], '.')) .'.png';
        }
    }

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table exist in 1.6.x version
    $dbTable->checkIntegrity();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop')
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->createTable($forumRankTableCfg);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_forums_rank` VALUES
        (1, \''. $this->_db->quote($this->_i18n['NEWBIE']) .'\', 0, 0, \'modules/Forum/images/rank/star1.png\'),
        (2, \''. $this->_db->quote($this->_i18n['JUNIOR_MEMBER']) .'\', 0, 10, \'modules/Forum/images/rank/star2.png\'),
        (3, \''. $this->_db->quote($this->_i18n['MEMBER']) .'\', 0, 100, \'modules/Forum/images/rank/star3.png\'),
        (4, \''. $this->_db->quote($this->_i18n['SENIOR_MEMBER']) .'\', 0, 500, \'modules/Forum/images/rank/star4.png\'),
        (5, \''. $this->_db->quote($this->_i18n['POSTING_FREAK']) .'\', 0, 1000, \'modules/Forum/images/rank/star5.png\'),
        (6, \''. $this->_db->quote($this->_i18n['MODERATOR']) .'\', 1, 0, \'modules/Forum/images/rank/mod.png\'),
        (7, \''. $this->_db->quote($this->_i18n['ADMINISTRATOR']) .'\', 2, 0, \'modules/Forum/images/rank/mod.png\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // Update rank image (since 1.8)
    if (version_compare($this->_session['version'], '1.8', '<')) {
        // Old rank image
        $oldRankImgList = array(
            'modules/Forum/images/rank/star1.gif' => '203f080c343d40d2bf046a5decca951b',
            'modules/Forum/images/rank/star2.gif' => '6bdc6f995ba8bc76848e5eab5309727e',
            'modules/Forum/images/rank/star3.gif' => '322d4dba63c8f56a9673c00a0bfc19f2',
            'modules/Forum/images/rank/star4.gif' => '15186dbb474fbdefc95a1f55f0ada5a4',
            'modules/Forum/images/rank/star5.gif' => 'dd0afc847a20211a75b5e0c295166680',
            'modules/Forum/images/rank/mod.gif'   => '0b26f8f2cc952e048defab650dab8e18'
        );

        $dbTable->setCallbackFunctionVars(array('oldRankImgList' => $$oldRankImgList))
            ->setUpdateFieldData('UPDATE_RANK_IMG', 'image');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateForumRankRow');
}

?>