<?php
/**
 * loging.php
 *
 * Login for backend access
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $cookie_session, $user, $visiteur, $nuked, $language;

translate('modules/Admin/lang/'. $language .'.lang.php');
translate('modules/User/lang/'. $language .'.lang.php');
require_once 'Includes/hash.php';


nkTemplate_setInterface('backend');
nkTemplate_setPageDesign('none');

$url        = 'index.php';
$message    = '';

if ($visiteur >= 2) {
    if ($user && isset($_POST['admin_password']) && $_POST['admin_password'] != '') {
        $dbrUser = nkDB_selectOne(
            'SELECT pass
            FROM '. USER_TABLE .'
            WHERE id = '. nkDB_escape($user['id'])
        );

        if (nkDB_numRows() == 1 && Check_Hash($_POST['admin_password'], $dbrUser['pass'])) {
            if ($_POST['formulaire'] == 0 && $_SERVER['HTTP_REFERER'] != '') {
                list(, $redirect) = explode('?', $_SERVER['HTTP_REFERER']);
                if ($redirect == 'file=Admin&page=login') $redirect = 'file=Admin';
                $url = 'index.php?'. $redirect;
            }
            else $url = 'index.php?file=Admin';

            $_SESSION['admin'] = true;
            // Action
            nkDB_insert(ACTION_TABLE,
                array('date', 'pseudo', 'action'),
                array(time(), $user['id'], _ACTIONCONNECT)
            );
            //Fin action

            $message    = _ADMINPROGRESS;
        }
        else {
            $message    = _BADLOGADMIN;
            $url        = 'index.php?file=Admin';
        }

        echo applyTemplate('modules/Admin/login', array('message' => $message));

        redirect($url, 2);
    }
    else {
        if (! $user)
            redirect('index.php?file=User&op=login_screen');
        else if ($_REQUEST['page'] == 'admin')
            redirect('index.php?file=Admin');
        else {
            echo applyTemplate('modules/Admin/login', array(
                'user'  => $user,
                'check' => ($_POST) ? 1 : 0
            ));
        }
    }
}
else {
    echo applyTemplate('modules/Admin/login', array('message' => 'zoneAdmin'));
}

?>