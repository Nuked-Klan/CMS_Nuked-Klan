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

$nkAccessModule = nkAccessAdmin('Groups');

admintop();

if($nkAccessModule === true) {

    function mainGroup() {
        $dbsGroup = ' SELECT id, nameGroup, description
                      FROM '.GROUP_TABLE.' ';
        $dbeGroup = mysql_query($dbsGroup);
?>
        <script type="text/javascript">
        <!--
            function delgroup(name, id) {
                if (confirm("<?php echo _FUNCDELGROUP; ?> " + name +" ! <?php echo _CONFIRM; ?>")) {
                    document.location.href = "index.php?file=Admin&page=group&op=delGroup&id="+id;
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

                            if ($id == 1 or $id == 2 or $id == 3) {
                                $linkDel = "<img src=\"modules/Admin/images/stop.png\" alt=\""._DELTHISGROUP."\" title=\""._DELETE."\" />";
                            }
                            else {
                                $linkDel = "
                                <a href=\"javascript:delgroup('".mysql_real_escape_string($nameGroup). "', '" .$id."')\">
                                    <img src=\"images/del.gif\" alt=\""._DELTHISGROUP."\" title=\""._DELETE."\" />
                                </a>";
                            }

                            if ($id == 1) {
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
                            <td class="nkCenter">
                                <?php echo $linkEdit; ?>
                            </td>
                            <td class="nkCenter">
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
        $dbsModule = ' SELECT id, nom AS name, niveau AS level
                       FROM ' . MODULES_TABLE . ' ';
        $dbeModule = mysql_query($dbsModule);

        $arrayModules = array();

        while($data = mysql_fetch_assoc($dbeModule)) {
            $arrayTmp = array('id', 'name', 'level');
            foreach($arrayTmp as $field){
                $arrayModules[$data['id']][$field] = $data[$field];
            }
        }

        // Ajout des module Fake Users et Groups
        $arrayUsers = array('id' => '', 'name' => 'Users', 'level' => 0);
        $arrayGroups = array('id' => '', 'name' => 'Groups', 'level' => 0);
        $arrayModules[] = $arrayUsers;
        $arrayModules[] = $arrayGroups;
?>
            <div class="content-box">
                <div class="content-box-header"><h3><?php echo _USERADMINGROUP; ?></h3>
                    <div style="text-align:right;">
                        <a href="help<?php echo $GLOBALS['language']; ?>/group.php" rel="modal">
                            <img src="help/help.gif" alt="" title="<?php echo _HELP; ?>" />
                        </a>
                    </div>
                </div>
                <div class="tab-content" id="tab2">
                    <div class="margin10Center"><strong>
                        <a href="index.php?file=Admin&amp;page=group&amp;op=mainGroup"><?php echo _TITLEGESTGROUP; ?></a> |
                            </strong><?php echo _MENUADDGROUP; ?>
                    </div>
                    <form id="adminGroup" method="post" action="index.php?file=Admin&amp;page=group&amp;op=sendGroupAdd">
                        <div id="userGroup">
                            <div class="three">
                                <span><?php echo _NAMEGROUP; ?></span>
                                <input class="tableWidth50" type="text" name="name" required="required" />
                            </div>
                            <div class="three">
                                <span><?php echo _DESCGROUP; ?></span>
                                <input class="tableWidth50" type="text" name="description" />
                            </div>
                            <div class="three" id="colorPickerJquery">
                                <span><?php echo _COLOR; ?></span>
                                <input class="tableWidth50" type="text" name="color" id="colorPickerGroup" value="#000000" />
                                <div id="picker"></div>
                            </div>
                            <div class="full"><span><?php echo _LISTMODULE; ?></span></div>
<?php
                            foreach($arrayModules as $module){
                                if($module['level'] == '-1'){
                                    $class = 'groupModDisabled';
                                }
                                else{
                                    $class = 'groupModEnabled';
                                }

                                if($module['name'] == 'Users' || $module['name'] == 'Groups'){
                                    $class2 = 'groupFakeMod';
                                }
                                else{
                                    $class2 = '';
                                }
?>
                            <div class="quarter <?php echo $class; ?>">
                                <span><?php echo $module['name']; ?></span>
<?php
                                if($module['name'] != 'Users' && $module['name'] != 'Groups'){
?>
                                <div class="tableWidth80 nKcenter">
                                    <label for="<?php echo $module['name'].'Access'; ?>">
                                        <span class="title"><?php echo _ACCESMODULE; ?></span>
                                        <input id="<?php echo $module['name'].'Access'; ?>" name="<?php echo $module['name'].'Access'; ?>" type="checkbox" />
                                        <span class="checkboxSlider"></span>
                                    </label>
                                </div>
<?php
                                }
?>
                                <div class="tableWidth80 nKcenter <?php echo $class2; ?>">
                                    <label for="<?php echo $module['name'].'AccessAdmin'; ?>">
                                        <span class="title"><?php echo _ACCESADMIN; ?></span>
                                        <input id="<?php echo $module['name'].'AccessAdmin'; ?>" name="<?php echo $module['name'].'AccessAdmin'; ?>" type="checkbox" />
                                        <span class="checkboxSlider"></span>
                                    </label>
                                </div>
                            </div>
<?php
                            }
?>
                            <div class="full">
                                <p class="textModEnabled"><?php echo _COLOR_MOD_ENABLED; ?></p>&nbsp;|&nbsp;
                                <p class="textModDisabled"><?php echo _COLOR_MOD_DISABLED; ?></p>
                            </div>
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
            if(!array_key_exists($key, $_REQUEST)){
                $_REQUEST[$key] = '';
            }
        }

        $dbsTestName = ' SELECT count(id) AS count
                         FROM '.GROUP_TABLE.'
                         WHERE nameGroup = "' . $_REQUEST['name'] . '"';
        $dbeTestName = mysql_query($dbsTestName);
        $testName = mysql_fetch_assoc($dbeTestName);

        $errors = 0;
        if(empty($_REQUEST['name'])){
            $errorMsg = _ERROR_NO_GROUPNAME;
            $errors++;
        }

        $arrayNameReserved = array('Visiteur', 'Guest', 'Membres', 'Members', 'Administrateur', 'Administrator');

        if(in_array($_REQUEST['name'], $arrayNameReserved)){
            $errorMsg = _GROUP_NAME_RESERVED;
            $errors++;
        }

        if($errors > 0){
            echo '<div class="notification error png_bg">
                    <div>'.$errorMsg.'</div>
                </div>';
            redirect('index.php?file=Admin&page=group&op=addGroup', 3);
            adminfoot();
            exit();
        }

        if($testName['count'] == 0) {

            $dbsModule = ' SELECT id, nom AS name, niveau AS level
                       FROM ' . MODULES_TABLE . ' ';
            $dbeModule = mysql_query($dbsModule);

            $arrayModules = array();

            while($data = mysql_fetch_assoc($dbeModule)) {
                $arrayTmp = array('id', 'name', 'level');
                foreach($arrayTmp as $field){
                    $arrayModules[$data['id']][$field] = $data[$field];
                }
            }

            // Ajout des module Fake Users et Groups
            $arrayUsers = array('id' => '', 'name' => 'Users', 'level' => 0);
            $arrayGroups = array('id' => '', 'name' => 'Groups', 'level' => 0);
            $arrayModules[] = $arrayUsers;
            $arrayModules[] = $arrayGroups;

            $groupForUser = array();
            $groupForAdmin = array();

            foreach($arrayModules as $module) {
                $inputAccess = $module['name'].'Access';
                $inputAdmin  = $module['name'].'AccessAdmin';
                if (array_key_exists($inputAccess, $_REQUEST) && !empty($_REQUEST[$inputAccess])) {
                    $groupForUser[] = $module['name'];
                }
                if (array_key_exists($inputAdmin, $_REQUEST) && !empty($_REQUEST[$inputAdmin])) {
                    $groupForAdmin[] = $module['name'];
                }
            }

            if(count($groupForAdmin) > 0){
                if(!in_array('Admin', $groupForUser)){
                    $groupForUser[] = 'Admin';
                }
            }

            $groupForUser = implode('|', $groupForUser);
            $groupForAdmin = implode('|', $groupForAdmin);

            $pattern = '#[A-z0-9\(\) _éèëêâäàîïöôûü°\.\:\',-]{0,30}#';

            $errors = 0;

            if(!empty($_REQUEST['name']) && !preg_match($pattern, $_REQUEST['name'])){
                $errors++;
                $errorMsg = _NAMEINVALID;
            }

            if(!empty($_REQUEST['name']) && !preg_match($pattern, $_REQUEST['description'])){
                $errors++;
                $errorMsg = _DESCINVALID;
            }

            if(!empty($_REQUEST['name']) && !preg_match('#\#[0-9a-f]{6}#', $_REQUEST['color'])){
                $errors++;
                $errorMsg = _COLORINVALID;
            }

            if($errors > 0){
                echo '<div class="notification error png_bg">
                        <div>'.$errorMsg.'</div>
                    </div>';
                redirect('index.php?file=Admin&page=group&op=addGroup', 3);
                adminfoot();
                exit();
            }

            $name = mysql_real_escape_string($_REQUEST['name']);
            $description = mysql_real_escape_string($_REQUEST['description']);
            $color = mysql_real_escape_string($_REQUEST['color']);

            $dbiInsertGroup = 'INSERT INTO '.GROUP_TABLE.' (`id` , `nameGroup` , `access`, `accessAdmin`, `description`, `color` )
                               VALUES ("", "' . $name . '" , "' . $groupForUser . '", "' . $groupForAdmin . '", "' . $description . '", "' . $color . '")';
            $dbeInsertGroup = mysql_query($dbiInsertGroup);

            $texteaction = _ACTIONGROUPADD . $name;
            $acdate = time();

            $dbiAction = 'INSERT INTO '.ACTION_TABLE.' (`date`, `pseudo`, `action`)
                          VALUES("' . $acdate . '", "' . $GLOBALS['user']['id'] . '", "' . $texteaction . '")';
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
    redirect("index.php?file=Admin&page=group", 2);
    }

    function editGroup() {
        if(array_key_exists('id', $_REQUEST)){
            $id = intval($_REQUEST['id']);
        }
        else{
            $id = 0;
        }

        $dbsGroup = ' SELECT nameGroup, access, accessAdmin, description, color, count(*) AS count
                      FROM '.GROUP_TABLE.'
                      WHERE id = "' . $id . '"';
        $dbeGroup = mysql_query($dbsGroup);
        list($nameGroup, $access, $accessAdmin, $description, $color, $count) = mysql_fetch_array($dbeGroup);

        if($count == 0){
            echo '<div class="notification error png_bg">
                    <div>'._BAD_GROUP_ID.'</div>
                </div>';
            redirect('index.php?file=Admin&page=group&op=addGroup', 3);
            adminfoot();
            exit();
        }

        $access = explode('|', $access);
        $accessAdmin = explode('|', $accessAdmin);

		if(empty($description)){
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

        $dbsModule = ' SELECT id, nom AS name, niveau AS level
                       FROM ' . MODULES_TABLE . ' ';
        $dbeModule = mysql_query($dbsModule);

        $arrayModules = array();

        while($data = mysql_fetch_assoc($dbeModule)) {
            $arrayTmp = array('id', 'name', 'level');
            foreach($arrayTmp as $field){
                $arrayModules[$data['id']][$field] = $data[$field];
            }
        }

        // Ajout des module Fake Users et Groups
        $arrayUsers = array('id' => '', 'name' => 'Users', 'level' => 0);
        $arrayGroups = array('id' => '', 'name' => 'Groups', 'level' => 0);
        $arrayModules[] = $arrayUsers;
        $arrayModules[] = $arrayGroups;
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
                                <input <?php echo $disabledName; ?> class="tableWidth50" type="text" name="description" value="<?php echo translateGroupName($id, $description); ?>" />
                            </div>
                            <div class="three" id="colorPickerJquery">
                                <span><?php echo _COLOR; ?></span>
                                <input class="tableWidth50" type="text" name="color" id="colorPickerGroup" value="<?php echo $color; ?>" />
                                <div id="picker"></div>
                            </div>
                            <div class="full"><span><?php echo _LISTMODULE; ?></span></div>
<?php
                            foreach($arrayModules as $module){
                                if($module['level'] == '-1'){
                                    $class = 'groupModDisabled';
                                }
                                else{
                                    $class = 'groupModEnabled';
                                }

                                if (in_array($module['name'], $access, true)) {
								    $checkedAcces = 'checked="checked"';
                                } else {
                                    $checkedAcces = '';
                                }
                                if (in_array($module['name'], $accessAdmin, true)) {
								    $checkedAdmin = 'checked="checked"';
                                } else {
                                    $checkedAdmin = '';
                                }

                                if($module['name'] == 'Users' || $module['name'] == 'Groups'){
                                    $class2 = 'groupFakeMod';
                                }
                                else{
                                    $class2 = '';
                                }
?>
                            <div class="quarter <?php echo $class; ?>">
                                <span><?php echo $module['name']; ?></span>
<?php
                                if($module['name'] != 'Users' && $module['name'] != 'Groups'){
?>
                            <div class="tableWidth80 nKcenter">
                                    <label for="<?php echo $module['name'].'Access'; ?>">
                                        <span class="title"><?php echo _ACCESMODULE; ?></span>
                                        <input id="<?php echo $module['name'].'Access'; ?>" <?php echo $disabledAccess . $checkedAcces; ?> name="<?php echo $module['name'].'Access'; ?>" type="checkbox" />
                                        <span class="checkboxSlider"></span>
                                    </label>
                                </div>
<?php
                                }
?>
                                <div class="tableWidth80 nKcenter <?php echo $class2; ?>">
                                    <label for="<?php echo $module['name'].'AccessAdmin'; ?>">
                                        <span class="title"><?php echo _ACCESADMIN; ?></span>
                                        <input id="<?php echo $module['name'].'AccessAdmin'; ?>" <?php echo $disabledAdmin  . $checkedAdmin; ?> name="<?php echo $module['name'].'AccessAdmin'; ?>" type="checkbox" />
                                        <span class="checkboxSlider"></span>
                                    </label>
                                </div>
                            </div>
<?php
                            }
?>
                            <div class="full">
                                <p class="textModEnabled"><?php echo _COLOR_MOD_ENABLED; ?></p>&nbsp;|&nbsp;
                                <p class="textModDisabled"><?php echo _COLOR_MOD_DISABLED; ?></p>
                            </div>
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
                $_REQUEST[$key] = $_REQUEST[$key];
            }
            else{
                $_REQUEST[$key] = '';
            }
        }

        $id = intval($_REQUEST['id']);

        $dbsGroupName = ' SELECT id , nameGroup, count(*) AS count
                          FROM '.GROUP_TABLE.'
                          WHERE id = "'.$id.'"';
        $dbeGroupName = mysql_query($dbsGroupName);
        $group = mysql_fetch_assoc($dbeGroupName);

        if($group['count'] == 0){
            echo '<div class="notification error png_bg">
                    <div>'._BAD_GROUP_ID.'</div>
                </div>';
            redirect('index.php?file=Admin&page=group', 3);
            adminfoot();
            exit();
        }

        $arrayPermGroup = array('1' => '_ADMINISTRATOR', '2' => '_MEMBERS', '3' => '_VISITOR');
        $arrayPermGroupDesc = array('1' => '_DESCADMINISTRATOR', '2' => '_DESCMEMBERS', '3' => '_DESCVISITORPADMIN');

        if($id == 1 || $id == 2 || $id == 3){
            $_REQUEST['name'] = $arrayPermGroup[$id];
            $_REQUEST['description'] = $arrayPermGroupDesc[$id];
        }

        $arrayNameReserved = array('Visiteur', 'Guest', 'Membres', 'Members', 'Administrateur', 'Administrator');

        if(in_array($_REQUEST['name'], $arrayNameReserved)){
            echo '<div class="notification error png_bg">
                    <div>'._GROUP_NAME_RESERVED.'</div>
                </div>';
            redirect('index.php?file=Admin&page=group&op=editGroup&id='.$id, 3);
            adminfoot();
            exit();
        }

        $dbsModule = ' SELECT id, nom AS name, niveau AS level
                       FROM ' . MODULES_TABLE . ' ';
        $dbeModule = mysql_query($dbsModule);

        $arrayModules = array();

        while($data = mysql_fetch_assoc($dbeModule)) {
            $arrayTmp = array('id', 'name', 'level');
            foreach($arrayTmp as $field){
                $arrayModules[$data['id']][$field] = $data[$field];
            }
        }

        // Ajout des module Fake Users et Groups
        $arrayUsers = array('id' => '', 'name' => 'Users', 'level' => 0);
        $arrayGroups = array('id' => '', 'name' => 'Groups', 'level' => 0);
        $arrayModules[] = $arrayUsers;
        $arrayModules[] = $arrayGroups;

        $groupForUser = array();
        $groupForAdmin = array();

        foreach($arrayModules as $module) {
            $inputAccess = $module['name'].'Access';
            $inputAdmin  = $module['name'].'AccessAdmin';
            if (array_key_exists($inputAccess, $_REQUEST) && !empty($_REQUEST[$inputAccess])) {
                $groupForUser[] = $module['name'];
            }
            if (array_key_exists($inputAdmin, $_REQUEST) && !empty($_REQUEST[$inputAdmin])) {
                $groupForAdmin[] = $module['name'];
            }
        }

        if(count($groupForAdmin) > 0){
            if(!in_array('Admin', $groupForUser)){
                $groupForUser[] = 'Admin';
            }
        }

        $groupForUser = implode('|', $groupForUser);
        $groupForAdmin = implode('|', $groupForAdmin);

        $pattern = '#[A-z0-9\(\) _éèëêâäàîïöôûü°\.\:\',-]{0,30}#';

        $errors = 0;

        if(!empty($_REQUEST['name']) && !preg_match($pattern, $_REQUEST['name'])){
            $errors++;
            $errorMsg = _NAMEINVALID;
        }

        if(!empty($_REQUEST['name']) && !preg_match($pattern, $_REQUEST['description'])){
            $errors++;
            $errorMsg = _DESCINVALID;
        }

        if(!empty($_REQUEST['name']) && !preg_match('#\#[0-9a-f]{6}#', $_REQUEST['color'])){
            $errors++;
            $errorMsg = _COLORINVALID;
        }

        if($errors > 0){
            echo '<div class="notification error png_bg">
                    <div>'.$errorMsg.'</div>
                </div>';
            redirect('index.php?file=Admin&page=group&op=editGroup&id='.$id, 3);
            adminfoot();
            exit();
        }

        $name = mysql_real_escape_string($_REQUEST['name']);
        $description = mysql_real_escape_string($_REQUEST['description']);
        $color = mysql_real_escape_string($_REQUEST['color']);

        $fields = '';

        if($id != 1) {
            $fields .= ' access = "'.$groupForUser.'" ';
        }

        if ($id != 1 && $id != 3) {
            $fields .= ' , accessAdmin = "'.$groupForAdmin.'" ';
        }

        if($id != 1 && $id != 2 && $id != 3) {
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
                          VALUES("' . $acdate . '", "' . $GLOBALS['user']['id'] . '", "' . $texteaction . '")';
            $dbeAction = mysql_query($dbiAction);
        }
?>
        <div class="notification success png_bg">
            <div><?php echo _SUCCESGROUPEDIT; ?></div>
        </div>
<?php
    redirect("index.php?file=Admin&page=group", 2);
    }

    function delGroup() {
        if(array_key_exists('id', $_REQUEST)){
            $id = intval($_REQUEST['id']);
        }
        else{
            $id = 0;
        }

        if($id == 1 || $id == 2 || $id == 3){
            echo '<div class="notification error png_bg">
                    <div>'._NO_DELETE_GROUP.'</div>
                </div>';
            redirect('index.php?file=Admin&page=group', 3);
            adminfoot();
            exit();
        }

        $dbsGroupName = " SELECT id, nameGroup, count(*) AS count
                          FROM ".GROUP_TABLE."
                          WHERE id = '".$id."' ";
        $dbeGroupName = mysql_query($dbsGroupName);
        $group = mysql_fetch_assoc($dbeGroupName);

        if($group['count'] == 0){
            echo '<div class="notification error png_bg">
                    <div>'._BAD_GROUP_ID.'</div>
                </div>';
            redirect('index.php?file=Admin&page=group', 3);
            adminfoot();
            exit();
        }


        $del = mysql_query("DELETE FROM " . GROUP_TABLE . " WHERE id = '" . $id . "'");

        $texteaction = _ACTIONGROUPDEL . $group['nameGroup'];
        $acdate = time();

        $dbiAction = 'INSERT INTO '.ACTION_TABLE.' (`date`, `pseudo`, `action`)
                      VALUES("' . $acdate . '", "' . $GLOBALS['user']['id'] . '", "' . $texteaction . '")';
        $dbeAction = mysql_query($dbiAction);
?>
        <div class="notification success png_bg">
            <div><?php echo _SUCCESGROUPDEL; ?></div>
        </div>
<?php
        redirect("index.php?file=Admin&page=group", 2);
    }

    if(!array_key_exists('op', $_REQUEST)){
        $_REQUEST['op'] = '';
    }

    switch ($_REQUEST['op']){
        case "mainGroup":
            mainGroup();
            break;

        case "addGroup":
            addGroup();
            break;

        case "editGroup":
            editGroup();
            break;

        case "delGroup":
            delGroup();
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

else if($nkAccessModule === false) {
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
