<?php
/**
 * table.style.u.php
 *
 * `[PREFIX]_style` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_style');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table removal
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.9 RC1 (created)
    // update 1.7.9 RC6 (removed)
    if ($dbTable->tableExist())
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_DROP');
}

?>
