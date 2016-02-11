<?php
/**
 * index.php
 *
 * Frontend of Survey module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Survey'))
    return;

//$captcha = initCaptcha();

function sondage($poll_id) {
    global $nuked;

    opentable();

    if(!empty($poll_id) && is_numeric($poll_id)) {

        echo '<br /><div style="text-align: center;"><big><b>' . _POLLOF . '</b></big></div><br />';

        $sql = mysql_query('SELECT titre FROM ' . SURVEY_TABLE . ' WHERE sid = ' . $poll_id);
        list($titre) = mysql_fetch_array($sql);
        $titre = printSecuTags($titre);

        echo "<form method=\"post\" action=\"index.php?file=Survey&amp;op=update_sondage\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\">\n"
        . "<tr><td align=\"center\"><b>" . $titre . "</b></td></tr>\n";

        $sql2 = mysql_query('SELECT voteID, optionText FROM ' . SURVEY_DATA_TABLE . ' WHERE sid = ' . $poll_id . ' ORDER BY voteID ASC');
        while (list($voteid, $optiontext) = mysql_fetch_array($sql2)) {
            $optiontext = printSecuTags($optiontext);

            echo '<tr><td><input type="radio" class="checkbox" name="voteID" value="' . $voteid . '" />&nbsp;' . $optiontext . '</td></tr>';
        }
        echo "<tr><td>&nbsp;<input type=\"hidden\" name=\"poll_id\" value=\"" . $poll_id . "\" /></td></tr>\n"
        . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . __('TO_VOTE') . "\" />"
        . "&nbsp;<input type=\"button\" value=\"" . _RESULT . "\" onclick=\"document.location='index.php?file=Survey&amp;op=affich_res&amp;poll_id=" . $poll_id . "'\" /></td></tr></table></form><br />\n";
    }
    else {
        echo applyTemplate('nkAlert/noEntrance');
        redirect('index.php?file=Survey', 2);
    }

    closetable();
}

function verif_check($poll_id) {
    global $nuked, $user_ip, $user;

    $time = time();
    $cookiename = $nuked['cookiename'];
    if(array_key_exists($cookiename.'_user_pool_'.$poll_id, $_COOKIE)){

    }
    else{
        $user_pool_id = '';
    }

    $verifip = 0;

    if (!empty($user[2])) $username = $user[2];
    else $username = 'not_member';

    $del = mysql_query('DELETE FROM ' . SURVEY_CHECK_TABLE . ' WHERE heurelimite < ' . $time);

    if (isset($user_pool_id) && $user_pool_id == $poll_id) {
        $verifip = 1;
    } else {
        $sql = mysql_query('SELECT sid FROM ' . SURVEY_CHECK_TABLE . ' WHERE (pseudo = "' . $username . '" OR ip = "' . $user_ip . '") AND sid = ' . $poll_id);
        $verifip = mysql_num_rows($sql);
    }
    return $verifip;
}

function update_sondage($poll_id, $voteID) {
    global $nuked, $user_ip, $user, $visiteur, $theme, $bgcolor2, $bgcolor3;

    nkTemplate_setPageDesign('none');

    $time = time() + $nuked['sond_delay'] * 3600;
    $cookiename = $nuked['cookiename'];

    if (!empty($voteID) && is_numeric($voteID) && is_numeric($poll_id)) {
        if (verif_check($poll_id) == 0) {
            $sql = mysql_query('SELECT niveau FROM ' . SURVEY_TABLE . ' WHERE sid = ' . $poll_id);
            list($niveau) = mysql_fetch_array($sql);

            if ($visiteur >= $niveau) {
                $upd = mysql_query('UPDATE ' . SURVEY_DATA_TABLE . ' SET optionCount = optionCount + 1 WHERE voteID = ' . $voteID . ' AND sid = ' . $poll_id);
                $sql = mysql_query('INSERT INTO ' . SURVEY_CHECK_TABLE . ' ( `ip` , `pseudo` , `heurelimite` , `sid` ) VALUES ( "' . $user_ip . '" , "' . $user[2] . '", "' . $time . '" , "' . $poll_id . '")');
                setcookie($cookiename . '_user_pool_' . $poll_id, $poll_id, $time);

                redirect('index.php?file=Survey&op=vote_message&poll_id=' . $poll_id, 0);
            } else if (!$user && $niveau == 1) {
                redirect('index.php?file=Survey&op=vote_message&poll_id=' . $poll_id . '&error=1', 0);
            } else {
                redirect('index.php?file=Survey&op=vote_message&poll_id='  . $poll_id . '&error=2', 0);
            }
        } else {
            redirect('index.php?file=Survey&op=vote_message&poll_id=' . $poll_id . '&error=3', 0);
        }
    } else {
        redirect('index.php?file=Survey&op=vote_message&poll_id=' . $poll_id . '&error=4', 0);
    }
}

function vote_message() {
    opentable();

    if(array_key_exists('error', $_REQUEST)){
        if ($_REQUEST['error'] == 1) {
            printNotification(_ONLYMEMBERS, 'error');
            redirect('index.php?file=User', 2);
        } else if ($_REQUEST['error'] == 2) {
            printNotification(_NOLEVEL, 'error');
            redirect('index.php?file=Survey&op=affich_res&poll_id='. $_REQUEST['poll_id'], 2);
        } else if ($_REQUEST['error'] == 3) {
            printNotification(_ALREADYVOTE, 'error');
            redirect('index.php?file=Survey&op=affich_res&poll_id='. $_REQUEST['poll_id'], 2);
        } else if ($_REQUEST['error'] == 4) {
            printNotification(_NOOPTION, 'error');
            redirect('index.php?file=Survey&op=sondage&poll_id='. $_REQUEST['poll_id'], 2);
        } else {
            printNotification(_VOTESUCCES, 'success');
            redirect('index.php?file=Survey&op=affich_res&poll_id='. $_REQUEST['poll_id'], 2);
        }
    }
    else{
        printNotification(_VOTESUCCES, 'success');
        redirect('index.php?file=Survey&op=affich_res&poll_id='. $_REQUEST['poll_id'], 2);
    }

    closetable();
}

function affich_res($poll_id) {
    global $nuked, $theme, $visiteur;

    opentable();

    if(!empty($poll_id) && is_numeric($poll_id)) {

        $sql = mysql_query('SELECT titre FROM ' . SURVEY_TABLE . ' WHERE sid=' . $poll_id);
        list($titre) = mysql_fetch_array($sql);
        $titre = printSecuTags($titre);

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _POLLOF . "</b></big></div>\n"
        . "<div style=\"text-align: center;\"><br /><b>$titre</b></div><br />\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n";

        $sql2 = mysql_query('SELECT optionCount FROM ' . SURVEY_DATA_TABLE . ' WHERE sid = ' . $poll_id);
        $nbcount = 0;
        while (list($option_count) = mysql_fetch_array($sql2)) {
            $nbcount = $nbcount + $option_count;
        }

        $sql3 = mysql_query('SELECT optionCount, optionText FROM ' . SURVEY_DATA_TABLE . ' WHERE sid = ' . $poll_id . ' ORDER BY voteID ASC');
        while (list($optioncount, $optiontext) = mysql_fetch_array($sql3)) {
            $optiontext = printSecuTags($optiontext);

            if ($nbcount <> 0) {
                $etat = ($optioncount * 100) / $nbcount;
            } else {
                $etat = 0;
            }
            $pourcent_arrondi = round($etat);

            echo '<tr><td>' . $optiontext . '</td><td>';

            if ($etat < 1) {
                $width = 2;
            } else {
                $width = $etat * 2;
                $width = round($width);
            }
            if (is_file('themes/" . $theme . "/images/bar.gif')) {
                $img = 'themes/" . $theme . "/images/bar.gif';
            } else {
                $img = 'modules/Survey/images/bar.gif';
            }

            echo '<img src="' . $img . '" width="' . $width . '" height="10" alt="" />&nbsp;' . $pourcent_arrondi . '% (' . $optioncount . ')</td></tr>';
        }

        echo "</table><table style=\"margin-left: auto;margin-right: auto;text-align: left;width:90%;\" border=\"0\">\n"
        . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _TOTALVOTE . " : </b>" . $nbcount . "</td></tr>\n";

        $sql = mysql_query(
            'SELECT active
            FROM '. COMMENT_MODULES_TABLE .'
            WHERE module = \'survey\''
        );

        list($active) = mysql_fetch_array($sql);

        if ($active == 1 && $visiteur >= nivo_mod('Comment') && nivo_mod('Comment') > -1) {
            echo '<tr><td>';
            include_once 'modules/Comment/index.php';
            com_index('Survey', $poll_id);
            echo '</td></tr>';
        }

        echo '</table><div style="text-align: center;"><br />[ <a href="index.php?file=Survey&amp;op=sondage&amp;poll_id=' . $poll_id . '">' . _SENDVOTE . '</a> | <a href="index.php?file=Survey">' . _OTHERPOLL . '</a> ]</div><br />';

    }
    else {
        echo applyTemplate('nkAlert/noEntrance');
        redirect('index.php?file=Survey' , 2);
    }

    closetable();
}

function index_sondage() {
    global $nuked, $bgcolor1, $bgcolor2, $bgcolor3;

    opentable();

    echo "<div style=\"text-align: center;\"><br /><big><b>" . _POLLLIST . "</b></big></div><br />\n"
    . "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
    . "<td align=\"center\"><b>" . _QUESTION . "</b></td>\n"
    . "<td align=\"center\"><b>" . _TOTALVOTE . "</b></td>\n"
    . "<td align=\"center\"><b>" . _DATE . "</b></td>\n"
    . "<td align=\"center\"><b>&nbsp;</b></td></tr>\n";

    $sql = mysql_query('SELECT sid, titre, date FROM ' . SURVEY_TABLE . ' ORDER BY date DESC');
    while (list($poll_id, $titre, $date) = mysql_fetch_array($sql)) {
        $titre = printSecuTags($titre);
        $date = nkDate($date);

        $sql2 = mysql_query('SELECT optionCount FROM ' . SURVEY_DATA_TABLE . ' WHERE sid = ' . $poll_id);
        $nbvote = 0;
        $j = 0;
        while (list($option_count) = mysql_fetch_array($sql2)) {
            $nbvote = $nbvote + $option_count;
        }

        if ($j == 0) {
            $bg = $bgcolor2;
            $j++;
        } else {
            $bg = $bgcolor1;
            $j = 0;
        }

        echo "<tr style=\"background: " . $bg . ";\">\n"
        . "<td>&nbsp;<a href=\"index.php?file=Survey&amp;op=sondage&amp;poll_id=" . $poll_id . "\"><b>" . $titre . "</b></a></td>\n"
        . "<td align=\"center\">" . $nbvote . "</td>\n"
        . "<td align=\"center\">" . $date . "</td>\n"
        . "<td align=\"center\"><a href=\"index.php?file=Survey&amp;op=affich_res&amp;poll_id=" . $poll_id . "\"><b>" . _RESULT . "</b></a></td></tr>\n";
    }
    echo '</table><br />';

    closetable();
}

switch ($GLOBALS['op']) {
    case 'sondage':
        sondage($_REQUEST['poll_id']);
        break;

    case 'affich_res':
        affich_res($_REQUEST['poll_id']);
        break;

    case 'update_sondage':
        update_sondage($_REQUEST['poll_id'], $_REQUEST['voteID']);
        break;

    case 'index':
        index_sondage();
        break;

    case 'vote_message':
        vote_message();
        break;

    default:
        index_sondage();
        break;
}


?>