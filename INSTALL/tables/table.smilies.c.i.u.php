<?php
/**
 * table.smilies.c.i.u.php
 *
 * `[PREFIX]_smilies` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_smilies');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'code');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_smilies` (
            `id` int(5) NOT NULL auto_increment,
            `code` varchar(50) NOT NULL default \'\',
            `url` varchar(100) NOT NULL default \'\',
            `name` varchar(100) NOT NULL default \'\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    // TODO : Traduction ?
    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_smilies` VALUES
        (1, \':D\', \'biggrin.gif\', \'Very Happy\'),
        (2, \':)\', \'smile.gif\', \'Smile\'),
        (3, \':(\', \'frown.gif\', \'Sad\'),
        (4, \':o\', \'eek.gif\', \'Surprised\'),
        (5, \':?\', \'confused.gif\', \'Confused\'),
        (6, \'8)\', \'cool.gif\', \'Cool\'),
        (7, \':P\', \'tongue.gif\', \'Razz\'),
        (8, \':x\', \'mad.gif\', \'Mad\'),
        (9, \';)\', \'wink.gif\', \'Wink\'),
        (10, \':red:\', \'redface.gif\', \'Embarassed\'),
        (11, \':roll:\', \'rolleyes.gif\', \'Rolling Eyes\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // TODO Preciser la version
    // update 1.7.9
    $sql = 'SELECT id, code
        FROM `'. $this->_session['db_prefix'] .'_smilies`
        WHERE code LIKE \'%\\\'%\'';

    $dbsSmilies = $this->_db->selectMany($sql);

    $i = 0;

    foreach ($dbsSmilies as $row) {
        $i++;
        $sql = 'UPDATE `'. $this->_session['db_prefix'] .'_smilies`
            SET code = \'#smiley'. $i .'#\'
            WHERE id = \''. $row['id'] .'\'';

        $dbTable->updateData(array('UPDATE_SMILIES', addslashes($row['code'])), $sql);
    }
}

?>