<?php
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


function getUserAvatar() {
    global $user;

    $dbrUser = nkDB_selectOne(
        'SELECT avatar
        FROM '. USER_TABLE .'
        WHERE id = '. nkDB_escape($user['id'])
    );

    if ($dbrUser['avatar'] != '')
        return $dbrUser['avatar'];
    else
        return 'modules/User/images/noavatar.png';
}

function printAdminMenuCurrentClass($menuName) {
    global $file, $page;

    $menuData = array(
        'pannel'        => array('index'),
        'parameter'     => array('setting','maj','phpinfo','mysql','action','erreursql'),
        'management'    => array('user','theme','modules','block','menu','smilies','games'),
        'miscellaneous' => array('propos', 'licence')
    );

    if ($file == 'Admin'
        && array_key_exists($menuName, $menuData)
        && in_array($page, $menuData[$menuName])
    )
        echo ' current';
}

function printAdminSubMenuCurrentClass($menuPage) {
    global $file, $page;

    if ($file == 'Admin' && $page == $menuPage)
        echo 'class="current"';
}

function getAdminModulesMenuData() {
    global $file, $page, $visiteur;

    $data = array(
        0  => '',
        1  => array()
    );

    $dbrModules = nkDB_selectMany(
        'SELECT `nom`
        FROM `'. MODULES_TABLE .'`
        WHERE \''. $visiteur .'\' >= admin AND niveau > -1 AND admin > -1',
        array('nom')
    );

    foreach ($dbrModules as $mod) {
        $moduleNameConst = strtoupper($mod['nom']) .'_MODNAME';

        if (translationExist($moduleNameConst))
            $moduleName = __($moduleNameConst);
        else
            $moduleName = $mod['nom'];

        if (is_file('modules/'. $mod['nom'] .'/admin.php') || is_dir('modules/'. $mod['nom'] .'/backend'))
            $data[1][$mod['nom']] = $moduleName;
    }

    natcasesort($data[1]);

    if (array_key_exists($file, $data[1]) && $page == 'admin')
        $data[0] = ' current';

    return $data;
}

function getDiscussionData() {
    return nkDB_selectMany(
        'SELECT date, texte, author
        FROM '. DISCUSSION_TABLE,
        array('date'), 'DESC', 16
    );
}

// TODO : Remove it later. Merge in nkForm
function checkboxButton($name, $id, $checked = false, $inline = false) {
    $check = null;
    $classInline = null;
    $dataCheck = null;
    if ($checked === true) {
        $check = 'checked="checked"';
        $dataCheck = 'data-check="checked"';
    }

    if ($inline === true) {
        $classInline = ' inline ';
    }
    ?>
    <div class="onoffswitch <?php echo $classInline; ?> ">
        <input id="<?php echo $id; ?>" type="checkbox" name="<?php echo $name; ?>"
               class="onoffswitch-checkbox" <?php echo $check; ?>
                <?php echo $dataCheck; ?> />
        <label class="onoffswitch-label" for="<?php echo $id; ?>">
            <div class="onoffswitch-inner"></div>
            <div class="onoffswitch-switch"></div>
        </label>
    </div>
<?php
}


function saveUserAction($action) {
    global $user;

    nkDB_insert(ACTION_TABLE, array(
        'date'      => time(),
        'authorId'  => $user['id'],
        'author'    => $user['name'],
        'action'    => $action
    ));
}

/**
 * Set website preview if enabled, and redirect.
 *
 * @param string $previewUrl : Url of website preview.
 * @param string $redirect : Url to redirect after website preview.
 * @param void
 */
function setPreview($previewUrl, $redirect) {
    nkTemplate_addJS('setTimeout(function(){screenon("'. $previewUrl .'", "'. $redirect .'");},"3000");' ."\n");
}

function getMenuOfModuleAdmin($module = null) {
    $adminMenu = '';

    if ($module === null)
        $module = $GLOBALS['file'];

    if (is_file('modules/'. $module .'/backend/config/menu.php')) {
        $adminMenu = require_once 'modules/'. $module .'/backend/config/menu.php';

        $adminMenu = applyTemplate('share/adminMenu', array(
            'menu' => $adminMenu
        ));
    }

    return $adminMenu;
}

?>