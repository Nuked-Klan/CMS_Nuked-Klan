<?php
/**
 * index.php
 *
 * Frontend / Backend 404 error page
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $op, $language;

translate('modules/404/lang/'. $language .'.lang.php');


$interface = nkTemplate_getInterface();

if ($interface == 'backend') {
    require_once 'modules/Admin/includes/core.php';
    translate('modules/Admin/lang/'. $language .'.lang.php');
}
else {
    nkTemplate_moduleInit('404');
    opentable();
}

echo applyTemplate('modules/404/index');

if ($interface == 'frontend')
    closetable();

?>
