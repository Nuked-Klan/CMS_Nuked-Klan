<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('<div style="text-align: center;">You cannot open this page directly</div>');

translate('modules/Vote/lang/' . $language . '.lang.php');

$visiteur = ($user) ? $user[1] : 0; 

function vote_index($module, $vid) {
    global $user, $nuked, $visiteur;

    $level_access = nivo_mod('Vote');

    echo '<b>' . _NOTE . ' :</b>&nbsp;';

    $sql = mysql_query("SELECT id, ip, vote FROM " . VOTE_TABLE . " WHERE vid = '" . $vid . "' AND module = '" . mysql_real_escape_string(stripslashes($module)) . "'");
    $count = mysql_num_rows($sql);

    if ($count > 0) {
        while (list($id, $ip, $vote) = mysql_fetch_array($sql)) {
            $total = $total + $vote / $count;
            $pourcent_arrondi = ceil($total);
        } 
        $note = $pourcent_arrondi;

        for ($i = 2;$i <= $note;$i += 2) {
            echo '<img style="border: 0;" src="modules/Vote/images/z1.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
            $n++;
        } 

        if (($note - $i) != -2) {
            echo '<img style="border: 0;" src="modules/Vote/images/z2.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
            $n++;
        } 

        for ($z = $n;$z < 5;$z++) {
            echo '<img style="border: 0;" src="modules/Vote/images/z3.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
        } 
    } else {
        echo _NOTEVAL;
    } 

    if ($visiteur >= $level_access && $level_access > -1) {
        echo '&nbsp;<small>[ <a href="#" onclick="javascript:window.open(\'index.php?file=Vote&amp;nuked_nude=index&amp;op=post_vote&amp;vid=' . $vid . '&amp;module=' . $module . '\',\'screen\',\'toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=350,height=150,top=30,left=0\');return(false)">' . _RATE . '</a> ]</small>'."\n";
    } 
} 

function post_vote($module, $vid) {
    global $user, $nuked, $bgcolor2, $theme, $visiteur,$user_ip;

    $level_access = nivo_mod('Vote');

    if ($visiteur >= $level_access) {

        if ($user) {
            $author = $user[2];
        } else {
            $author = _VISITOR;
        } 

        $sql = mysql_query("SELECT ip FROM " . VOTE_TABLE . " WHERE vid = '" . $vid . "' AND module = '" . mysql_real_escape_string(stripslashes($module)) . "' AND ip = '" . $user_ip . "'");
        $count = mysql_num_rows($sql);

        if ($count > 0) {
            echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
               . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
               . "<head><title>" . _VOTEFROM . "&nbsp;" . $author . "</title>\n"
               . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
               . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
               . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
               . "<body style=\"background : " . $bgcolor2 . ";\">\n"
               . "<div style=\"text-align: center;\"><br /><br /><br />" . _ALREADYVOTE . "<br /><br /><br />\n"
               . "<a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOWS . "</b></a></div></body></html>";
        } else {
            echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
               . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
               . "<head><title>" . _VOTEFROM . "&nbsp;" . $author . "</title>\n"
               . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
               . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
               . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
               . "<body style=\"background : " . $bgcolor2 . ";\">\n"
               . "<form method=\"post\" action=\"index.php?file=Vote&amp;nuked_nude=index&amp;op=do_vote\">\n"
               . "<div style=\"text-align: center;\"><br /><br />" . _ONEVOTEONLY . "<br /><br /><b>" . _NOTE . " : </b>"
               . "<select name=\"vote\"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>"
               . "<option>6</option><option>7</option><option>8</option><option>9</option><option>10</option></select>"
               . "&nbsp;<b>/10</b><br /><br /><input type=\"hidden\" name=\"vid\" value=\"" . $vid . "\" />\n"
               . "<input type=\"hidden\" name=\"module\" value=\"" . $module . "\" />\n"
               . "<input type=\"submit\" name=\"Submit\" value=\"" . _TOVOTE . "\" /></div></form></body></html>";
        }
    } else {
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
           . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
           . "<head><title>" . _VOTEFROM . "&nbsp;" . $author . "</title>\n"
           . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
           . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
           . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
           . "<body style=\"background : " . $bgcolor2 . ";\">\n"
           . "<div style=\"text-align: center;\"><br /><br /><br />" . _NOENTRANCE . "<br /><br /><br />\n"
           . "<a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></body></html>";
    }
 
} 

function do_vote($vid, $module, $vote) {
    global $nuked, $user, $bgcolor2, $theme, $visiteur,$user_ip;

    $level_access = nivo_mod('Vote');
    $module = mysql_real_escape_string(stripslashes($module));

    if ($visiteur >= $level_access && is_numeric($vote) && $vote<=10 && $vote>=0) {
        if ($user) {
            $author = $user[2];
        } else {
            $author =  _VISITOR;
        } 

        $sql = mysql_query("SELECT ip FROM " . VOTE_TABLE . " WHERE vid = '" . $vid . "' AND module = '" . $module . "' AND ip = '" . $user_ip . "'");
        $count = mysql_num_rows($sql);

        if ($count > 0) {
            echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
               . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
               . "<head><title>" . _VOTEFROM . "&nbsp;" . $author . "</title>\n"
               . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
               . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
               . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
               . "<body style=\"background : " . $bgcolor2 . ";\">\n"
               . "<div style=\"text-align: center;\"><br /><br /><br />"  . _ALREADYVOTE .  "<br /><br /><br />\n"
               . "<a href=\"#\" onclick=\"javascript:window.close();\"><b>" . _CLOSEWINDOWS . "</b></a></b></div></body></html>";
        } else {
            $sql = mysql_query("INSERT INTO " . VOTE_TABLE . " ( `id` , `module` , `vid` , `ip` , `vote` ) VALUES ( '' , '" . $module . "' , '" . $vid . "' , '" . $user_ip . "' , '" . $vote . "' )");

            echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
               . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
               . "<head><title>" . _VOTEFROM . "&nbsp;" . $author . "</title>\n"
               . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
               . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
               . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
               . "<body style=\"background : " . $bgcolor2 . ";\">\n"
               . "<div style=\"text-align: center;\"><br /><br /><br />" . _VOTEADD . "<br /><br /><br />\n"
               . "<a href=\"#\" onclick=\"javascript:window.close();window.opener.document.location.reload(true);\"><b>" . _CLOSEWINDOWS . "</b></a></b></div></body></html>";
        }
    } else {
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
        . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
        . "<head><title>" . _VOTEFROM . "&nbsp;" . $author . "</title>\n"
        . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
        . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
        . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
        . "<body style=\"background : " . $bgcolor2 . ";\">\n"
        . "<div style=\"text-align: center;\"><br /><br /><br />" . _NOENTRANCE . "<br /><br /><br />\n"
        . "<a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></body></html>";
    }
} 

switch ($_REQUEST['op']) {
    case 'vote_index':
        vote_index($_REQUEST['module'], $_REQUEST['vid']);
        break;

    case 'post_vote':
        post_vote($_REQUEST['module'], $_REQUEST['vid']);
        break;

    case 'do_vote':
        do_vote($_REQUEST['vid'], $_REQUEST['module'], $_REQUEST['vote']);
        break;

    default:
        break;
} 

?>
