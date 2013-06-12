<?php
/**
*   [Group Management]
*
*   @version 1.8
*   @link http://www.nuked-klan.org Clan Management System 4 Gamers NK CMS
*   @license http://opensource.org/licenses/gpl-license.php GNU Public License
*   @copyright 2001-2013 Nuked Klan
*/

if (!defined("INDEX_CHECK")) {
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

translate("modules/Admin/lang/" . $GLOBALS['language'] . ".lang.php");
include("modules/Admin/design.php");

$nkAccessModule = nkAccessModule('Admin', $GLOBALS['user']['1'], TRUE);

admintop();

if($nkAccessModule === TRUE) {

    function mainGroup() {
        $dbsGroup = ' SELECT id, nameGroup, description
                      FROM '.GROUP_TABLE.' ';
        $dbeGroup = mysql_query($dbsGroup);
?>
        <script type="text/javascript">
        <!--
            function delgroup(name, id) {
                if (confirm("<?php echo _FUNCDELGROUP; ?> " + name +" ! <?php echo _CONFIRM; ?>")) {
                    document.location.href = "index.php?file=Admin&page=group&op=del_group&id="+id;
                }
            }
        // -->
        </script>

            <div class="content-box">
                <div class="content-box-header"><h3><?php echo _USERADMINGROUP; ?></h3>
                    <div style="text-align:right;">
                        <a href="help" . $language . "/user.php" rel="modal">
                            <img src="help/help.gif" alt="" title="" . _HELP . "" />
                        </a>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div class="margin10Center">
                        <?php echo _TITLEGESTGROUP; ?><strong> |
                        <a href="index.php?file=Admin&amp;page=group&amp;op=addGroup"><?php echo _MENUADDGROUP; ?></a></strong>
                    </div>

                    <table width="100%" border="0" cellspacing="1" cellpadding="2">
                        <tr>
                            <td class="bold tableWidth30"><?php echo _TITLEWORDING; ?></td>
                            <td class="bold tableWidth30"><?php echo _TITLEDESCGROUP; ?></td>
                            <td class="bold tableWidth20"><?php echo _NBUTILISATEURS; ?></td>
                            <td class="bold tableWidth10"><?php echo _EDIT; ?></td>
                            <td class="bold tableWidth10"><?php echo _DELETE; ?></td>
                        </tr>
<?php
                        while(list($id, $nameGroup, $description) = mysql_fetch_array($dbeGroup)) {

                            $dbsUserCount = ' SELECT id
                                              FROM ' . USER_TABLE . '
                                              WHERE GroupMain = "' . $id . '"';
                            $dbeUserCount = mysql_query($dbsUserCount);
                            $dbcUserCount = mysql_num_rows($dbeUserCount);

                            if(!$description) {
                                $description = 'Aucune description';
                            }

                            if ($id == 0 or $id == 1 or $id == 2) {
                                $linkDel = "<img src=\"modules/Admin/images/stop.png\" alt=\""._DELTHISGROUP."\" title=\""._DELETE."\" />";
                            }
                            else {
                                $linkDel = "
                                <a href=\"javascript:delgroup('".mysql_real_escape_string($nameGroup). "', '" .$id."')\">
                                    <img src=\"images/del.gif\" alt=\""._DELTHISGROUP."\" title=\""._DELETE."\" />
                                </a>";
                            }

                            if ($id == 0) {
                                $linkEdit = "<img src=\"modules/Admin/images/stop.png\" alt=\""._EDITTHISGROUP."\" title=\""._EDIT."\" />";
                            }
                            else {
                                $linkEdit = "
                                <a href=\"index.php?file=Admin&amp;page=group&amp;op=editGroup&amp;id=".$id."\">
                                    <img src=\"images/edit.gif\" alt=\""._EDITTHISGROUP."\" title=\""._EDIT."\" />
                                </a>";
                            }
?>
                        <tr>
                            <td><?php echo translateGroupName($id, $nameGroup); ?></td>
                            <td><?php echo translateGroupName($id, $description); ?></td>
                            <td># <?php echo $dbcUserCount; ?></td>
                            <td style="<?php echo $center; ?>">
                                <?php echo $linkEdit; ?>
                            </td>
                            <td style="<?php echo $center; ?>">
                                <?php echo $linkDel; ?>
                            </td>
                        </tr>
<?php
                            }
?>
                    </table>
                </div>
                <div class="boldCenter margin10Center"><a href="index.php?file=Admin&amp;page=group"><?php echo _BACK; ?></a></div>
            </div>
<?php
    }

    function addGroup() {
        $dbsModule = ' SELECT id, nom
                       FROM ' . MODULES_TABLE . '
                       WHERE niveau != "-1"
                       AND admin != "-1"';
        $dbeModule = mysql_query($dbsModule);
?>
            <div class="content-box">
                <div class="content-box-header"><h3><?php echo _USERADMINGROUP; ?></h3>
                    <div style="text-align:right;">
                        <a href="help<?php echo $language; ?>/group.php" rel="modal">
                            <img src="help/help.gif" alt="" title="<?php echo _HELP; ?>" />
                        </a>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div class="margin10Center"><strong>
                        <a href="index.php?file=Admin&amp;page=group&amp;op=mainGroup"><?php echo _TITLEGESTGROUP; ?></a> |
                            </strong><?php echo _MENUADDGROUP; ?>
                    </div>
                    <form method="post" action="index.php?file=Admin&amp;page=group&amp;op=sendGroupAdd">
                        <div id="userGroup">
                            <div class="three">
                                <span><?php echo _NAMEGROUP; ?></span>
                                <input class="tableWidth50" type="text" name="name" required="required" />
                            </div>
                            <div class="three">
                                <span><?php echo _DESCGROUP; ?></span>
                                <input class="tableWidth50" type="text" name="description" required="required" />
                            </div>
                            <div class="three" id="colorPickerJquery">
                                <span><?php echo _COLOR; ?></span>
                                <input class="tableWidth50" type="text" name="color" id="colorPickerGroup" value="#000000" />
                                <div id="picker"></div>
                            </div>
                            <div class="full"><span><?php echo _LISTMODULE; ?></span></div>
<?php
                            while(list($id, $name) = mysql_fetch_array($dbeModule)) {
?>
                            <div class="quarter">
                                <span><?php echo $name; ?></span>
                                <div class="tableWidth90 nKcenter">
                                    <span class="title"><?php echo _ACCESMODULE; ?></span>
                                    <span class="input"><input name="a<?php echo $id; ?>" type="checkbox" /></span>
                                </div>
                                <div class="tableWidth90 nKcenter">
                                    <span class="title"><?php echo _ACCESADMIN; ?></span>
                                    <span class="input"><input name="b<?php echo $id; ?>" type="checkbox" /></span>
                                </div>
                            </div>
<?php
                            }
?>
                            <div class="full"><span><input type="submit" value="<?php echo _ADD; ?>" /></span></div>
                        </div>
                    </form>
                </div>
                <div class="boldCenter margin10Center"><a href="index.php?file=Admin&amp;page=group"><?php echo _BACK; ?></a></div>
            </div>
<?php
    }

    function sendGroupAdd() {

        $arrayRequest = array('name', 'description', 'color');
        foreach($arrayRequest as $key){
            if(array_key_exists($key, $_REQUEST)){
                ${$key} = $_REQUEST[$key];
            }
            else{
                ${$key} = '';
            }
        }

        $dbsTestName = ' SELECT id, count(id) AS count
                         FROM '.GROUP_TABLE.'
                         WHERE nameGroup = "' . $name . '"';
        $dbeTestName = mysql_query($dbsTestName);
        $testName = mysql_fetch_assoc($dbeTestName);

        if($testName['count'] == 0) {

            $dbsModule = ' SELECT id, nom
                           FROM ' . MODULES_TABLE . '
                           WHERE niveau != "-1"
                           AND admin != "-1"';
            $dbeModule = mysql_query($dbsModule);

            $i = 0;
            $j = 0;

            $groupForUser  = '';
            $groupForAdmin = '';

            while(list($idGroup, $moduleName) = mysql_fetch_array($dbeModule)) {
                if (array_key_exists('a'.$idGroup, $_POST)) {
                    if ($_POST['a'.$idGroup]) {
                        if($i > 0) {
                            $groupForUser .= '|';
                        }
                        $groupForUser .= $moduleName;
                        $i++;
                    }
                }
                if (array_key_exists('b'.$idGroup, $_POST)) {
                    if ($_POST['b'.$idGroup]) {
                        if($j > 0) {
                            $groupForAdmin .= '|';
                        }
                        $groupForAdmin .= $moduleName;
                        $j++;
                    }
                }
            }

            if (!is_string($description)) {
                $description = _DESCINVALID;
?>
                <div class="notification error png_bg">
                    <div><?php echo _DESCINVALID; ?></div>
                </div>
<?php
            }

            $dbiInsertGroup = 'INSERT INTO '.GROUP_TABLE.' (`id` , `nameGroup` , `access`, `accessAdmin`, `description`, `color` )
                               VALUES ("", "' . $name . '" , "' . $groupForUser . '", "' . $groupForAdmin . '", "' . $description . '", "' . $color . '")';
            $dbeInsertGroup = mysql_query($dbiInsertGroup);

            debug($dbiInsertGroup);

            $texteaction = _ACTIONGROUPADD . $name;
            $acdate = time();

            $dbiAction = 'INSERT INTO '.ACTION_TABLE.' (`date`, `pseudo`, `action`)
                          VALUES("' . $acdate . '", "' . $GLOBALS['user']['0'] . '", "' . $texteaction . '")';
            $dbeAction = mysql_query($dbiAction);
?>
            <div class="notification success png_bg">
                <div><?php echo _SUCCESGROUPADD; ?></div>
            </div>
<?php
        }
        else {
?>
            <div class="notification error png_bg">
                <div><?php echo _ERRORGROUPEXIST; ?></div>
            </div>
<?php
        }
    //redirect("index.php?file=Admin&page=group", 2);
    }

    function editGroup($id) {
        $dbsGroup = ' SELECT nameGroup, access, accessAdmin, description, color
                      FROM '.GROUP_TABLE.'
                      WHERE id = "' . $id . '"';
        $dbeGroup = mysql_query($dbsGroup);
        list($nameGroup, $access, $accessAdmin, $description, $color) = mysql_fetch_array($dbeGroup);

        $access = explode('|', $access);
        $accessAdmin = explode('|', $accessAdmin);

		if(!$description){
			$description = 'Aucune description';
		}

        if ($nameGroup == "_ADMINISTRATOR") {
            $disabledName = 'readonly="readonly"';
            $disabledAccess = 'disabled="disabled"';
            $disabledAdmin = 'disabled="disabled"';
        } else if ($nameGroup == "_MEMBERS") {
            $disabledName = 'readonly="readonly"';
            $disabledAccess = '';
            $disabledAdmin = '';
        } else if ($nameGroup == "_VISITOR") {
            $disabledName = 'readonly="readonly"';
            $disabledAccess = '';
            $disabledAdmin = 'disabled="disabled"';
        } else {
            $nameGroup = $nameGroup;
            $disabledName = '';
            $disabledAccess = '';
            $disabledAdmin = '';
        }
?>
            <div class="content-box">
                <div class="content-box-header"><h3><?php echo _USERADMINGROUP; ?></h3>
                    <div style="text-align:right;">
                        <a href="help" . $language . "/group.php" rel="modal">
                            <img src="help/help.gif" alt="" title="<?php echo _HELP; ?>" />
                        </a>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div class="margin10Center"><strong>
                        <a href="index.php?file=Admin&amp;page=group&amp;op=mainGroup"><?php echo _TITLEGESTGROUP; ?></a> |
                        <a href="index.php?file=Admin&amp;page=group&amp;op=addGroup"><?php echo _MENUADDGROUP; ?></a></strong>
                    </div>
                    <form id="adminGroup" method="post" action="index.php?file=Admin&amp;page=group&amp;op=sendGroupEdit">
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <div id="userGroup">
                            <div class="three">
                                <span><?php echo _NAMEGROUP; ?></span>
                                <input <?php echo $disabledName; ?> class="tableWidth50" type="text" name="name" value="<?php echo translateGroupName($id, $nameGroup); ?>" required="required" />
                            </div>
                            <div class="three">
                                <span><?php echo _DESCGROUP; ?></span>
                                <input <?php echo $disabledName; ?> class="tableWidth50" type="text" name="description" value="<?php echo translateGroupName($id, $description); ?>" required="required" />
                            </div>
                            <div class="three" id="colorPickerJquery">
                                <span><?php echo _COLOR; ?></span>
                                <input class="tableWidth50" type="text" name="color" id="colorPickerGroup" value="<?php echo $color; ?>" />
                                <div id="picker"></div>
                            </div>
                            <div class="full"><span><?php echo _LISTMODULE; ?></span></div>
<?php
                            $dbsModule = ' SELECT id, nom
                                           FROM ' . MODULES_TABLE . '
                                           WHERE niveau != "-1"
                                           AND admin != "-1"';
                            $dbeModule = mysql_query($dbsModule);

                            while(list($id, $name) = mysql_fetch_array($dbeModule)) {
                                if (in_array($name, $access, true)) {
									$checkedAcces = 'checked="checked"';
                                    } else {
                                        $checkedAcces = '';
                                    }
                                if (in_array($name, $accessAdmin, true)) {
									$checkedAdmin = 'checked="checked"';
                                    } else {
                                        $checkedAdmin = '';
                                    }
?>
                            <div class="quarter">
                                <span><?php echo $name; ?></span>
                                <div class="tableWidth90 nKcenter">
                                    <span class="title">Acc&egrave;s module</span>
                                    <span class="input"><input <?php echo $disabledAccess . $checkedAcces; ?> name="a<?php echo $id; ?>" type="checkbox" value="1"></span>
                                </div>
                                <div class="tableWidth90 nKcenter">
                                    <span class="title">Acc&egrave;s Admin </span>
                                    <span class="input"><input <?php echo $disabledAdmin  . $checkedAdmin; ?> name="b<?php echo $id; ?>" type="checkbox" value="1"></span>
                                </div>
                            </div>
<?php
                            }
?>
                            <div class="full"><span><input type="submit" value="<?php echo _EDIT; ?>" /></span></div>
                        </div>
                    </form>
                </div>
                <div class="boldCenter margin10Center"><a href="index.php?file=Admin&amp;page=group"><?php echo _BACK; ?></a></div>
            </div>
<?php
    }

    function sendGroupEdit() {
        $arrayRequest = array('id', 'name', 'description', 'color');
        foreach($arrayRequest as $key){
            if(array_key_exists($key, $_REQUEST)){
                ${$key} = $_REQUEST[$key];
            }
            else{
                ${$key} = '';
            }
        }

        if($name == 'Visiteur' or $name == 'Guest') {
            $name = '_VISITOR';
        }
        else if($name == 'Membres' or $name == 'Members') {
            $name = '_MEMBERS';
        }

        $dbsGroupName = ' SELECT id , count(id) AS count
                          FROM '.GROUP_TABLE.'
                          WHERE nameGroup = "' . $name . '"';
        $dbeGroupName = mysql_query($dbsGroupName);
        $group = mysql_fetch_assoc($dbeGroupName);

        if ($group['id'] == $id && $group['count'] == 1 && $id != 0) {

            $dbsModule = 'SELECT id, nom
                          FROM ' . MODULES_TABLE . '
                          WHERE niveau != "-1"
                          AND admin != "-1"';
            $dbeModule = mysql_query($dbsModule);
            $i = 0;
            $j = 0;
            $groupForUser = $groupForAdmin = '';
            while(list($idGroup, $moduleName) = mysql_fetch_array($dbeModule)) {
                if (array_key_exists('a'.$idGroup, $_POST)) {
                    if($i > 0) {
                        $groupForUser .= '|';
                    }
                    $groupForUser .= $moduleName;
                    $i++;
                }
                if (array_key_exists('b'.$idGroup, $_POST)) {
                    if($j > 0) {
                        $groupForAdmin .= '|';
                    }
                    $groupForAdmin .= $moduleName;
                    $j++;
                }
            }

            $fields = '';

            if($id != 0) {
                $fields .= ' access = "'.$groupForUser.'" ';
            }

            if ($id != 0 && $id != 2) {
                $fields .= ' , accessAdmin = "'.$groupForAdmin.'" ';
            }

            if($id != 0 && $id != 1 && $id != 2) {
                $fields .= ', nameGroup = "'.$name.'", description = "'.$description.'" ';
            }

            if(!empty($fields)){
                $fields .= ' , ';
            }
            $fields .= 'color = "'.$color.'" ';

            if(!empty($fields)){
                $dbuGroup = '   UPDATE '.GROUP_TABLE.'
                                SET  '.$fields.'
                                WHERE id = "' . $id . '"';
                $dbeGroup = mysql_query($dbuGroup);

                $texteaction = _ACTIONGROUPEDIT . $name;
                $acdate = time();

                $dbiAction = 'INSERT INTO '.ACTION_TABLE.' (`date`, `pseudo`, `action`)
                              VALUES("' . $acdate . '", "' . $GLOBALS['user']['0'] . '", "' . $texteaction . '")';
                $dbeAction = mysql_query($dbiAction);
            }
?>
            <div class="notification success png_bg">
                <div><?php echo _SUCCESGROUPEDIT; ?></div>
            </div>
<?php
        }
        else {
?>
            <div class="notification error png_bg">
                <div><?php echo _ERRORGROUPEDIT; ?></div>
            </div>
<?php
        }
    redirect("index.php?file=Admin&page=group", 2);
    }

    function del_group($id) {

        $dbsGroup = ' SELECT nameGroup
                      FROM '.GROUP_TABLE.'
                      WHERE id = "' . $id . '"';
        $dbeGroup = mysql_query($dbsGroup);
        list($nameGroup) = mysql_fetch_array($dbeGroup);

        if ($nameGroup == "_ADMINISTRATOR" or $nameGroup == "_NAMEMEMBERS" or $nameGroup == "_VISITOR") {
?>
            <div class="notification error png_bg">
                <div><?php echo _ERRORGROUPDEL; ?></div>
            </div>
<?php
        }
        else {

            $del = mysql_query("DELETE FROM " . GROUP_TABLE . " WHERE id = '" . $id . "'");

            $texteaction = _ACTIONGROUPDEL . $nameGroup;
            $acdate = time();

            $dbiAction = 'INSERT INTO '.ACTION_TABLE.' (`date`, `pseudo`, `action`)
                          VALUES("' . $acdate . '", "' . $GLOBALS['user']['0'] . '", "' . $texteaction . '")';
            $dbeAction = mysql_query($dbiAction);
?>
            <div class="notification success png_bg">
                <div><?php echo _SUCCESGROUPDEL; ?></div>
            </div>
<?php
        }

        redirect("index.php?file=Admin&page=group", 2);
    }


    switch ($_REQUEST['op'])
    {
        case "mainGroup":
            mainGroup();
            break;

        case "addGroup":
            addGroup();
            break;

        case "editGroup":
            editGroup($_REQUEST['id']);
            break;

        case "del_group":
            del_group($_REQUEST['id']);
            break;

        case "sendGroupAdd":
            sendGroupAdd();
            break;

        case "sendGroupEdit":
            sendGroupEdit();
            break;

        default:
            mainGroup();
            break;
    }

}

else if($nkAccessModule === FALSE) {
    echo ' <div class="notification error png_bg">
                <div style="margin:5x;">' . _NOENTRANCE . '</div>
                <div style="margin:5x;"><a href="javascript:history.back()"><strong>' . _BACK . '</strong></a><div>
           </div>';
}
else
{
    echo ' <div class="notification error png_bg">
                <div style="margin:5x;">' . _ZONEADMIN . '</div>
                <div style="margin:5x;"><a href="javascript:history.back()"><strong>' . _BACK . '</strong></a><div>
           </div>';
}
adminfoot();
?>
