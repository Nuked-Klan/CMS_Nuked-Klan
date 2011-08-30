<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")) die ('<div style="text-align: center;">You cannot open this page directly</div>');

global $nuked, $language, $user;
translate("modules/Irc/lang/" . $language . ".lang.php");
$visiteur = (!$user) ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);

if ($visiteur >= $level_access && $level_access > -1){
    compteur("Irc");

    function index(){
        global $nuked;

        opentable();

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _CHANIRC . "</b></big><br /><br />\n"
				. _JOINCHAN . " #" . $nuked['irc_chan'] . "&nbsp;" . _ON . " irc." . $nuked['irc_serv'] . " :<br /><br />\n"
				. "<a href=\"irc://irc." . $nuked['irc_serv'] . "/" . $nuked['irc_chan'] . "\" title=\"" . _CONNEXSOFT . "\"><b>mIRC</b></a>\n"
				. " | <a href=\"#\" onclick=\"window.open('index.php?file=Irc&amp;nuked_nude=index&amp;op=join_chan','nom','toolbar=1,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=600,height=400,top=30,left=0');return(false)\" title=\"" . _CONNEXBYSITE . "\"><b>PJIRC</b></a>\n"
				. "<br /><br />[ <a href=\"index.php?file=Irc&amp;op=awards\">" . _SEEAWARDS . "</a> ]</div><br />\n";

        closetable();
    } 

    function join_chan(){
        global $user, $theme, $bgcolor2, $bgcolor3;

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
				. "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
				. "<head><title>" . $nuked['name'] . " :: " . $nuked['slogan'] . " ::</title>\n"
				. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
				. "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
				. "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
				. "<body style=\"background: " . $bgcolor2 . ";\">\n"
				. "<form method=\"post\" action=\"index.php?file=Irc&amp;nuked_nude=index&amp;op=chat\">\n"
				. "<table style=\"height: 370px;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\">\n"
				. "<tr><td align=\"center\" valign=\"middle\"><table><tr><td><b>" . _YOURNICK . " :</b> <input type=\"text\" name=\"nick\" size=\"20\" value=\"" . $user[2] . "\" /></td></tr>\n"
				. "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _ENTER . "\" /></td></tr></table></td></tr></table></form></body></html>";
    } 

    function chat($nick){
        global $bgcolor2, $nuked, $language, $theme;

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
				. "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
				. "<head><title>" . $nuked['name'] . " :: " . $nuked['slogan'] . " ::</title>\n"
				. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
				. "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
				. "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
				. "<body style=\"background: " . $bgcolor2 . ";\">\n"
				. "<div style=\"text-align: center;\"><br />\n"
				. "<applet name=\"applet\" code=IRCApplet.class archive=\"irc.jar,pixx.jar\" width=\"100%\" height=\"80%\">\n"
				. "<param name=\"CABINETS\" value=\"irc.cab,securedirc.cab,pixx.cab\" />\n"
				. "<param name=\"code\" value=\"IRCApplet.class\" />\n"
				. "<param name=\"archive\" value=\"irc.jar,pixx.jar\" />\n"
				. "<param name=\"codebase\" value=\"modules/Irc/pjirc/\" />\n"
				. "<param name=\"nick\" value=\"" . $nick . "\" />\n"
				. "<param name=\"alternatenick\" value=\"" . htmlentities($nick, ENT_QUOTES) . "\" />\n"
				. "<param name=\"name\" value=\"Java User\" />\n"
				. "<param name=\"host\" value=\"irc." . $nuked['irc_serv'] . "\" />\n"
				. "<param name=\"port\" value=\"6667\" />\n"
				. "<param name=\"command1\" value=\"/join #" . $nuked['irc_chan'] . "\" />\n"
				. "<param name=\"gui\" value=\"pixx\" />\n"
				. "<param name=\"language\" value=\"" . $language . "\" />\n"
				. "<param name=\"pixx:timestamp\" value=\"true\" />\n"
				. "<param name=\"pixx:highlight\" value=\"true\" />\n"
				. "<param name=\"pixx:highlightnick\" value=\"true\" />\n"
				. "<param name=\"pixx:nickfield\" value=\"true\" />\n"
				. "<param name=\"pixx:styleselector\" value=\"true\" />\n"
				. "<param name=\"pixx:setfontonstyle\" value=\"true\" />\n"
				. "<param name=\"style:bitmapsmileys\" value=\"true\" />\n"
				. "<param name=\"style:smiley1\" value=\":) img/sourire.gif\" />\n"
				. "<param name=\"style:smiley2\" value=\":-) img/sourire.gif\" />\n"
				. "<param name=\"style:smiley3\" value=\":-D img/content.gif\" />\n"
				. "<param name=\"style:smiley4\" value=\":d img/content.gif\" />\n"
				. "<param name=\"style:smiley5\" value=\":-O img/OH-2.gif\" />\n"
				. "<param name=\"style:smiley6\" value=\":o img/OH-1.gif\" />\n"
				. "<param name=\"style:smiley7\" value=\":-P img/langue.gif\" />\n"
				. "<param name=\"style:smiley8\" value=\":p img/langue.gif\" />\n"
				. "<param name=\"style:smiley9\" value=\";-) img/clin-oeuil.gif\" />\n"
				. "<param name=\"style:smiley10\" value=\";) img/clin-oeuil.gif\" />\n"
				. "<param name=\"style:smiley11\" value=\":-( img/triste.gif\" />\n"
				. "<param name=\"style:smiley12\" value=\":( img/triste.gif\" />\n"
				. "<param name=\"style:smiley13\" value=\":-| img/OH-3.gif\" />\n"
				. "<param name=\"style:smiley14\" value=\":| img/OH-3.gif\" />\n"
				. "<param name=\"style:smiley15\" value=\":'( img/pleure.gif\" />\n"
				. "<param name=\"style:smiley16\" value=\":$ img/rouge.gif\" />\n"
				. "<param name=\"style:smiley17\" value=\":-$ img/rouge.gif\" />\n"
				. "<param name=\"style:smiley18\" value=\"(H) img/cool.gif\" />\n"
				. "<param name=\"style:smiley19\" value=\"(h) img/cool.gif\" />\n"
				. "<param name=\"style:smiley20\" value=\":-@ img/enerve1.gif\" />\n"
				. "<param name=\"style:smiley21\" value=\":@ img/enerve2.gif\" />\n"
				. "<param name=\"style:smiley22\" value=\":-S img/roll-eyes.gif\" />\n"
				. "<param name=\"style:smiley23\" value=\":s img/roll-eyes.gif\" />\n"
				. "</applet>\n";

        $smiley[':)'] = "sourire.gif";
        $smiley[':d'] = "content.gif";
        $smiley[':o'] = "OH-2.gif";
        $smiley[':p'] = "langue.gif";
        $smiley[';)'] = "clin-oeuil.gif";
        $smiley[':('] = "triste.gif";
        $smiley[':|'] = "OH-3.gif";
        $smiley[':$'] = "rouge.gif";
        $smiley['(h)'] = "cool.gif";
        $smiley[':@'] = "enerve2.gif";
        $smiley[':S'] = "roll-eyes.gif";

        echo "&nbsp;<b>" . _SMILIES . " :</b>&nbsp;";

        foreach ($smiley as $i => $sm){
            echo "<a href=\"javascript:document.applet.setFieldText(document.applet.getFieldText()+'" . $i . "');document.applet.requestSourceFocus()\" title=\"" . $i . "\"><img style=\"border: 0;\" src=\"modules/Irc/pjirc/img/" . $sm . "\" alt=\"\" /></a>&nbsp;";
        }
		
		echo "</div></body></html>";
    } 

    function awards(){
        opentable();

        global $nuked;

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _CHANIRC . " - " . _AWARDS . "</b></big></div><br /><br />\n"
				. "<div style=\"width: 90%;text-align: left;\">\n";

        $i = 0;
        $sql = mysql_query("SELECT date, text FROM " . IRC_AWARDS_TABLE . " ORDER BY id DESC");
        $count = mysql_num_rows($sql);
        while (list($date, $txt) = mysql_fetch_array($sql)){
            $date = nkDate($date);
            $i++;

            echo "&nbsp;<b><big>&middot;</big></b>&nbsp;<b>" . $date . "</b><br />" . $txt . "<br /><br />\n";

            if ($count > $i){
                echo "<hr style=\"height: 1px;\" />\n";
            } 
        } 

        if ($count == 0){
            echo "<div style=\"width: 100%;text-align: center;\">" . _NOAWARD . "</div>";
        } 

        echo "</div><br /><br />\n";
        closetable();
    } 

    switch ($_REQUEST['op']){
        case"index":
            index();
            break;

        case"join_chan":
            join_chan();
            break;

        case"chat":
            chat($_REQUEST['nick']);
            break;

        case"awards":
            awards();
            break;

        default:
            index();
            break;
    } 
} 
else if ($level_access == -1){
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    closetable();
} 
else if ($level_access == 1 && $visiteur == 0){
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
    closetable();
} 
else{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    closetable();
} 
?>