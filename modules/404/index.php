<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $nuked, $language;
translate('modules/Search/lang/' . $language . '.lang.php');
translate('modules/404/lang/' . $language . '.lang.php');

if($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin'){
	include('modules/Admin/design.php');
	admintop();
}else{
	opentable();
}

$error_title = ($_REQUEST['op'] != 'sql') ? '<big><b>' . $nuked['name'] . '</b></big><br /><br />' . _NOEXIST . '<br /><br />' : _ERROR404SQL . '<br /><br />';

echo '<div style="text-align: center; padding: 0 10px">' . $error_title . '
          <form method="post" action="index.php?file=Search&amp;op=mod_search">
              <p><input type="hidden" name="module" value="" /><input type="text" name="main" size="25" /></p>
              <p><input type="submit" class="button" name="submit" value="' . _SEARCHFOR . '" /></p>
              <p><a href="index.php?file=Search"><b>' . _ADVANCEDSEARCH . '</b></a> - <a href="javascript:history.back()"><b>' . _BACK . '</b></a></p>
		  </form>
	  </div>';

if($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin'){
	include('modules/Admin/design.php');
	adminfoot();
}else{
	closetable();
}

?>
