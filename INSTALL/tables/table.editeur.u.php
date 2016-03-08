<?php
/**
 * table.editeur.u.php
 *
 * `[PREFIX]_editeur` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_editeur');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table removal
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install 1.7.9 RC1 (created)
    // install 1.7.9 RC6 (removed)
    if ($dbTable->tableExist())
        $dbTable->dropTable();
    else
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_DROP');
}

?>