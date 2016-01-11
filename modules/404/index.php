<?php
/**
 * index.php
 *
 * Frontend / Backend 404 error page
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $nuked, $language;

translate('modules/Search/lang/'. $language .'.lang.php');
translate('modules/404/lang/'. $language .'.lang.php');

if ($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin') {
    require_once 'modules/Admin/includes/core.php';
    nkTemplate_setInterface('backend');
    adminDesignInit();
    translate('modules/Admin/lang/'. $language .'.lang.php');
}
else {
    nkTemplate_setInterface('frontend');
    nkTemplate_init('404');
    opentable();
}

if ($_REQUEST['op'] != 'sql')
    $error_title = '<big><b>' . $nuked['name'] . '</b></big><br /><br />' . _NOEXIST . '<br /><br />';
else
    $error_title = _ERROR404SQL . '<br /><br />';

echo '<div style="text-align: center; padding: 0 10px">' . $error_title . '
          <form method="post" action="index.php?file=Search&amp;op=mod_search">
              <p><input type="hidden" name="module" value="" /><input type="text" name="main" size="25" /></p>
              <p><input type="submit" class="button" name="submit" value="' . _SEARCHFOR . '" /></p>
              <p><a href="index.php?file=Search"><b>' . _ADVANCEDSEARCH . '</b></a> - <a href="javascript:history.back()"><b>' . __('BACK') . '</b></a></p>
		  </form>
	  </div>';

if ($_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin')
    closetable();


?>