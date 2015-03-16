<?php
/**
 * Restless
 * Design by Homax
 * Developped by Samoth93
 * Thanks GuigZ for participation
 * April 2013
 */
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

require_once('themes/Restless/lang/' . $GLOBALS['language'] . '.lang.php');

if ($GLOBALS['user'][1] < 9) {
    printNotification(NOACCESS, 'index.php?file=Admin', 'error');
}
else {
    try {
        require_once 'themes/Restless/librairies/iniConfigTool.php';

        $cfg = new iniConfigTool('themes/Restless/config.ini');

        $tpl = new Tpl();
        $tpl->assign('cfg', $cfg);

?>
        <script type="text/javascript" src="themes/Restless/js/restless_admin.js"></script>
        <link rel="stylesheet" type="text/css" href="themes/Restless/css/restless_admin.css" />
<?php

        switch ($_REQUEST['op']) {
            case 'settings':
                $tpl->render('adminNav');
                $tpl->render('adminSettings', $cfg);
                break;
            case 'blocks_management':
                $tpl->render('adminNav');
                $tpl->render('adminBlocks', $cfg);
                break;
            case 'modules_management':
                $tpl->render('adminNav');
                $tpl->render('adminModules');
                break;
            case 'saveBlocks':
                $tpl->render('adminSaveBlocks');
                break;
            default:
                $tpl->render('adminNav');
                $tpl->render('adminHome');
                break;
        }
    } catch (Exception $e) {
        printNotification($e->getMessage(), 'index.php?file=Admin&page=theme', 'error', false);
    }
}