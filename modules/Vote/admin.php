<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die('<div style="text-align:center;">You cannot open this page directly</div>');

global $user, $language, $visiteur;

translate('modules/Vote/lang/'. $language .'.lang.php');

include 'modules/Admin/design.php';
admintop();

$level_admin = admin_mod('Vote');

if ($visiteur >= $level_admin && $level_admin > -1) {

    function module_vote() {
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINVOTE . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Vote.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b>". _VOTEMOD . "</b></div><br />\n"
            . "<form method=\"post\" action=\"index.php?file=Vote&amp;page=admin&amp;op=modify_module_vote\">\n"
            . "<div>". _LISTI ."</div>\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">";

        $sql = mysql_query("SELECT module, active FROM " . VOTE_MODULES_TABLE);

        while(list($module, $active) = mysql_fetch_array($sql)) {
        ?>
            <tr><td><b><?php echo $module; ?>:</b></td><td>
        <?php if ( $active == 0) {
        ?>
                <select name="<?php echo $module; ?>">
                    <option value="0"><?php echo _DESACTIVER; ?></option>
                    <option value="1"><?php echo _ACTIVER; ?></option>
                </select></td></tr>
        <?php
            }
            else {
        ?>
                <select name="<?php echo $module; ?>">
                    <option value="1"><?php echo _ACTIVER; ?></option>
                    <option value="0"><?php echo _DESACTIVER; ?></option>
                </select></td></tr>
        <?php
            }
        }

        echo "<tr><td align=\"center\" colspan=\"2\">\n"
            . "<br /><input type=\"submit\" name=\"send\" value=\"" . _MODIF . "\" /></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br />[ <a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function modify_module_vote() {
        global $nuked, $user;

        mysql_query("UPDATE ". VOTE_MODULES_TABLE ." SET active = '". $_REQUEST['download'] ."' WHERE module = 'download'");
        mysql_query("UPDATE ". VOTE_MODULES_TABLE ." SET active = '". $_REQUEST['sections'] ."' WHERE module = 'sections'");
        mysql_query("UPDATE ". VOTE_MODULES_TABLE ." SET active = '". $_REQUEST['links'] ."' WHERE module = 'links'");
        mysql_query("UPDATE ". VOTE_MODULES_TABLE ." SET active = '". $_REQUEST['gallery'] ."' WHERE module = 'gallery'");

        // Action
        mysql_query(
            "INSERT INTO ". $nuked['prefix'] ."_action
            (`date`, `pseudo`, `action`)
            VALUES
            ('". time() ."', '". $user[0] ."', '". _ACTIONMODIFVOTEMOD .".')"
        );
        //Fin action

        echo "<div class=\"notification success png_bg\">\n"
            . "<div>\n"
            . _VOTEMODIFMOD . "\n"
            . "</div>\n"
            . "</div>\n";

        redirect('index.php?file=Vote&page=admin&op=module_vote', 2);
    }


    switch ($_REQUEST['op']) {
        case 'modify_module_vote' :
            modify_module_vote();
            break;

        default :
            module_vote();
            break;
    }
}
else if ($level_admin == -1) {
    echo "<div class=\"notification error png_bg\">\n"
        . "<div>\n"
        . "<br /><br /><div style=\"text-align: center;\">". _MODULEOFF
        . "<br /><br /><a href=\"javascript:history.back()\"><b>". _BACK ."</b></a></div><br /><br />"
        . "</div>\n"
        . "</div>\n";
}
else if ($visiteur > 1) {
    echo "<div class=\"notification error png_bg\">\n"
        . "<div>\n"
        . "<br /><br /><div style=\"text-align: center;\">". _NOENTRANCE
        . "<br /><br /><a href=\"javascript:history.back()\"><b>". _BACK ."</b></a></div><br /><br />"
        . "</div>\n"
        . "</div>\n";
}
else {
    echo "<div class=\"notification error png_bg\">\n"
        . "<div>\n"
        . "<br /><br /><div style=\"text-align: center;\">". _ZONEADMIN
        . "<br /><br /><a href=\"javascript:history.back()\"><b>". _BACK ."</b></a></div><br /><br />"
        . "</div>\n"
        . "</div>\n";
}

adminfoot();

?>