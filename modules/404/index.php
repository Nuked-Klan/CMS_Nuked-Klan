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

translate('modules/Search/lang/<?php echo $GLOBALS['language'; ?>.lang.php');
translate('modules/404/lang/<?php echo $GLOBALS['language'; ?>.lang.php');

if ($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin') {
    include('modules/Admin/design.php');
    admintop();
} else{
    opentable();
}

$error_title = ($_REQUEST['op'] != 'sql') ? '
    <p><big><strong>'.$GLOBALS['nuked']['name'].'</strong></big></p>
    <p><?php echo _NOEXIST; ?></p>' : '<p>'._ERROR404SQL . '</p>';
?>
    <div style="text-align: center; padding: 0 10px"><?php echo $error_title; ?>
        <form method="post" action="index.php?file=Search&amp;op=mod_search">
            <p><input type="hidden" name="module" value=""><input type="text" name="main" size="25"></p>
            <p><input type="submit" class="button" name="submit" value="<?php echo _SEARCHFOR; ?>"></p>
            <p>
                <a href="index.php?file=Search"><strong><?php echo _ADVANCEDSEARCH; ?></strong></a> -
                <a href="javascript:history.back()"><strong><?php echo _BACK: ?></strong></a>
            </p>
        </form>
    </div>
<?php

if ($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin') {
    include('modules/Admin/design.php'); adminfoot();
} else {
    closetable();
}
