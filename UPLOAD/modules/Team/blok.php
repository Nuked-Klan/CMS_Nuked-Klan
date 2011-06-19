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

global $nuked, $theme, $language, $bgcolor3;
translate("modules/Team/lang/" . $language . ".lang.php");

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4)
{
    echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
    . "<td style=\"width: 35%;\" align=\"center\">&nbsp;<b>" . _NICK . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _MAIL . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _ICQ . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _MSN . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _AIM . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _YIM . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _RANK . "</b></td></tr>\n";

    $sql_team = mysql_query("SELECT cid FROM " . TEAM_TABLE);
    $nb_team = mysql_num_rows($sql_team);

    if ($nb_team > 0) $where = "WHERE team > 0"; else $where = "WHERE niveau > 1";

    $sql = mysql_query("SELECT pseudo, mail, icq, msn, aim, yim, rang FROM " . USER_TABLE . " " . $where . " ORDER BY ordre, pseudo");
    while (list($pseudo, $mail, $icq, $msn, $aim, $yim, $rang) = mysql_fetch_array($sql))
    {
        $nick_team = $nuked['tag_pre'] . $pseudo . $nuked['tag_suf'];

        if (is_file("themes/" . $theme . "/images/mail.gif"))
        {
            $img = "themes/" . $theme . "/images/mail.gif";
        }
        else
        {
            $img = "modules/Team/images/mail.gif";
        }

        if ($rang != "" && $rang > 0)
        {
            $sql_rank = mysql_query("SELECT titre FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $rang . "'");
            list($rank_name) = mysql_fetch_array($sql_rank);
            $rank_name = htmlentities($rank_name);
        }
        else
        {
            $rank_name = "N/A";
        }

        if ($j == 0)
        {
            $bg = $bgcolor2;
            $j++;
        }
        else
        {
            $bg = $bgcolor1;
            $j = 0;
        }

        echo "<tr style=\"background: " . $bg . ";\">\n"
        . "<td style=\"width: 35%;\" align=\"left\">&nbsp;&nbsp;<a href=\"index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($pseudo) . "\" title=\"" . _VIEWPROFIL . "\"><b>" . $nick_team . "</b></a></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><a href=\"mailto:" . $mail . "\"><img style=\"border: 0;\" src=\"" . $img . "\" alt=\"\" title=\"" . $mail . "\" /></a></td>\n"
        ." <td style=\"width: 10%;\" align=\"center\">\n";

        if ($icq != "")
        {
            echo "<a href=\"http://web.icq.com/whitepages/add_me?uin=" . $icq . "&amp;action=add\"><img style=\"border: 0;\" src=\"modules/Team/images/icq.gif\" alt=\"\" title=\"" . $icq . "\" /></a>";
        }
        else
        {
            echo "N/A";
        }

        echo "</td><td style=\"width: 10%;\" align=\"center\">\n";

        if ($msn != "")
        {
            echo "<a href=\"mailto:" . $msn . "\"><img style=\"border: 0;\" src=\"modules/Team/images/msn.gif\" alt=\"\" title=\"" . $msn . "\" /></a>";
        }
        else
        {
            echo"N/A";
        }

        echo "</td><td style=\"width: 10%;\" align=\"center\">\n";

        if ($aim != "")
        {
            echo "<a href=\"aim:goim?screenname=" . $aim . "&amp;message=Hi+" . $aim . "+Are+you+there+?\"><img style=\"border: 0;\" src=\"modules/Team/images/aim.gif\" alt=\"\" title=\"" . $aim . "\" /></a>";
        }
        else
        {
            echo "N/A";
        }

        echo "</td><td style=\"width: 10%;\" align=\"center\">\n";

        if ($yim != "")
        {
            echo "<a href=\"http://edit.yahoo.com/config/send_webmesg?target=" . $yim . "&amp;src=pg\"><img style=\"border: 0;\" src=\"modules/Team/images/yim.gif\" alt=\"\" title=\"" . $yim . "\" /></a>";
        }
        else
        {
            echo "N/A";
        }

        echo "</td><td style=\"width: 15%;\" align=\"center\">" . $rank_name . "</td></tr>\n";
    }
    echo "</table>\n";
}
else
{
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"1\">\n";

    $sql_team = mysql_query("SELECT cid FROM " . TEAM_TABLE);
    $nb_team = mysql_num_rows($sql_team);

    if ($nb_team > 0) $where = "WHERE team > 0"; else $where = "WHERE niveau > 1";

    $sql = mysql_query("SELECT pseudo, mail, country FROM " . USER_TABLE . " " . $where . " ORDER BY ordre, pseudo");
    while (list($pseudo, $mail, $country) = mysql_fetch_array($sql))
    {
        list ($pays, $ext) = explode ('.', $country);

        $nick_team = $nuked['tag_pre'] . $pseudo . $nuked['tag_suf'];

        if (is_file("themes/" . $theme . "/images/mail.gif"))
        {
            $img = "themes/" . $theme . "/images/mail.gif";
        }
        else
        {
            $img = "modules/Team/images/mail.gif";
        }

        echo "<tr><td style=\"width: 20%;\" align=\"center\"><img src=\"images/flags/" . $country . "\" alt=\"\" title=\"" . $pays . "\" /></td>\n"
        . "<td style=\"width: 60%;\"><a href=\"index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($pseudo) . "\"><b>" . $nick_team . "</b></a></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><a href=\"mailto:" . $mail . "\"><img style=\"border: 0;\" src=\"" . $img . "\" alt=\"\" title=\"" . $mail . "\" /></a></td></tr>\n";
    }
    echo "</table>\n";
}

?>