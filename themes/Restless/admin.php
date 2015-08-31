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

        $tpl = new Tpl();
        $tpl->assign('cfg', new iniConfigTool('themes/Restless/config.ini'));

?>
        <script type="text/javascript" src="themes/Restless/js/restless_admin.js"></script>
        <link rel="stylesheet" type="text/css" href="themes/Restless/css/restless_admin.css" />
<?php

        switch ($_REQUEST['op']) {
            case 'settings':
                $tpl->render('adminNav');
                $tpl->render('adminSettings');
                break;
            case 'saveSettings':
                $tpl->render('adminSaveSettings');
                break;
            case 'blocks_management':
                $tpl->render('adminNav');
                $tpl->render('adminBlocks');
                break;
            case 'modules_management':
                $tpl->render('adminNav');
                $tpl->render('adminModules');
                break;
            case 'sponsors_management':
                $tpl->render('adminNav');
                $tpl->render('adminSponsors');
                break;
            case 'saveBlocks':
                $tpl->render('adminSaveBlocks');
                break;
            case 'saveModules':
                $tpl->render('adminSaveModules');
                break;
            case 'saveHome':
                $tpl->render('adminSaveHome');
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