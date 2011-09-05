<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $nuked, $language;

translate('modules/Stats/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');

$visiteur = $user ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function main()
    {
        global $nuked, $language;

        $date_install = nkDate($nuked['date_install']);

        $width_div = (isset($_REQUEST['nuked_nude'])) ? 100 : 80;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINSTATS . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Stats.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\" style=\"width: " . $width_div . "%; margin: auto\"><br /><br />\n";

        echo "<div style=\"text-align: center;\"><br /><big><b>" . _STATSSITE . "&nbsp;" . $nuked['name'] . "</b></big><br /><br /> ";
        if ($nuked['level_analys'] != -1)
        {
            echo "[ <a href=\"index.php?file=Stats&amp;page=visits\">" . _ANALYS . "</a> ] - ";
        }

        echo "[ <a href=\"index.php?file=Stats&amp;page=top\">" . _TOP . "</a> ]</div>";

        $sql = mysql_query("SELECT count FROM " . STATS_TABLE . " WHERE type = 'pages'");
        while (list($count) = mysql_fetch_array($sql))
        {
            $counter = $counter + $count;
        }

        echo "<div style=\"text-align: center;\"><br />" . _WERECEICED . "&nbsp;" . $counter . "&nbsp;" . _PAGESEE . "&nbsp;" . $date_install . ".<br /><br /><h3>" . _PAGEVIEWS . "</h3></div>\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" cellpadding=\"2\" cellspacing=\"1\">\n"
        . "<tr>\n"
        . "<td style=\"width: 5%;\" align=\"center\"><b>#</b></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NOM . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _VISITCOUNT . "</b></td>\n"
        . "<td style=\"width: 50%;\">\n"
        . "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">\n"
        . "<tr><td style=\"width: 25%;\">&nbsp;<b>0%</b></td>\n"
        . "<td style=\"width: 25%;\"><b>25%</b></td>\n"
        . "<td style=\"width: 25%;\"><b>50%</b></td>\n"
        ." <td style=\"width: 25%;\"><b>75%</b></td>\n"
        . "<td style=\"width: 25%;\"><b>100%</b>&nbsp;</td></tr></table></td></tr>\n";

        $ipage = 0;
        $sql2 = mysql_query("SELECT nom FROM " . STATS_TABLE . " WHERE type = 'pages' ORDER BY count DESC");
        while (list($page) = mysql_fetch_array($sql2))
        {
            if (nivo_mod($page) != -1)
            {
                $ipage++;

                if ($page == "Archives")
                {
                    $pagename = _NAMEARCHIVES;
                } 
                else if ($page == "Gallery")
                {
                    $pagename = _NAMEGALLERY;
                } 
                else if ($page == "Calendar")
                {
                    $pagename = _NAMECALANDAR;
                } 
                else if ($page == "Defy")
                {
                    $pagename = _NAMEDEFY;
                } 
                else if ($page == "Download")
                {
                    $pagename = _NAMEDOWNLOAD;
                } 
                else if ($page == "Guestbook")
                {
                    $pagename = _NAMEGUESTBOOK;
                } 
                else if ($page == "Irc")
                {
                    $pagename = _NAMEIRC;
                } 
                else if ($page == "Links")
                {
                    $pagename = _NAMELINKS;
                } 
                else if ($page == "Wars")
                {
                    $pagename = _NAMEMATCHES;
                } 
                else if ($page == "News")
                {
                    $pagename = _NAMENEWS;
                } 
                else if ($page == "Search")
                {
                    $pagename = _NAMESEARCH;
                } 
                else if ($page == "Recruit")
                {
                    $pagename = _NAMERECRUIT;
                } 
                else if ($page == "Sections")
                {
                    $pagename = _NAMESECTIONS;
                } 
                else if ($page == "Server")
                {
                    $pagename = _NAMESERVER;
                } 
                else if ($page == "Team")
                {
                    $pagename = _NAMETEAM;
                } 
                else if ($page == "Members")
                {
                    $pagename = _NAMEMEMBERS;
                } 
                else if ($page == "Forum")
                {
                    $pagename = _NAMEFORUM;
                } 
                else
                {
                    $pagename = $page;
                } 

                $sql3 = mysql_query("SELECT count FROM " . STATS_TABLE . " WHERE type = 'pages' AND nom = '" . $page . "'");
                list($pg_count) = mysql_fetch_array($sql3);
                $etat = ($pg_count * 100) / $counter;
                $pourcent = round($etat);

                echo "<tr>\n"
                . "<td style=\"width : 5%;\" align=\"center\">" . $ipage . "</td>\n"
                . "<td style=\"width : 25%;\">" . $pagename . "</td>\n"
                . "<td style=\"width : 20%;\" align=\"center\">" . $pg_count . " (" . $pourcent . "%)</td>\n"
                . "<td style=\"width : 50%;\">\n";

                show_etat($etat);

                echo "</td></tr>\n";
            }
        } 

        echo "</table><div style=\"text-align: center;\"><br /><br /><big>" . _OTHERSTATS . "</big></div>\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left\" width=\"80%\" cellpadding=\"2\" cellspacing=\"1\">\n"
        . "<tr>\n"
        . "<td align=\"center\"><b>" . _NOM . "</b></td>\n"
        . "<td align=\"center\"><b>" . _COUNT . "</b></td></tr>\n";

        $sql_users = mysql_query("SELECT id FROM " . USER_TABLE);
        $nb_users = mysql_num_rows($sql_users);

        echo "<tr><td>&nbsp;" . _MEMBERSRECORD . "</td><td align=\"center\">" . $nb_users . "</td></tr>\n";

        if (nivo_mod("News") != -1)
        {
            $sql_news = mysql_query("SELECT id FROM " . NEWS_TABLE);
            $nb_news = mysql_num_rows($sql_news);
            echo "<tr><td>&nbsp;" . _NEWSINDB . "</td><td align=\"center\">" . $nb_news . "</td></tr>\n";
        }

        if (nivo_mod("Sections") != -1)
        {
            $sql_arts = mysql_query("SELECT artid FROM " . SECTIONS_TABLE);
            $nb_arts = mysql_num_rows($sql_arts);
            echo "<tr><td>&nbsp;" . _ARTSINDB . "</td><td align=\"center\">" . $nb_arts . "</td></tr>\n";
        }

        if (nivo_mod("Comment") != -1)
        {
            $sql_com = mysql_query("SELECT id FROM " . COMMENT_TABLE);
            $nb_comment = mysql_num_rows($sql_com);
            echo "<tr><td>&nbsp;" . _COMMENTINDB . "</td><td align=\"center\">" . $nb_comment . "</td></tr>\n";
        }

        if (nivo_mod("Guestbook") != -1)
        {
            $sql_gbook = mysql_query("SELECT id FROM " . GUESTBOOK_TABLE);
            $nb_mess = mysql_num_rows($sql_gbook);
            echo "<tr><td>&nbsp;" . _SIGNINGUESTBOOK . "</td><td align=\"center\">" . $nb_mess . "</td></tr>\n";
        }

        if (nivo_mod("Download") != -1)
        {
            $sql_dl = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE);
            $nb_downloads = mysql_num_rows($sql_dl);
            echo "<tr><td>&nbsp;" . _FILESINDB . "</td><td align=\"center\">" . $nb_downloads . "</td></tr>\n";
        }

        if (nivo_mod("Links") != -1)
        {
            $sql_links = mysql_query("SELECT id FROM " . LINKS_TABLE);
            $nb_liens = mysql_num_rows($sql_links);
            echo "<tr><td>&nbsp;" . _LINKSINDB . "</td><td align=\"center\">" . $nb_liens . "</td></tr>\n";
        }

        if (nivo_mod("Gallery") != -1)
        {
            $sql_img = mysql_query("SELECT sid FROM " . GALLERY_TABLE);
            $nb_img = mysql_num_rows($sql_img);
            echo "<tr><td>&nbsp;" . _SCREENINGALLERY . "</td><td align=\"center\">" . $nb_img . "</td></tr>\n";
        }

        if (defined("WARS_TABLE") && nivo_mod("Wars") != -1)
        {
            $sql_match = mysql_query("SELECT warid FROM " . WARS_TABLE . "");
            $nb_match = mysql_num_rows($sql_match);
            echo "<tr><td>&nbsp;" . _MATCHSINDB . "</td><td align=\"center\">" . $nb_match . "</td></tr>\n";
        } 
        echo "</table><br />\n";

        echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function del()\n"
        . "{\n"
        . "if (confirm('" . _DELETE . "'))\n"
        . "{document.location.href = 'index.php?file=Stats&page=admin&op=del';}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

        echo "<div style=\"text-align: center;\"><b><a href=\"javascript:del();\">"._VIDERSTATS."</a></b><br />\n"
        . "<br /><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function del()
    {
        global $nuked, $user;

        $sql = mysql_query('DELETE FROM ' . $nuked['prefix'] . '_stats_visitor');

        // Action
        $texteaction = _ACTIONDELSTATS;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action

        echo "<div class=\"notification success png_bg\">\n"
            . "<div>\n"
            . "" . _VIDER . "\n"
            . "</div>\n"
            . "</div>\n";

        redirect("index.php?file=Stats&page=admin", 2);
    }

    function show_etat($etat)
    {
        global $theme;

        if ($etat < 1)
        {
            $width = 1;
        } 
        else
        {
            $width = $etat;
        } 
        if (is_file("themes/" . $theme . "/images/bar.gif"))
        {
            $img = "themes/" . $theme . "/images/bar.gif";
        } 
        else
        {
            $img = "modules/Stats/images/bar.gif";
        } 

        echo "<div style=\"width: " . round($width) . "%\">\n"
        . "<div style=\"width: " . round($width) . "%;height: 10px;background-image: url(" . $img . ");margin:0;padding:0\">&nbsp;</div>\n"
        ."</div>\n";
    }
    
    switch($_REQUEST['op'])
    {
        case "del":
            admintop();
            del();
            adminfoot();
            break;
        default:
            if (!isset($_REQUEST['nuked_nude'])) admintop();
            main();
            if (!isset($_REQUEST['nuked_nude'])) adminfoot();
            break;
    }

}
else if ($level_admin == -1){
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
} 
else if ($visiteur > 1){
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
} 
else
{
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
}
?>