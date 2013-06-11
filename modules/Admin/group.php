<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $user, $language;
translate("modules/Admin/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");

$nkAccessModule = NkAccessModule('Admin', $user[1], false);

if($_REQUEST['op'] != "menu")
admintop();

if($nkAccessModule === TRUE) {

    function main_group() {

        global $nuked, $user, $language;

        $bold = 'font-weight: bold;'; /// en mettre en css //
        $center = 'text-align: center;'; /// en mettre en css //

        $dbsGroup = ' SELECT id, nameGroup, description
                      FROM ' . GROUP . ' ';
        $dbeGroup = mysql_query($dbsGroup);

        echo '<script type="text/javascript">
                <!--
                function delgroup(name, id) {
                    if (confirm("' . _FUNCDELGROUP . ' " + name +" ! ' . _CONFIRM . '")) {
                        document.location.href = "index.php?file=Admin&page=group&op=del_group&id="+id;
                    }
                }
                // -->
              </script>';
        ?>
            <div class="content-box">
                <div class="content-box-header"><h3><?php echo _USERADMINGROUP; ?></h3>
                    <div style="text-align:right;">
                        <a href="help" . $language . "/user.php" rel="modal">
                            <img style="border: 0;" src="help/help.gif" alt="" title="" . _HELP . "" />
                        </a>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div style="text-align: center;margin: 10px 0;">
                        <?php echo _TITLEGESTGROUP; ?><strong> |
                        <a href="index.php?file=Admin&amp;page=group&amp;op=add_group"><?php echo _MENUADDGROUP; ?></a></strong>
                    </div>

                    <table width="100%" border="0" cellspacing="1" cellpadding="2">
                        <tr>
                            <td style="width:30%; <?php echo $bold; ?>"><?php echo _TITLEWORDING; ?></td>
                            <td style="width:30%; <?php echo $bold; ?>"><?php echo _TITLEDESCGROUP; ?></td>
                            <td style="width:20%; <?php echo $bold; ?>"><?php echo _UTILISATEURS; ?></td>
                            <td style="width:10%; <?php echo $bold . $center; ?>"><?php echo _EDIT; ?></td>
                            <td style="width:10%; <?php echo $bold . $center; ?>"><?php echo _DELETE; ?></td>
                        </tr>
                        <?php
                        while(list($id, $nameGroup, $description) = mysql_fetch_array($dbeGroup)) {

                                $dbsUserCount = ' SELECT id
                                                  FROM ' . USER_TABLE . '
                                                  WHERE GroupMain = "' . $id . '"';
                                $dbeUserCount = mysql_query($dbsUserCount);
                                $dbcUserCount = mysql_num_rows($dbeUserCount);

                                if($description) {
                                    if ($description == "_DESCSUPPADMIN") {
                                        $description = _DESCSUPPADMIN;
                                    } else if ($description == "_DESCMEMBERS") {
                                        $description = _DESCMEMBERS;
                                    } else if ($description == "_DESCVISITORPADMIN") {
                                        $description = _DESCVISITORPADMIN;
                                    }  else {
                                        $description = $description;
                                    }
                                } else {
                                    $description = 'Aucune description';
                                }
                        ?>
                        <tr>
                            <td><?php echo traductNameGroup($nameGroup); ?></td>
                            <td><?php echo $description; ?></td>
                            <td># <?php echo $dbcUserCount; ?></td>
                            <td style="<?php echo $center; ?>">
                                <a href="index.php?file=Admin&amp;page=group&amp;op=edit_group&amp;id=<?php echo $id; ?>">
                                    <img style="border: 0;" src="images/edit.gif" alt="<?php echo _EDITTHISGROUP; ?>" title="<?php echo _EDIT; ?>" />
                                </a>
                            </td>
                            <td style="<?php echo $center; ?>">
                                <a href="javascript:delgroup('<?php echo mysql_real_escape_string(stripslashes(traductNameGroup($nameGroup))); ?>', '<?php echo $id; ?>');">
                                    <img style="border: 0;" src="images/del.gif" alt="<?php echo _DELTHISGROUP; ?>" title="<?php echo _DELETE; ?>" />
                                </a>
                            </td>
                        </tr>
                        <?php
                            }
                        ?>
                    </table>
                </div>
                <div style="margin: 10px auto;<?php echo $center . $bold; ?>"><a href="index.php?file=Admin&amp;page=user"><?php echo _BACK; ?></a></div>
            </div>
            <?php
    }

    function add_group() {

        global $nuked, $user, $language;

        $bold = 'font-weight: bold;'; /// en mettre en css //
        $center = 'text-align: center;'; /// en mettre en css //

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
                            <img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" />
                        </a>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div style="text-align: center;"><strong>
                        <a href="index.php?file=Admin&amp;page=group&amp;op=main_group"><?php echo _TITLEGESTGROUP; ?></a> |
                            </strong><?php echo _MENUADDGROUP; ?>
                    </div><br />
                    <form method="post" action="index.php?file=Admin&amp;page=group&amp;op=send_group_add">
                        <ul id="userGroup">
                            <li class="half">
                                <span><?php echo _NAMEGROUP; ?></span>
                                <input style="width:50%;" type="text" name="name" required="required" />
                            </li>
                            <li class="half">
                                <span><?php echo _DESCGROUP; ?></span>
                                <input style="width:50%;" type="text" name="description" required="required" />
                            </li>
                            <li class="full"><span><?php echo _LISTMODULE; ?></span></li>
                            <?php
                            while(list($id, $name) = mysql_fetch_array($dbeModule)) {
                            ?>
                            <li class="quarter">
                                <span><?php echo $name; ?></span>
                                <div>
                                    <span class="title"><?php echo _ACCESMODULE; ?></span>
                                    <span class="input"><input <?php echo $disabled; ?> name="a<?php echo $id; ?>" type="checkbox" /></span>
                                </div>
                                <div>
                                    <span class="title"><?php echo _ACCESADMIN; ?></span>
                                    <span class="input"><input <?php echo $disabled; ?> name="b<?php echo $id; ?>" type="checkbox" /></span>
                                </div>
                            </li>
                            <?php
                            }
                            ?>
                            <li class="full"><span><input type="submit" value="<?php echo _ADD; ?>" /></span></li>
                        </ul>
                    </form>
                </div>
                <div style="margin: 10px auto;<?php echo $center . $bold; ?>"><a href="index.php?file=Admin&amp;page=user"><?php echo _BACK; ?></a></div>
            </div>
            <?php
    }

    function send_group_add($name, $description) {

        global $nuked, $user, $language;

            $dbsModule = ' SELECT id, nom
                           FROM ' . MODULES_TABLE . '
                           WHERE niveau != "-1"
                           AND admin != "-1"';
            $dbeModule = mysql_query($dbsModule);
            $i = 0;
            while(list($idGroup, $moduleName) = mysql_fetch_array($dbeModule)) {
                if ($_POST['a'.$idGroup]) {
                    if($a > 0) {
                        $groupForUser .= '|';
                    }
                    $groupForUser .= $moduleName;
                    $a++;
                }
                if ($_POST['b'.$idGroup]) {
                    if($i > 0) {
                        $groupForAdmin .= '|';
                    }
                    $groupForAdmin .= $moduleName;
                    $i++;
                }
            }

            $dbiInsertGroup = 'INSERT INTO ' . GROUP . ' (`id` , `nameGroup` , `access`, `accessAdmin`, `description` )
                               VALUES ("", "' . $name . '" , "' . $groupForUser . '", "' . $groupForAdmin . '", "' . $description . '")';
            $dbeInsertGroup = mysql_query($dbiInsertGroup);

            $texteaction = _ACTIONGROUPADD . $name;
            $acdate = time();

            $sqlaction = 'INSERT INTO ' . ACTION_TABLE . ' (`date`, `pseudo`, `action`)
                          VALUES("' . $acdate . '", "' . $user[0] . '", "' . $texteaction . '")';

        echo ' <div class="notification success png_bg">
                    <div>' . _SUCCESGROUPADD . '</div>
               </div>';
        redirect("index.php?file=Admin&page=group", 2);
    }

    function edit_group($id) {

        global $nuked, $user, $language;

        $bold = 'font-weight: bold;'; /* À intégrer au css */
        $center = 'text-align: center;'; /* À intégrer au css */

        $dbsGroup = ' SELECT nameGroup, access, accessAdmin, description
                      FROM ' . GROUP . '
                      WHERE id = "' . $id . '"';
        $dbeGroup = mysql_query($dbsGroup);
        list($nameGroup, $access, $accessAdmin, $description) = mysql_fetch_array($dbeGroup);

        $access = explode('|', $access);
        $accessAdmin = explode('|', $accessAdmin);

		if($description) {
			if ($description == "_DESCSUPPADMIN") {
				$description = _DESCSUPPADMIN;
			} else if ($description == "_DESCMEMBERS") {
				$description = _DESCMEMBERS;
			} else if ($description == "_DESCVISITORPADMIN") {
				$description = _DESCVISITORPADMIN;
			}  else {
				$description = $description;
			}
		} else {
			$description = 'Aucune description';
		}

        if ($nameGroup == "_ADMINISTRATOR") {
            $disabledName = 'disabled="disabled"';
            $disabledAccess = 'disabled="disabled"';
            $disabledAdmin = 'disabled="disabled"';
        } else if ($nameGroup == "_MEMBERS") {
            $disabledName = 'disabled="disabled"';
            $disabledAccess = '';
            $disabledAdmin = '';
        } else if ($nameGroup == "_VISITOR") {
            $disabledName = 'disabled="disabled"';
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
                            <img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP; ?>" />
                        </a>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div style="text-align: center;"><strong>
                        <a href="index.php?file=Admin&amp;page=group&amp;op=main_group"><?php echo _TITLEGESTGROUP; ?></a> |
                        </strong><?php echo _MENUADDGROUP; ?>
                    </div><br />
                    <form id="adminGroup" method="post" action="index.php?file=Admin&amp;page=group&amp;op=send_group_edit">
                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <ul id="userGroup">
                            <li class="half">
                                <span><?php echo _NAMEGROUP; ?></span>
                                <input <?php echo $disabledName; ?> style="width:50%;" type="text" name="name" value="<?php echo traductNameGroup($nameGroup); ?>" required="required" />
                            </li>
                            <li class="half">
                                <span><?php echo _DESCGROUP; ?></span>
                                <input <?php echo $disabledName; ?> style="width:50%;" type="text" name="description" value="<?php echo $description; ?>" required="required" />
                            </li>
                            <li class="full"><span><?php echo _LISTMODULE; ?></span></li>
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
                            <li class="quarter">
                                <span><?php echo $name; ?></span>
                                <div>
                                    <span class="title">Acc&egrave;s module</span>
                                    <span class="input"><input <?php echo $disabledAccess . $checkedAcces; ?> name="a<?php echo $id; ?>" type="checkbox" value="1"></span>
                                </div>
                                <div>
                                    <span class="title">Acc&egrave;s Admin </span>
                                    <span class="input"><input <?php echo $disabledAdmin  . $checkedAdmin; ?> name="b<?php echo $id; ?>" type="checkbox" value="1"></span>
                                </div>
                            </li>
                            <?php
                            }
                            ?>
                            <li class="full"><span><input type="submit" value="<?php echo _EDIT; ?>" /></span></li>
                        </ul>
                    </form>
                </div>
                <div style="margin: 10px auto;<?php echo $center . $bold; ?>"><a href="index.php?file=Admin&amp;page=group"><?php echo _BACK; ?></a></div>';
            </div>
            <?php
    }

    function send_group_edit($id, $name, $description) {

        global $nuked, $user, $language;

            $dbsGroupName = ' SELECT id
                              FROM ' . GROUP . '
                              WHERE nameGroup = "' . $name . '"';
            $dbeGroupName = mysql_query($dbsGroupName);
            list($dbsGroupNameId) = mysql_fetch_array($dbeGroupName);
            $dbcGroupName = mysql_num_rows($dbeGroupName);

            if ($dbsGroupNameId == $id or $dbcGroupName == 0) {

                $dbsModule = '   SELECT id, nom
                                 FROM ' . MODULES_TABLE . '
                                 WHERE niveau != "-1"
                                 AND admin != "-1"';
                $dbeModule = mysql_query($dbsModule);
                $a = 0;
                $i = 0;
                while(list($idGroup, $moduleName) = mysql_fetch_array($dbeModule)) {
                    if ($_POST['a'.$idGroup]) {
                        if($a > 0) {
                            $groupForUser .= '|';
                        }
                        $groupForUser .= $moduleName;
                        $a++;
                    }
                    if ($_POST['b'.$idGroup]) {
                        if($i > 0) {
                            $groupForAdmin .= '|';
                        }
                        $groupForAdmin .= $moduleName;
                        $i++;
                    }
                }

                $dbuGroup = '   UPDATE ' . GROUP . '
                                SET nameGroup = "' . $name . '", access = "' . $groupForUser . '", accessAdmin = "' . $groupForAdmin . '", description = "' . $description . '"
                                WHERE id = "' . $id . '"';
                $dbeGroup = mysql_query($dbuGroup);

                $texteaction = _ACTIONGROUPEDIT . $name;
                $acdate = time();
                $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");

            echo ' <div class="notification success png_bg">
                        <div>' . _SUCCESGROUPEDIT . '</div>
                   </div>';
            } else {
            echo ' <div class="notification error png_bg">
                        <div>' . _ERRORGROUPEDIT . '</div>
                   </div>';
            }
        redirect("index.php?file=Admin&page=group", 2);
    }

    function del_group($id) {

        global $nuked, $user, $language;

        $dbsGroup = ' SELECT nameGroup
                      FROM ' . GROUP . '
                      WHERE id = "' . $id . '"';
        $dbeGroup = mysql_query($dbsGroup);
        list($nameGroup) = mysql_fetch_array($dbeGroup);

        if ($nameGroup == "_ADMINISTRATOR" or $nameGroup == "_NAMEMEMBERS" or $nameGroup == "_VISITOR") {
            echo ' <div class="notification error png_bg">
                        <div>' . _ERRORGROUPDEL . '</div>
                   </div>';
        } else {

            $del = mysql_query("DELETE FROM " . GROUP . " WHERE id = '" . $id . "'");

            $texteaction = _ACTIONGROUPDEL . $nameGroup;
            $acdate = time();
            $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");

            echo ' <div class="notification success png_bg">
                        <div>' . _SUCCESGROUPDEL . '</div>
                   </div>';
        }

        redirect("index.php?file=Admin&page=group", 2);
    }


    switch ($_REQUEST['op'])
    {
        case "main_group":
            main_group();
            break;

        case "add_group":
            add_group();
            break;

        case "edit_group":
            edit_group($_REQUEST['id']);
            break;

        case "del_group":
            del_group($_REQUEST['id']);
            break;

        case "send_group_add":
            send_group_add($_REQUEST['name'], $_REQUEST['description']);
            break;

        case "send_group_edit":
            send_group_edit($_REQUEST['id'], $_REQUEST['name'], $_REQUEST['description']);
            break;

        default:
            main_group();
            break;
    }

}

else if($nkAccessModule === FALSE) {
	debug($nkAccessModule);
    echo ' <div class="notification error png_bg">
                <div style="margin:5x;">' . _NOENTRANCE . '</div>
                <div style="margin:5x;"><a href="javascript:history.back()"><strong>' . _BACK . '</strong></a><div>
           </div>';
}
else
{
	debug($nkAccessModule);
    echo ' <div class="notification error png_bg">
                <div style="margin:5x;">' . _ZONEADMIN . '</div>
                <div style="margin:5x;"><a href="javascript:history.back()"><strong>' . _BACK . '</strong></a><div>
           </div>';
}
adminfoot();

?>
