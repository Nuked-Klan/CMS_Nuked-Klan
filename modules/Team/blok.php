<?php
/**
 * blok.php
 *
 * Display block of Team module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $nuked, $language, $bgcolor2, $bgcolor3;

translate('modules/Team/lang/'. $language .'.lang.php');


if ($active == 3 || $active == 4)
{
    require_once 'Includes/nkUserSocial.php';

    echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
        . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
        . "<td style=\"width: 35%;\" align=\"center\">&nbsp;<b>" . __('NICK') . "</b></td>\n";

    // NOTE : public email is used instead private email
    $userSocialData = nkUserSocial_getConfig();

    foreach ($userSocialData as $userSocial)
        // width: 10%;
        echo '<td class="', $userSocial['cssClass'], '" align="center"><b>', nkUserSocial_getLabel($userSocial), '</b></td>', "\n";

    echo "<td style=\"width: 15%;\" align=\"center\"><b>" . __('RANK') . "</b></td></tr>\n";

    $sql_team = nkDB_execute("SELECT cid FROM " . TEAM_TABLE);
    $nb_team = nkDB_numRows($sql_team);

    if ($nb_team > 0) $where = "WHERE team > 0"; else $where = "WHERE niveau > 1";

    $userSocialFields = nkUserSocial_getActiveFields();
    $userSocialFields = ($userSocialFields) ? ', '. implode(', ', $userSocialFields) : '';

    $dbrTeamMember = nkDB_selectMany(
        'SELECT pseudo AS nickname, rang'. $userSocialFields .'
        FROM '. USER_TABLE,
        array('ordre', 'pseudo')
    );

    $j = 0;

    foreach ($dbrTeamMember as $teamMember) {
        $nick_team = $nuked['tag_pre'] . $teamMember['nickname'] . $nuked['tag_suf'];

        if ($teamMember['rang'] != "" && $teamMember['rang'] > 0)
        {
            $sql_rank = nkDB_execute("SELECT titre FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $teamMember['rang'] . "'");
            list($rank_name) = nkDB_fetchArray($sql_rank);
            $rank_name = nkHtmlEntities($rank_name);
        }
        else
        {
            $rank_name = "N/A";
        }

        echo "<tr style=\"background: " . (($j++ % 2 == 1) ? $bgcolor1 : $bgcolor2) . ";\">\n"
        . "<td style=\"width: 35%;\" align=\"left\">&nbsp;&nbsp;<a href=\"index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($teamMember['nickname']) . "\" title=\"" . __('VIEW_PROFIL') . "\"><b>" . $nick_team . "</b></a></td>\n";

        foreach ($userSocialData as $userSocial) {
            // width: 10%;
            echo '<td class="', $userSocial['cssClass'], '" align="center">', "\n"
                , nkUserSocial_formatImgLink($userSocial, $teamMember)
                , '</td>', "\n";
        }

        echo "<td style=\"width: 15%;\" align=\"center\">" . $rank_name . "</td></tr>\n";
    }
    echo "</table>\n";
}
else
{
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"1\">\n";

    $sql_team = nkDB_execute("SELECT cid FROM " . TEAM_TABLE);
    $nb_team = nkDB_numRows($sql_team);

    if ($nb_team > 0) $where = "WHERE team > 0"; else $where = "WHERE niveau > 1";

    $sql = nkDB_execute("SELECT pseudo, email, country FROM " . USER_TABLE . " " . $where . " ORDER BY ordre, pseudo");

    $userSocialImgCfg = nkUserSocial_getImgConfig();

    while (list($pseudo, $email, $country) = nkDB_fetchArray($sql)) {
        list($pays, $ext) = explode ('.', $country);

        $nick_team = $nuked['tag_pre'] . $pseudo . $nuked['tag_suf'];

        echo "<tr><td style=\"width: 20%;\" align=\"center\"><img src=\"images/flags/" . $country . "\" alt=\"\" title=\"" . $pays . "\" /></td>\n"
        . "<td style=\"width: 60%;\"><a href=\"index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($pseudo) . "\"><b>" . $nick_team . "</b></a></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">";

        if ($visiteur >= $nuked['user_social_level'] && $email != '') {
            return '<a href="'. str2htmlEntities('mailto:'. $email) .'"><img class="nkNoBorder" src="'. $imgConfig['email'] .'" alt="" title="'. __('SEND_EMAIL') .'" /></a>';
        }
        else {
            return '<img class="nkNoBorder" src="images/user/'. $imgConfig['emailna'] .'.png" alt="" />';
        }

        echo "</td></tr>\n";
    }
    echo "</table>\n";
}

?>