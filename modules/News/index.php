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

global $nuked, $language, $user;
translate("modules/News/lang/" . $language . ".lang.php");

// Inclusion système Captcha
include_once("Includes/nkCaptcha.php");

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == "off") $captcha = 0;
else if (_NKCAPTCHA == "auto" && $user[1] > 0)  $captcha = 0;
else $captcha = 1;

if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    compteur("News");

    function index()
    {
        global $nuked, $language;

        $max_news = $nuked['max_news'];
        $day = time();

        if ($_REQUEST['op'] == "categorie")
        {
            $where = "WHERE cat = '" . $_REQUEST['cat_id'] . "' AND " . $day . " >= date";
        }
        else if ($_REQUEST['op'] == "suite" || $_REQUEST['op'] == "index_comment")
        {
            $where = "WHERE id = '" . $_REQUEST['news_id'] . "' AND " . $day . " >= date";
        }
        else
        {
            $where = "WHERE " . $day . " >= date";
        }

        $sql_nbnews = mysql_query("SELECT id FROM " . NEWS_TABLE . " " . $where);
        $nb_news = mysql_num_rows($sql_nbnews);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $max_news - $max_news;

        if ($_REQUEST['op'] == "categorie")
        {
            $sql = mysql_query("SELECT id, auteur, auteur_id, date, titre, texte, suite, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $_REQUEST['cat_id'] . "' AND " . $day . " >= date ORDER BY date DESC LIMIT " . $start . ", " . $max_news);
        }

        else if ($_REQUEST['op'] == "suite")
        {
            $sql = mysql_query("SELECT id, auteur, auteur_id, date, titre, texte, suite, cat FROM " . NEWS_TABLE . " WHERE id = '" . $_REQUEST['news_id'] . "'");
        }

        else if ($_REQUEST['op'] == "index_comment")
        {
            $sql = mysql_query("SELECT id, auteur, auteur_id, date, titre, texte, suite, cat FROM " . NEWS_TABLE . " WHERE id = '" . $_REQUEST['news_id'] . "'");
        }

        else
        {
            $sql = mysql_query("SELECT id, auteur, auteur_id, date, titre, texte, suite, cat FROM " . NEWS_TABLE . " WHERE " . $day . " >= date ORDER BY date DESC LIMIT " . $start . ", " . $max_news);
        }
		if(mysql_num_rows($sql) <= 0)
		{
			redirect("index.php?file=404", 0);
			exit();
		}
        while (list($nid, $autor, $autor_id, $date, $titre, $texte, $suite, $cid) = mysql_fetch_array($sql))
        {
            $titre = htmlentities($titre);

            $sql2 = mysql_query("SELECT im_id FROM " . COMMENT_TABLE . " WHERE im_id = '" . $nid . "' AND module = 'news'");
            $nb_comment = mysql_num_rows($sql2);

            $sql3 = mysql_query("SELECT titre, image FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cid . "'");
            list($categorie, $image) = mysql_fetch_array($sql3);

            if ($autor_id != "")
            {
            $sql4 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            $test = mysql_num_rows($sql4);
            }

            if ($autor_id != "" && $test > 0)
            {
            list($auteur) = mysql_fetch_array($sql4);
            }
            else
            {
                $auteur = $autor;
            }

            if ($language == "french")
            {
                $data['date'] = strftime("%A %d %B %Y", $date);
            }
            else
            {
                $data['date'] = strftime("%A %B %d %Y", $date);
            }
			$data['cat'] = $categorie;
			$data['catid'] = $cid;
            $data['id'] = $nid;
            $data['titre'] = $titre;
            $data['auteur'] = $auteur;
            $data['heure'] = strftime("%H:%M", $date);
            $data['nb_comment'] = $nb_comment;
            $data['printpage'] = "<a href=\"index.php?file=News&amp;nuked_nude=index&amp;op=pdf&amp;news_id=" . $nid . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"images/pdf.gif\" alt=\"\" title=\"" . _PDF . "\" /></a>";
            $data['friend'] = "<a href=\"index.php?file=News&amp;op=sendfriend&amp;news_id=" . $nid . "\"><img style=\"border: 0;\" src=\"images/friend.gif\" alt=\"\" title=\"" . _FSEND . "\" /></a>";

            if ($image != "")
            {
                $data['image'] = "<a href=\"index.php?file=News&amp;op=categorie&amp;cat_id=" . $cid . "\"><img style=\"float: right;border: 0;\" src=\"" . $image . "\" alt=\"\" title=\"" . $categorie . "\" /></a>";
            }
            else
            {
                $data['image'] = "";
            }

            if ($_REQUEST['op'] == "suite" || $_REQUEST['op'] == "index_comment" && $suite != "")
            {
                $data['texte'] = $texte . "<br /><br />" . $suite;
            }

            else if ($suite != "")
            {
                $data['texte'] = $texte . "<div style=\"text-align: right;\"><a href=\"index.php?file=News&amp;op=suite&amp;news_id=" . $nid . "\">" . _READMORE . "</a></div>";
            }

            else
            {
                $data['texte'] = $texte;
            }

            news($data);
        }
        if ($_REQUEST['op'] == "categorie")
        {
            $url = "index.php?file=News&amp;op=categorie&amp;cat_id=" . $_REQUEST['cat_id'];
        }
        else
        {
            $url = "index.php?file=News";
        }
        if ($nb_news > $max_news)
        {
            echo "&nbsp;";
            number($nb_news, $max_news, $url);
            echo "<br /><br />";
        }
    }

    function index_comment($news_id)
    {
        global $user, $visiteur, $nuked;

        if ($visiteur >= admin_mod("News"))
        {
            echo"<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function delnews(id)\n"
            . "{\n"
            . "if (confirm('" . _DELTHISNEWS . " ?'))\n"
            . "{document.location.href = 'index.php?file=News&page=admin&op=do_del&news_id='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

            echo "<div style=\"text-align: right;\"><a href=\"index.php?file=News&amp;page=admin&amp;op=edit&amp;news_id=" . $news_id . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>"
            . "&nbsp;<a href=\"javascript:delnews('" . $news_id . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DEL . "\" /></a></div>\n";
        }

        index();
        $sql = mysql_query("SELECT active FROM " . $nuked['prefix'] . "_comment_mod WHERE module = 'news'");
		list($active) = mysql_fetch_array($sql);

		if($active ==1)
		{
			include ("modules/Comment/index.php");
			com_index("news", $news_id);
		}
    }

    function suite($news_id)
    {
        global $user, $visiteur, $nuked;

        if ($visiteur >= admin_mod("News"))
        {
            echo"<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function delnews(id)\n"
            . "{\n"
            . "if (confirm('" . _DELTHISNEWS . " ?'))\n"
            . "{document.location.href = 'index.php?file=News&page=admin&op=do_del&news_id='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

            echo "<div style=\"text-align: right;\"><a href=\"index.php?file=News&amp;page=admin&amp;op=edit&amp;news_id=" . $news_id . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>"
            . "&nbsp;<a href=\"javascript:delnews('" . $news_id . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DEL . "\" /></a></div>\n";
        }

        index();
		$sql = mysql_query("SELECT active FROM " . $nuked['prefix'] . "_comment_mod WHERE module = 'news'");
		list($active) = mysql_fetch_array($sql);

		if($active ==1)
		{
			include ("modules/Comment/index.php");
			com_index("news", $news_id);
		}

    }

    function categorie($cat_id)
    {
        index();
    }

    function sujet()
    {
        global $nuked;

        opentable();

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _SUBJECTNEWS . "</b></big></div><br /><br />\n"
		. "<table cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n";

        $sql = mysql_query("SELECT nid, titre, description, image FROM " . NEWS_CAT_TABLE . " ORDER BY titre");
        while (list($id, $titre, $description, $image) = mysql_fetch_array($sql))
        {
            $titre = htmlentities($titre);

            echo "<tr>";

            if ($image != "")
            {
                echo "<td><a href=\"index.php?file=News&amp;op=categorie&amp;cat_id=" . $id . "\">"
                . "<img style=\"border: 0;\" src=\"" . $image . "\" align=\"left\" alt=\"\" title=\"" . _SEENEWS . "&nbsp;" . $titre . "\" /></a></td>\n";
            }

            echo "<td><b>" . $titre . " :</b><br />" . $description . "</td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n";
        }
        echo "</table><br /><br /><div style=\"text-align: center;\"><small><i>( " . _CLICSCREEN . " )</i></small></div><br />\n";

        closetable();
    }

    function pdf($news_id)
    {
        global $nuked, $language;

		if ($language == "french" && strpos("WIN", PHP_OS)) setlocale (LC_TIME, "french");
		else if ($language == "french" && strpos("BSD", PHP_OS)) setlocale (LC_TIME, "fr_FR.ISO8859-1");
		else if ($language == "french") setlocale (LC_TIME, "fr_FR");
		else setlocale (LC_TIME, $language);

        $sql = mysql_query("SELECT auteur, auteur_id, date, titre, texte, suite FROM " . NEWS_TABLE . " WHERE id = '" . $news_id . "'");
        list($autor, $autor_id, $date, $title, $content, $suite) = mysql_fetch_row($sql);

        $heure = strftime("%H:%M", $date);
        $text = $content . "<br><br>" . $suite;

        if ($autor_id != "")
        {
            $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            $test = mysql_num_rows($sql2);
        }

        if ($autor_id != "" && $test > 0)
        {
            list($auteur) = mysql_fetch_array($sql2);
            $auteur = @html_entity_decode($auteur);
        }
        else
        {
            $auteur = $autor;
        }

        if ($language == "french")
        {
            $date = strftime("%A %d %B %Y", $date);
        }
        else
        {
            $date = strftime("%A %B %d %Y", $date);
        }

        $posted = "<font size=\"1\">" . _NEWSPOSTBY . " <a href=\"" . $nuked['url'] . "/index.php?file=Members&op=detail&autor=" . $auteur . "\">" . $auteur . "</a> " . _THE . " " . $date . " " . _AT . " " . $heure . "</font><br><br>";

        $texte = $posted . $text;

        $articleurl = $nuked['url'] . "/index.php?file=News&op=index_comment&news_id=" . $news_id;

        include ('Includes/html2pdf/html2pdf.class.php');
        $sitename = $nuked['name'] . " - " . $nuked['slogan'];
        $sitename  = @html_entity_decode($sitename);

		$texte = "<h1>".$title."</h1><hr />".$texte."<hr />".$sitename."<br />".$articleurl;
		$_REQUEST['file'] = $sitename."_".$title;
		$_REQUEST['file'] = str_replace(' ','_',$_REQUEST['file']);
		$_REQUEST['file'] .= ".pdf";
		
		$pdf = new HTML2PDF('P','A4','fr');
		$pdf->WriteHTML($texte);
		$pdf->Output($_REQUEST['file']);
    }


    function sendfriend($news_id)
    {
        global $nuked, $user, $captcha;

        opentable();

        echo "<script type=\"text/javascript\">\n"
		."<!--\n"
		."\n"
		. "function verifchamps()\n"
		. "{\n"
		. "\n"
		. "if (document.REQUESTElementById('sf_pseudo').value.length == 0)\n"
		. "{\n"
		. "alert('" . _NONICK . "');\n"
		. "return false;\n"
		. "}\n"
		. "\n"
		. "if (document.REQUESTElementById('sf_mail').value.indexOf('@') == -1)\n"
		. "{\n"
		. "alert('" . _BADMAIL . "');\n"
		. "return false;\n"
		. "}\n"
		. "\n"
		. "return true;\n"
		. "}\n"
		."\n"
		. "// -->\n"
		. "</script>\n";

        $sql = mysql_query("SELECT titre FROM " . NEWS_TABLE . " WHERE id = '" . $news_id . "'");
        list($title) = mysql_fetch_array($sql);

        echo "<form method=\"post\" action=\"index.php?file=News\" onsubmit=\"return verifchamps()\">\n"
		. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"60%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
		. "<tr><td align=\"center\"><br /><big><b>" . _FSEND . "</b></big><br /><br />" . _YOUSUBMIT . " :<br /><br />\n"
		. "<b>" . $title . "</b><br /><br /></td></tr><tr><td align=\"left\">\n"
		. "<b>" . _YNICK . " : </b>&nbsp;<input type=\"text\" id=\"sf_pseudo\" name=\"pseudo\" value=\"" . $user[2] . "\" size=\"20\" /></td></tr>\n"
		. "<tr><td><b>" . _FMAIL . " : </b>&nbsp;<input type=\"text\" id=\"sf_mail\" name=\"mail\" value=\"mail@gmail.com\" size=\"25\" /></td></tr>\n"
		. "<tr><td><b>" . _YCOMMENT . " : </b><br /><textarea name=\"comment\" cols=\"60\" rows=\"10\"></textarea></td></tr>\n";

		if ($captcha == 1) create_captcha(1);

		echo "<tr><td align=\"center\"><input type=\"hidden\" name=\"op\" value=\"sendnews\" />\n"
		. "<input type=\"hidden\" name=\"news_id\" value=\"" . $news_id . "\" />\n"
		." <input type=\"hidden\" name=\"title\" value=\"" . $title . "\" />\n"
		." <input type=\"submit\" value=\"" . _SEND . "\" /></td></tr></table></form><br />\n";

        closetable();
    }

    function sendnews($title, $news_id, $comment, $mail, $pseudo)
    {
        global $nuked, $captcha,$user_ip;

        opentable();

		if ($captcha == 1 && !ValidCaptchaCode($_POST['code_confirm']))
		{
			echo "<div style=\"text-align: center;\"><br /><br />" . _BADCODECONFIRM . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div>";
		}
		else
		{
	        $date2 = time();
	        $date2 = strftime("%x %H:%M", $date2);


	        $mail = trim($mail);
	        $pseudo = trim($pseudo);

			$subject = $nuked['name'] . ", " . $date2;
			$corps = $pseudo . " (IP : " . $user_ip . ") " . _READNEWS . " " . $title . ", " . _NEWSURL . "\r\n" . $nuked['url'] . "/index.php?file=News&op=index_comment&news_id=" . $news_id . "\r\n\r\n" . _YCOMMENT . " : " . $comment . "\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
			$from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];

			$subject = @html_entity_decode($subject);
			$corps = @html_entity_decode($corps);
			$from = @html_entity_decode($from);

			mail($mail, $subject, $corps, $from);

	        echo "<div style=\"text-align: center;\"><br />" . _SENDFMAIL . "<br /><br /></div>";
	        redirect("index.php?file=News", 2);
		}
        closetable();
    }

    switch ($_REQUEST['op'])
    {
        case"index":
            index();
            break;

        case"index_comment":
            index_comment($_REQUEST['news_id']);
            break;

        case"suite":
            suite($_REQUEST['news_id']);
            break;

        case"categorie":
            categorie($_REQUEST['cat_id']);
            break;

        case"sujet":
            sujet();
            break;

        case"pdf":
            pdf($_REQUEST['news_id']);
            break;

        case"sendfriend":
            sendfriend($_REQUEST['news_id']);
            break;

        case"sendnews":
            sendnews($_REQUEST['title'], $_REQUEST['news_id'], $_REQUEST['comment'], $_REQUEST['mail'], $_REQUEST['pseudo']);
            break;

        default:
            index();
            break;
    }

}
else if ($level_access == -1)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
}
else if ($level_access == 1 && $visiteur == 0)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
    closetable();
}
else
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
}

?>