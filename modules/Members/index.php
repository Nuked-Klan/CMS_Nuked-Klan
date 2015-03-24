<?php 
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
if (!defined("INDEX_CHECK")){
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $language, $user;
translate("modules/Members/lang/" . $language . ".lang.php");

$visiteur = !$user ? 0 : $user[1];

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1){
    compteur("Members");

    function index(){
        global $bgcolor1, $bgcolor2, $bgcolor3, $theme, $nuked;

        $nb_membres = $nuked['max_members'];

        if ($_REQUEST['letter'] == "Autres"){
            $and = "AND pseudo NOT REGEXP '^[a-zA-Z].'";
        } 
        else if ($_REQUEST['letter'] != "" && preg_match("`^[A-Z]+$`", $_REQUEST['letter'])){
            $and = "AND pseudo LIKE '" . $_REQUEST['letter'] . "%'";
        } 
        else{
            $and = "";
        } 

        $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE team = '' AND niveau > 0 " . $and);
        $count = mysql_num_rows($sql2);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_membres - $nb_membres;

        opentable();

        echo "<br /><table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n"
				. "<tr><td align=\"center\"><br /><big><b>" . _SITEMEMBERS . "</b></big><br /><br /></td></tr>\n";

        $alpha = array ("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "" . _OTHER . "");

        echo "<tr><td align=\"center\"><small>[ <a href=\"index.php?file=Members\">" . _ALL . "</a> | ";

        $num = count($alpha) - 1;
        $counter = 0;
        while (list(, $lettre) = each($alpha)){
            echo "<a href=\"index.php?file=Members&amp;letter=" . $lettre . "\">" . $lettre . "</a>";

            if ($counter == round($num / 2)){
                echo " ]<br />[ ";
            } 
            else if ($counter != $num){
                echo " | ";
            } 

            $counter++;
        } 

        echo " ]</small><br /><br /></td></tr></table>";

        if ($count > $nb_membres){
            $url_members = "index.php?file=Members&amp;letter=" . $_REQUEST['letter'];
            number($count, $nb_membres, $url_members);
        } 

        echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
				. "<tr style=\"background: " . $bgcolor3 . ";\">\n"
				. "<td align=\"center\">&nbsp;</td>\n"
				. "<td align=\"center\"><b>" . _NICK . "</b></td>\n";
				if ($nuked['user_email'] == 'on'){echo "<td align=\"center\"><b>" . _MAIL . "</b></td>\n";}
				if ($nuked['user_icq'] == 'on'){echo "<td align=\"center\"><b>" . _ICQ . "</b></td>\n";}
				if ($nuked['user_msn'] == 'on'){echo "<td align=\"center\"><b>" . _MSN . "</b></td>\n";}
				if ($nuked['user_aim'] == 'on'){echo "<td align=\"center\"><b>" . _AIM . "</b></td>\n";}
				if ($nuked['user_yim'] == 'on'){echo "<td align=\"center\"><b>" . _YIM . "</b></td>\n";}
				if ($nuked['user_xfire'] == 'on'){echo "<td align=\"center\"><b>" . _XFIRE . "</b></td>\n";}
				if ($nuked['user_facebook'] == 'on'){echo "<td align=\"center\"><b>" . _FACEBOOK . "</b></td>\n";}
				if ($nuked['user_origin'] == 'on'){echo "<td align=\"center\"><b>" . _ORIGINEA . "</b></td>\n";}
				if ($nuked['user_steam'] == 'on'){echo "<td align=\"center\"><b>" . _STEAM . "</b></td>\n";}
				if ($nuked['user_twitter'] == 'on'){echo "<td align=\"center\"><b>" . _TWITTER . "</b></td>\n";}
				if ($nuked['user_skype'] == 'on'){echo "<td align=\"center\"><b>" . _SKYPE . "</b></td>\n";}
				if ($nuked['user_website'] == 'on'){echo "<td style=\" width=\"5%\";\" align=\"center\"><b>" . _URL . "</b></td>\n";}
				echo "</tr>\n";

        $sql = mysql_query("SELECT pseudo, url, email, icq, msn, aim, yim, rang, country, xfire, facebook ,origin, steam, twitter, skype FROM " . USER_TABLE . " WHERE team = '' " . $and . " AND niveau > 0 ORDER BY pseudo LIMIT " . $start . ", " . $nb_membres);
        while (list($pseudo, $url, $email, $icq, $msn, $aim, $yim, $rang, $country, $xfire, $facebook ,$origin, $steam, $twitter, $skype) = mysql_fetch_array($sql)){
            list ($pays, $ext) = explode ('.', $country);

            if ($url != "" && preg_match("`http://`i", $url)){
                $home = "<a href=\"" . $url . "\" title=\"" . $url . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"images/user/url.png\" alt=\"\" title=\"" . $url . "\" /></a>";
            } 

            if (is_file("themes/" . $theme . "/images/mail.png")){
                $img = "themes/" . $theme . "/images/mail.png";
            } 
            else{
                $img = "images/user/email.png";
            }

            if ($j == 0){
                $bg = $bgcolor2;
                $j++;
            } 
            else{
                $bg = $bgcolor1;
                $j = 0;
            } 

            echo "<tr style=\"background: " . $bg . ";\">\n"
					. "<td align=\"center\"><img src=\"images/flags/" . $country . "\" alt=\"\" title=\"" . $pays . "\" /></td>\n"
					. "<td><a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($pseudo) . "\" title=\"" . _VIEWPROFIL . "\"><b>" . $pseudo . "</b></a></td>\n";
			if ($nuked['user_email'] == 'on')
		{
			echo "<td align=\"center\">\n";

            if ($email != ""){
                echo "<a href=\"mailto:" . $email . "\"><img style=\"border: 0;\" src=\"" . $img . "\" alt=\"\" title=\"" . $email . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/emailna.png\" alt=\"\"/></td>";
            } 
		}
            if ($nuked['user_icq'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($icq != ""){
                echo "<a href=\"http://web.icq.com/whitepages/add_me?uin=" . $icq . "&amp;action=add\"><img style=\"border: 0;\" src=\"images/user/icq.png\" alt=\"\" title=\"" . $icq . "\" /></a></td>";
            } 
            else{
                echo"<img style=\"border: 0;\" src=\"images/user/icqna.png\" alt=\"\"/></td>";
            } 
		}
		    if ($nuked['user_msn'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($msn != ""){
                echo "<a href=\"mailto:" . $msn . "\"><img style=\"border: 0;\" src=\"images/user/msn.png\" alt=\"\" title=\"" . $msn . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/msnna.png\" alt=\"\"/></td>";
            } 
		}
		    if ($nuked['user_aim'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($aim != ""){
                echo "<a href=\"aim:goim?screenname=" . $aim . "&amp;message=Hi+" . $aim . "+Are+you+there+?\"><img style=\"border: 0;\" src=\"images/user/aim.png\" alt=\"\" title=\"" . $aim . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/aimna.png\" alt=\"\"/></td>";
            } 
		}
		    if ($nuked['user_yim'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($yim != ""){
                echo "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $yim . "&amp;.src=pg\"><img style=\"border: 0;\" src=\"images/user/yahoo.png\" alt=\"\" title=\"" . $yim . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/yahoona.png\" alt=\"\"/></td>";
            } 
		}
		
			if ($nuked['user_xfire'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($xfire != ""){
                echo "<a href=\"xfire:add_friend?user=" . $xfire . "\"><img style=\"border: 0;\" src=\"images/user/xfire.png\" alt=\"\" title=\"" . $xfire . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/xfirena.png\" alt=\"\"/></td>";
            } 
		}
		
			if ($nuked['user_facebook'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($facebook != ""){
                echo "<a href=\"http://www.facebook.com/" . $facebook . "\"><img style=\"border: 0;\" src=\"images/user/facebook.png\" alt=\"\" title=\"" . $facebook . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/facebookna.png\" alt=\"\"/></td>";
            } 
		}
		
			if ($nuked['user_origin'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($origin != ""){
                echo "<img style=\"border: 0;\" src=\"images/user/origin.png\" alt=\"\" title=\"" . $origin . "\" /></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/originna.png\" alt=\"\"/></td>";
            } 
		}
		
			if ($nuked['user_steam'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($steam != ""){
                echo "<a href=\"http://steamcommunity.com/actions/AddFriend/" . $steam . "\"><img style=\"border: 0;\" src=\"images/user/steam.png\" alt=\"\" title=\"" . $steam . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/steamna.png\" alt=\"\"/></td>";
            } 
		}
		
			if ($nuked['user_twitter'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($twitter != ""){
                echo "<a href=\"http://twitter.com/#!/" . $twitter . "\"><img style=\"border: 0;\" src=\"images/user/twitter.png\" alt=\"\" title=\"" . $twitter . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/twitterna.png\" alt=\"\"/></td>";
            } 
		}
		
			if ($nuked['user_skype'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($skype != ""){
                echo "<a href=\"skype:" . $skype . "?call\"><img style=\"border: 0;\" src=\"images/user/skype.png\" alt=\"\" title=\"" . $skype . "\" /></a></td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/skypena.png\" alt=\"\"/></td>";
            } 
		}
		
			if ($nuked['user_website'] == 'on')
		{
            echo "<td align=\"center\">\n";

            if ($url != ""){
				
                echo "" . $home . "</td>";
            } 
            else{
                echo "<img style=\"border: 0;\" src=\"images/user/urlna.png\" alt=\"\"/></td>";
            } 
		}
        } 

        if ($count == 0){
            echo "<tr><td colspan=\"8\" align=\"center\">" . _NOMEMBERS . "</td></tr>\n";
        }
		
        echo "</table>";

        if ($count > $nb_membres){
            $url_members = "index.php?file=Members&amp;letter=" . $_REQUEST['letter'];
            number($count, $nb_membres, $url_members);
        } 

        $date_install = nkDate($nuked['date_install']);

        if ($_REQUEST['letter'] != ""){
            $_REQUEST['letter'] = nkHtmlEntities($_REQUEST['letter']);
            $_REQUEST['letter'] = nk_CSS($_REQUEST['letter']);

            echo "<br /><div style=\"text-align: center;\">" . $count . "&nbsp;" . _MEMBERSFOUND . " <b>" . $_REQUEST['letter'] . "</b></div><br />\n";
        } 
        else{
            echo "<br /><div style=\"text-align: center;\">" . _THEREARE . "&nbsp;" . $count . "&nbsp;" . _MEMBERSREG . "&nbsp;" . $date_install . "<br />\n";

            if ($count > 0){
                $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE team = '' ORDER BY date DESC LIMIT 0, 1");
                list($member) = mysql_fetch_array($sql_member);
                echo _LASTMEMBER . " <a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($member) . "\"><b>" . $member . "</b></a></div><br />\n";
            } 
            else{
                echo "</div><br />\n";
            } 
	}

        closetable();
    } 

    function detail($autor){
        global $nuked, $bgcolor1, $bgcolor2, $bgcolor3, $user, $visiteur;

        opentable();

        $autor = htmlentities($autor, ENT_QUOTES, 'ISO-8859-1' );

        $sql = mysql_query("SELECT U.id, U.icq, U.msn, U.aim, U.yim, U.email, U.url, U.date, U.game, U.country, U.xfire, U.facebook , U.origin, U.steam, U.twitter, U.skype, S.date FROM " . USER_TABLE . " AS U LEFT OUTER JOIN " . SESSIONS_TABLE . " AS S ON U.id = S.user_id WHERE U.pseudo = '" . $autor . "'");
        $test = mysql_num_rows($sql);

        if ($test > 0){
            list($id_user, $icq, $msn, $aim, $yim, $email, $url, $date, $game_id, $country, $xfire, $facebook, $origin, $steam, $twitter, $skype, $last_used) = mysql_fetch_array($sql);
            list ($pays, $ext) = explode ('.', $country);

            if ($email != ""){
                $mail = "<a href=\"mailto:" . $email . "\">" . $email . "</a>";
            } 
            else{
                $mail = "";
            } 

            $sql2 = mysql_query("SELECT prenom, age, sexe, ville, motherboard, cpu, ram, video, resolution, son, ecran, souris, clavier, connexion, system, photo, pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . USER_DETAIL_TABLE . " WHERE user_id = '" . $id_user . "'");
            list($prenom, $birthday, $sexe, $ville, $motherboard, $cpu, $ram, $video, $resolution, $sons, $ecran, $souris, $clavier, $connexion, $osystem, $photo, $pref1, $pref2, $pref3, $pref4, $pref5) = mysql_fetch_array($sql2);

            $sql3 = mysql_query("SELECT titre, pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . GAMES_TABLE . " WHERE id = '" . $game_id . "'");
            list($titre, $pref_1, $pref_2, $pref_3, $pref_4, $pref_5) = mysql_fetch_array($sql3);
	    
			$date = nkDate($date);
			$last_used > 0 ? $last_used=nkDate($last_used) : $last_used='';            

            $titre = nkHtmlEntities($titre);
            $pref_1 = nkHtmlEntities($pref_1);
            $pref_2 = nkHtmlEntities($pref_2);
            $pref_3 = nkHtmlEntities($pref_3);
            $pref_4 = nkHtmlEntities($pref_4);
            $pref_5 = nkHtmlEntities($pref_5);

            if ($birthday != ""){
                list ($jour, $mois, $an) = explode ('/', $birthday);
                $age = date("Y") - $an;
				
                if (date("m") < $mois){
                    $age = $age - 1;
                }
				
                if (date("d") < $jour && date("m") == $mois){
                    $age = $age - 1;
                } 
            } 
            else{
                $age = "";
            } 

            if ($sexe == "male"){
              $sex = _MALE;
            } 
            else if ($sexe == "female"){
                $sex = _FEMALE;
            } 
            else{
                $sex = "";
            } 

            if ($visiteur == 9){
               echo "<div style=\"text-align: right;\"><a href=\"index.php?file=Admin&amp;page=user&amp;op=edit_user&amp;id_user=" . $id_user . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>";
            
	            if ($id_user != $user[0]){
	                echo "<script type=\"text/javascript\">\n"
							."<!--\n"
							."\n"
							. "function deluser(pseudo, id)\n"
							. "{\n"
							. "if (confirm('" . _DELETEUSER . " '+pseudo+' ! " . _CONFIRM . "'))\n"
							. "{document.location.href = 'index.php?file=Admin&page=user&op=del_user&id_user='+id;}\n"
							. "}\n"
							. "\n"
							. "// -->\n"
							. "</script>\n";

	            	echo "<a href=\"javascript:deluser('" . mysql_real_escape_string(stripslashes($autor)) . "', '" . $id_user . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DELETE . "\" /></a>";
	            }
				
			echo "&nbsp;</div>\n";
			} 

            $a = "¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Ò";
            $b = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
            $flash_autor = @nkHtmlEntityDecode($autor);
            $flash_autor = strtr($flash_autor, $a, $b);

            echo "<br /><object type=\"application/x-shockwave-flash\" data=\"modules/Members/images/title.swf\" width=\"100%\" height=\"50\">\n"
					. "<param name=\"movie\" value=\"modules/Members/images/title.swf\" />\n"
					. "<param name=\"pluginurl\" value=\"http://www.macromedia.com/go/getflashplayer\" />\n"
					. "<param name=\"wmode\" value=\"transparent\" />\n"
					. "<param name=\"menu\" value=\"false\" />\n"
					. "<param name=\"quality\" value=\"best\" />\n"
					. "<param name=\"scale\" value=\"exactfit\" />\n"
					. "<param name=\"flashvars\" value=\"text=" . $flash_autor . "\" /></object>\n";

			echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
					."<tr style=\"background: " . $bgcolor3 . ";\"><td style=\"height: 20px\" colspan=\"2\" align=\"center\"><big><b>" . _INFOPERSO . "</b></big></td></tr>\n"
					."<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"width: 100%\"><table cellpadding=\"1\" cellspacing=\"1\">\n"
					."<tr><td><b>&nbsp;&nbsp;ª " . _NICK . "&nbsp;:&nbsp;</b></td><td><img src=\"images/flags/" . $country . "\" alt=\"" . $pays . "\" />&nbsp;" . $autor . "</td></tr>\n";
			
			if ($prenom) echo "<tr><td><b>&nbsp;&nbsp;ª " . _LASTNAME . "&nbsp;:&nbsp;</b></td><td>" . $prenom . "</td></tr>\n";
			if ($age) echo "<tr><td><b>&nbsp;&nbsp;ª " . _AGE . "&nbsp;:&nbsp;</b></td><td>" . $age . "</td></tr>\n";
			if ($sex) echo "<tr><td><b>&nbsp;&nbsp;ª " . _SEXE . "&nbsp;:&nbsp;</b></td><td>" . $sex . "</td></tr>\n";
			if ($ville) echo "<tr><td><b>&nbsp;&nbsp;ª " . _CITY . "&nbsp;:&nbsp;</b></td><td>" . $ville . "</td></tr>\n";
			if ($pays) echo "<tr><td><b>&nbsp;&nbsp;ª " . _COUNTRY . "&nbsp;:&nbsp;</b></td><td>" . $pays . "</td></tr>\n";
			
if ($visiteur >= $nuked['user_social_level'] )
{			
			if ($mail) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MAIL . "&nbsp;:&nbsp;</b></td><td>" . $mail . "</td></tr>\n";
			if ($url && preg_match("`http://`i", $url)) echo "<tr><td><b>&nbsp;&nbsp;ª " . _URL . "&nbsp;:&nbsp;</b></td><td><a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . $url . "</a></td></tr>\n";
			if ($icq) echo "<tr><td><b>&nbsp;&nbsp;ª " . _ICQ . "&nbsp;:&nbsp;</b></td><td><a href=\"http://web.icq.com/whitepages/add_me?uin=" . $icq . "&amp;action=add\">" . $icq . "</a></td></tr>"; 
			if ($msn) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MSN . "&nbsp;:&nbsp;</b></td><td><a href=\"mailto:" . $msn . "\">" . $msn . "</a></td></tr>";
			if ($aim) echo "<tr><td><b>&nbsp;&nbsp;ª " . _AIM . "&nbsp;:&nbsp;</b></td><td><a href=\"aim:goim?screenname=" . $aim . "&amp;message=Hi+" . $aim . "+Are+you+there+?\">" . $aim . "</a></td></tr>";                
			if ($yim) echo "<tr><td><b>&nbsp;&nbsp;ª " . _YIM . "&nbsp;:&nbsp;</b></td><td><a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $yim . "&amp;.src=pg\">" . $yim . "</a></td></tr>";
			if ($xfire) echo "<tr><td><b>&nbsp;&nbsp;ª " . _XFIRE . "&nbsp;:&nbsp;</b></td><td><a href=\"xfire:add_friend?user=" . $xfire . "\">" . $xfire . "</a></td></tr>";
			if ($facebook) echo "<tr><td><b>&nbsp;&nbsp;ª " . _FACEBOOK . "&nbsp;:&nbsp;</b></td><td><a href=\"http://www.facebook.com/" . $facebook . "\">" . $facebook . "</a></td></tr>";
			if ($origin) echo "<tr><td><b>&nbsp;&nbsp;ª " . _ORIGINEA . "&nbsp;:&nbsp;</b></td><td><a href=\"#\">" . $origin. "</a></td></tr>";
			if ($steam) echo "<tr><td><b>&nbsp;&nbsp;ª " . _STEAM . "&nbsp;:&nbsp;</b></td><td><a href=\"#\">" . $steam . "</a></td></tr>";
			if ($twitter) echo "<tr><td><b>&nbsp;&nbsp;ª " . _TWITTER . "&nbsp;:&nbsp;</b></td><td><a href=\"http://twitter.com/#!/" . $twitter . "\">" . $twitter . "</a></td></tr>";
			if ($skype) echo "<tr><td><b>&nbsp;&nbsp;ª " . _SKYPE . "&nbsp;:&nbsp;</b></td><td><a href=\"skype:" . $skype . "?call\">" . $skype . "</a></td></tr>";
 }
			if ($date) echo "<tr><td><b>&nbsp;&nbsp;ª " . _DATEUSER . "&nbsp;:&nbsp;</b></td><td>" . $date . "</td></tr>";
			if ($last_used) echo "<tr><td><b>&nbsp;&nbsp;ª " . _LASTVISIT . "&nbsp;:&nbsp;</b></td><td>" . $last_used . "</td></tr>";
			
			echo "</table></td><td style=\"padding: 5px;\" align=\"right\">\n";
			
			if ($photo != ""){
				echo "<img style=\"border: 1px solid " . $bgcolor3 . "; background:" . $bgcolor1 . "; padding: 2px; overflow: auto; max-width: 100px;  width: expression(this.scrollWidth >= 100? '100px' : 'auto');\" src=\"" . checkimg($photo) . "\" alt=\"\" />";
			} 
			else{
				echo "<img src=\"modules/Members/images/pas_image.jpg\" width=\"100\" alt=\"\" style=\"border: 1px solid " . $bgcolor3 . "; background:" . $bgcolor1 . "; padding: 2px;\" />";
			}
			

			
			if ( $cpu || $ram || $motherboard || $video || $resolution || $sons || $souris || $clavier || $ecran || $osystem || $connexion ){
				echo "<tr style=\"background: " . $bgcolor3 . ";\"><td colspan=\"2\" style=\"height: 20px\" align=\"center\"><big><b>" . _HARDCONFIG . "</b></big></td></tr>\n"
						."<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"width: 100%\" colspan=\"2\"><table cellpadding=\"1\" cellspacing=\"1\">\n";
				
				if ($cpu) echo "<tr><td><b>&nbsp;&nbsp;ª " . _PROCESSOR . "&nbsp;:&nbsp;</b></td><td>" . $cpu . "</td></tr>\n";
				if ($ram) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MEMORY . "&nbsp;:&nbsp;</b></td><td>" . $ram . "</td></tr>\n";
				if ($motherboard) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MOTHERBOARD . "&nbsp;:&nbsp;</b></td><td>" . $motherboard . "</td></tr>\n";
				if ($video) echo "<tr><td><b>&nbsp;&nbsp;ª " . _VIDEOCARD . "&nbsp;:&nbsp;</b></td><td>" . $video . "</td></tr>\n";
				if ($resolution) echo "<tr><td><b>&nbsp;&nbsp;ª " . _RESOLUTION . "&nbsp;:&nbsp;</b></td><td>" . $resolution . "</td></tr>\n";
				if ($sons) echo "<tr><td><b>&nbsp;&nbsp;ª " . _SOUNDCARD . "&nbsp;:&nbsp;</b></td><td>" . $sons . "</td></tr>\n";
				if ($souris) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MOUSE . "&nbsp;:&nbsp;</b></td><td>" . $souris . "</td></tr>\n";
				if ($clavier) echo "<tr><td><b>&nbsp;&nbsp;ª " . _KEYBOARD . "&nbsp;:&nbsp;</b></td><td>" . $clavier . "</td></tr>\n";
				if ($ecran) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MONITOR . "&nbsp;:&nbsp;</b></td><td>" . $ecran . "</td></tr>\n";
				if ($osystem) echo "<tr><td><b>&nbsp;&nbsp;ª " . _SYSTEMOS . "&nbsp;:&nbsp;</b></td><td>" . $osystem . "</td></tr>\n";
				if ($connexion) echo "<tr><td><b>&nbsp;&nbsp;ª " . _CONNECT . "&nbsp;:&nbsp;</b></td><td>" . $connexion . "</td></tr>\n";
				
				echo "</table></td></tr>\n";
			}
			
			if ( $pref1 || $pref2 || $pref3 || $pref4 || $pref5 ){
				echo "<tr style=\"background: " . $bgcolor3 . ";\"><td colspan=\"2\" style=\"height: 20px\" align=\"center\"><big><b>" . $titre . " :</b></big></td></tr>\n";
				echo "<tr style=\"background: " . $bgcolor1 . ";\"><td colspan=\"2\"><table cellpadding=\"1\" cellspacing=\"1\">\n";
				
				if ($pref1) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_1 . "&nbsp;:&nbsp;</b></td><td>" . $pref1 . "</td></tr>\n";
				if ($pref2) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_2 . "&nbsp;:&nbsp;</b></td><td>" . $pref2 . "</td></tr>\n";
				if ($pref3) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_3 . "&nbsp;:&nbsp;</b></td><td>" . $pref3 . "</td></tr>\n";
				if ($pref4) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_4 . "&nbsp;:&nbsp;</b></td><td>" . $pref4 . "</td></tr>\n";
				if ($pref5) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_5 . "&nbsp;:&nbsp;</b></td><td>" . $pref5 . "</td></tr>\n";
				
				echo "</table>";
			}
			
			echo "</td></tr></table><br />\n"
					."<br /><div style=\"text-align: center;\">\n";
			
            if ($user){
                echo "&nbsp;[&nbsp;<a href=\"index.php?file=Userbox&amp;op=post_message&amp;for=" . $id_user . "\">" . _SENDPV . "</a>&nbsp;]&nbsp;\n";
            }
			
			echo "&nbsp;[&nbsp;<a href=\"index.php?file=Search&amp;op=mod_search&amp;autor=" . $autor . "\">" . _FINDSTUFF . "</a>&nbsp;]&nbsp;</div><br />\n";
        }
        else{
            echo "<br /><br /><div style=\"text-align: center;\">" . _NOMEMBER . "</div><br /><br />\n";
        } 

        closetable();
    } 
	
	function listing($q,$type='right',$limit=100){
		$q	= strtolower($q);
		$q = nk_CSS($q);
		$q = htmlentities($q, ENT_QUOTES, 'ISO-8859-1' );	
		if (!$q) return;
		
		if (!is_numeric($limit)) $limit = 0;
		if ($limit > 0) $str_limit = "LIMIT 0," . $limit;
		else $str_limit = '';
		
		if ($type=='full') $left = '%';
		else $left = '';
		
		$req_list = "SELECT pseudo FROM " . USER_TABLE . " WHERE lower(pseudo) like '" . $left . $q . "%' ORDER BY pseudo DESC " . $str_limit;
		$sql_list = mysql_query($req_list);
		
		while (list($pseudo) = mysql_fetch_array($sql_list)){
			$pseudo = str_replace('|','',$pseudo);
			echo $pseudo . "\n";
		}
	}

    switch ($_REQUEST['op']){
        case"index":
        index();
        break;

        case"detail":
        detail($_REQUEST['autor']);
        break;        
		
		case"list":
        listing($_REQUEST['q'],$_REQUEST['type'],$_REQUEST['limit']);
        break;

        default:
		index();
    } 
} 
else if ($level_access == -1){
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    closetable();
} 
else if ($level_access == 1 && $visiteur == 0){
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | "
    . "<a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
    closetable();
} 
else{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    closetable();
} 
?>