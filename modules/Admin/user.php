<?php
/**
 * games.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', SUPER_ADMINISTRATOR_ACCESS))
    return;


function select_team() {
    echo "<option value=\"\">". __('NONE') ."</option>\n";

    $sql = mysql_query("SELECT cid, titre FROM " . TEAM_TABLE . " ORDER BY ordre, titre");

    while (list($cid, $titre) = mysql_fetch_array($sql)) {
        $titre = printSecuTags($titre);

        echo "<option value=\"" . $cid . "\">" . $titre . "</option>\n";
    }
}

function select_rank() {
    global $nuked;

    echo "<option value=\"\">" . _NORANK . "</option>\n";

    $sql = mysql_query("SELECT id, titre FROM " . TEAM_RANK_TABLE . " ORDER BY ordre, titre");

    while (list($rid, $titre) = mysql_fetch_array($sql)) {
        $titre = nkHtmlEntities($titre);

        echo "<option value=\"" . $rid . "\">" . $titre . "</option>\n";
    }
}

function getTeamSelector() {
    nkTemplate_setPageDesign('none');

    $n = $_POST['nbTeam'] + 1;

    echo "<tr class=\"teamSelector alt-row\"><td><b>". _TEAM ." ". $n ." : </b></td><td><select name=\"team[]\">\n";

    select_team();

    echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
    . "<b>" . _RANKTEAM . " : </b><select name=\"teamRank[]\">\n";

    select_rank();

    echo '</select>&nbsp;&nbsp;&nbsp;&nbsp;<a class="deleteTeamLink" href="#">'
    , '<img style="border: 0;" src="modules/Admin/images/icons/cross.png" alt="" title="', __('DELETE_THIS_TEAM'), '" />'
    , '</a></td></tr>', "\n";
}

function add_user() {
    global $language;

    require_once 'Includes/nkUserSocial.php';

    echo '<script type="text/javascript">' ."\n"
    . '// <![CDATA[' ."\n"
    . '$(document).ready(function() { ' ."\n"
    . '$("#addTeam").click(function() {

        var nbTeam = $("#nbTeam").val(),
            tr     = $(this).closest("tr");

        $.ajax({
            type: "POST",
            url: "index.php?file=Admin&page=user&op=getTeamSelector",
            data: { nbTeam : nbTeam }
        }).done(function(html) {
            nbTeam++;
            $(html).insertBefore(tr);
            $("#nbTeam").attr("value", nbTeam);
        });

    });

    $("a.deleteTeamLink").live("click", function() {
        $(this).parent().parent().remove();
        return false;
    });'
    . "});\n"
    . '// ]]>' ."\n"
    . '</script>' ."\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADDUSER . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/user.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(2);

    echo "<form method=\"post\" action=\"index.php?file=Admin&amp;page=user&amp;op=do_user\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NICK . " :</b></td><td><input type=\"text\" name=\"nick\" size=\"30\" maxlength=\"80\" /> *</td></tr>\n"
    . "<tr><td><b>" . _PASSWORD . " :</b></td><td><input type=\"password\" name=\"pass_reg\" size=\"10\" maxlength=\"80\" /> *</td></tr>\n"
    . "<tr><td><b>" . _PASSWORD . " (" . _CONFIRMPASS . ") :</b></td><td><input type=\"password\" name=\"pass_conf\" size=\"10\" maxlength=\"80\" /> *</td></tr>\n"
    . "<tr><td><b>" . _MAIL . " :</b></td><td><input type=\"text\" name=\"mail\" size=\"30\" maxlength=\"80\" /> *</td></tr>\n";

    foreach (nkUserSocial_getConfig() as $userSocial) {
        $userSocialInput = nkUserSocial_getInputConfig($userSocial);

        echo '<tr><td><b>', $userSocialInput['label'], ' : </b></td><td><input type="text" name="'
            , $userSocial['field'], '" size="', $userSocial['size'], '" maxlength="'
            , $userSocial['maxlength'], '" value="" /></td></tr>', "\n";
    }

    echo "<tr><td><b>" . _COUNTRY . " :</b></td><td><select name=\"country\">\n";

    if ($language == "french") $pays = "France.gif";

    $rep = Array();
    $handle = @opendir("images/flags");
    while (false !== ($f = readdir($handle)))
    {
        if ($f != ".." && $f != "." && $f != "index.html" && $f != "Thumbs.db")
        {
            $rep[] = $f;
        }
    }

    closedir($handle);
    sort ($rep);
    reset ($rep);

    while (list ($key, $filename) = each ($rep))
    {
        if ($filename == $pays)
        {
            $checked = "selected=\"selected\"";
        }
        else
        {
            $checked = "";
        }

        list ($country, $ext) = explode ('.', $filename);
        echo "<option value=\"" . $filename . "\" " . $checked . ">" . $country . "</option>\n";
    }

    echo "</select></td></tr>\n"
    . "<tr><td><b>" . __('GAME') . " :</b></td><td><select name=\"game\">\n";

    $sql = mysql_query("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
    while (list($game_id, $nom) = mysql_fetch_array($sql))
    {
        $nom = printSecuTags($nom);

        echo "<option value=\"" . $game_id . "\">" . $nom . "</option>\n";
    }

    echo "</select></td></tr>\n"
    . "<tr><td><b>" . _LEVEL . " :</b></td><td><select name=\"niveau\">\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select></td></tr>\n"
    . "<tr class=\"teamSelector\"><td><b>". _TEAM ." : </b></td><td><select name=\"team[]\">\n";

    select_team();

    echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
    . "<b>" . _RANKTEAM . " : </b><select name=\"teamRank[]\">\n";

    select_rank();

    echo"</select></td></tr>\n"
    . "<tr><td>&nbsp;</td><td><input id=\"addTeam\" class=\"button\" type=\"button\" value=\"" . __('ADD_TEAM') . "\" /></td></tr>\n"
    . "<tr><td><b>" . _AVATAR . " :</b></td><td><input type=\"text\" name=\"avatar\" size=\"40\" maxlength=\"100\" /></td></tr>\n"
    . "<tr><td><b>" . _SIGN . " :</b></td><td><textarea class=\"editor\" name=\"signature\" rows=\"10\" cols=\"55\"></textarea></td></tr></table>\n"
    . "<div style=\"text-align:center;padding-top:10px;\"><input class=\"button\" type=\"submit\" value=\"" . _ADDUSER . "\" />"
    . "<a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=user\">" . __('BACK') . "</a>"
    . "<input id=\"nbTeam\" type=\"hidden\" name=\"nbTeam\" value=\"1\" /></div>\n"
    . "</form><br /></div></div>\n";
}

function edit_user($id_user) {
    global $nuked, $language, $user;

    require_once 'Includes/nkUserSocial.php';

    $userSocialFields = implode(', U.', nkUserSocial_getActiveFields());

    if ($userSocialFields != '') $userSocialFields = ', U.'. $userSocialFields;

    $dbrUser = nkDB_selectOne(
        'SELECT U.niveau, U.pseudo, U.mail, U.rang, U.country, U.game, U.avatar, U.signature, TR.titre AS rankName
        '. $userSocialFields .'
        FROM '. USER_TABLE .' AS U
        LEFT JOIN '. TEAM_RANK_TABLE .' AS TR ON TR.id = U.rang
        WHERE U.id = '. nkDB_escape($id_user)
    );

    $dbrTeam = nkDB_selectMany(
        'SELECT T.cid, T.titre AS teamName, TM.rank, TR.titre AS rankName
        FROM '. TEAM_MEMBERS_TABLE.' AS TM
        INNER JOIN '. TEAM_TABLE .' AS T ON T.cid = TM.team
        INNER JOIN '. TEAM_RANK_TABLE .' AS TR ON TR.id = TM.rank
        WHERE TM.userId = '. nkDB_escape($id_user)
    );

    echo '<script type="text/javascript">' ."\n"
    . '// <![CDATA[' ."\n"
    . '$(document).ready(function() { ' ."\n"
    . '$("#addTeam").click(function() {

        var nbTeam = $("#nbTeam").val(),
            tr     = $(this).closest("tr");

        $.ajax({
            type: "POST",
            url: "index.php?file=Admin&page=user&op=getTeamSelector",
            data: { nbTeam : nbTeam }
        }).done(function(html) {
            nbTeam++;
            $(html).insertBefore(tr);
            $("#nbTeam").attr("value", nbTeam);
        });

    });

    $("a.deleteTeamLink").live("click", function() {
        $(this).parent().parent().remove();
        return false;
    });'
    . "});\n"
    . '// ]]>' ."\n"
    . '</script>' ."\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _USERADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/user.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=user&amp;op=update_user\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NICK . " :</b></td><td><input type=\"text\" name=\"nick\" size=\"30\" maxlength=\"80\" value=\"" . $dbrUser['pseudo'] . "\" /> *</td></tr>\n"
    . "<tr><td><b>" . _PASSWORD . " :</b></td><td><input type=\"password\" name=\"pass_reg\" size=\"10\" maxlength=\"80\" autocomplete=\"off\" /></td></tr>\n"
    . "<tr><td><b>" . _PASSWORD . " (" . _CONFIRMPASS . ") :</b></td><td><input type=\"password\" name=\"pass_conf\" size=\"10\" maxlength=\"80\" autocomplete=\"off\" /></td></tr>\n"
    . "<tr><td><b>" . _MAIL . " :</b></td><td><input type=\"text\" name=\"mail\" size=\"30\" maxlength=\"80\" value=\"" . $dbrUser['mail'] . "\" /> *</td></tr>\n";

    foreach (nkUserSocial_getConfig() as $userSocial) {
        $userSocialInput = nkUserSocial_getInputConfig($userSocial);

        if (isset($dbrUser[$userSocial['field']]))
            $value = $dbrUser[$userSocial['field']];
        else
            $value = '';

        echo '<tr><td><b>', $userSocialInput['label'], ' : </b></td><td><input type="text" name="'
            , $userSocial['field'], '" size="', $userSocial['size'], '" maxlength="'
            , $userSocial['maxlength'], '" value="', $value, '" /></td></tr>', "\n";
    }

    echo "<tr><td><b>" . _COUNTRY . " :</b></td><td><select name=\"country\">\n";

    $rep = Array();
    $handle = @opendir("images/flags");
    while (false !== ($f = readdir($handle)))
    {
        if ($f != ".." && $f != "." && $f != "index.html" && $f != "Thumbs.db")
        {
            $rep[] = $f;
        }
    }
    closedir($handle);
    sort ($rep);
    reset ($rep);

    while (list ($key, $filename) = each ($rep))
    {
            if ($filename == $dbrUser['country'])
            {
                $checked = "selected=\"selected\"";
            }
            else
            {
                $checked = "";
            }

            list ($country, $ext) = explode ('.', $filename);
            echo "<option value=\"" . $filename . "\" " . $checked . ">" . $country . "</option>\n";
    }

    echo "</select></td></tr>\n"
    . "<tr><td><b>" . __('GAME') . " :</b></td><td><select name=\"game\">\n";

    $sql = mysql_query("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
    while (list($game_id, $nom) = mysql_fetch_array($sql))
    {
        $nom = printSecuTags($nom);

        if ($game_id == $dbrUser['game'])
        {
            $checked1 = "selected=\"selected\"";
        }
        else
        {
            $checked1 = "";
        }

        echo "<option value=\"" . $game_id . "\" " . $checked1 . ">" . $nom . "</option>\n";
    }

    if ($user[0] == $id_user)
    {
        echo"</select><input type=\"hidden\" name=\"niveau\" value=\"" . $dbrUser['niveau'] . "\" /></td></tr>\n";
    }
    else
    {
        echo"</select></td></tr>\n"
        . "<tr><td><b>" . _LEVEL . " :</b></td><td><select name=\"niveau\"><option>" . $dbrUser['niveau'] . "</option>\n"
        . "<option>1</option>\n"
        . "<option>2</option>\n"
        . "<option>3</option>\n"
        . "<option>4</option>\n"
        . "<option>5</option>\n"
        . "<option>6</option>\n"
        . "<option>7</option>\n"
        . "<option>8</option>\n"
        . "<option>9</option></select></td></tr>\n";
    }

    $n = 0;

    if (! $dbrTeam) {
        echo "<tr class=\"teamSelector\"><td><b>". _TEAM ." : </b></td><td><select name=\"team[]\">\n";

        select_team();

        echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
        . "<b>" . _RANKTEAM . " : </b><select name=\"teamRank[]\">"
        . "<option value=\"" . $dbrUser['rang'] . "\">" . $dbrUser['rankName'] . "</option>";

        select_rank();

        echo "</select></td></tr>\n";
    }
    else {
        foreach ($dbrTeam as $team) {
            $n++;

            $label = _TEAM;

            if ($n > 1) $label .= ' '. $n;

            echo "<tr class=\"teamSelector\"><td><b>". $label ." : </b></td><td><select name=\"team[]\">\n"
            . "<option value=\"" . $team['cid'] . "\">" . $team['teamName'] . "</option>\n";

            select_team();

            echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"
            . "<b>" . _RANKTEAM . " : </b><select name=\"teamRank[]\">"
            . "<option value=\"" . $team['rank'] . "\">" . $team['rankName'] . "</option>";

            select_rank();

            echo '</select>';

            if ($n > 1) {
                echo '&nbsp;&nbsp;&nbsp;&nbsp;<a class="deleteTeamLink" href="#">'
                , '<img style="border: 0;" src="modules/Admin/images/icons/cross.png" alt="" title="', __('DELETE_THIS_TEAM'), '" />'
                , '</a>' ,"\n";
            }

            echo '</td></tr>' ,"\n";
        }
    }

    $nbTeam = $n;

    echo "<tr><td>&nbsp;</td><td><input id=\"addTeam\" class=\"button\" type=\"button\" value=\"" . __('ADD_TEAM') . "\" /></td></tr>\n"
    . "<tr><td><b>" . _AVATAR . " :</b></td><td><input type=\"text\" name=\"avatar\" size=\"40\" maxlength=\"100\" value=\"" . $dbrUser['avatar'] . "\" /></td></tr>\n"
    . "<tr><td><b>" . _SIGN . " :</b></td><td><textarea class=\"editor\" name=\"signature\" rows=\"10\" cols=\"55\">" . $dbrUser['signature'] . "</textarea></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"id_user\" value=\"" . $id_user . "\" /></td></tr>\n"
    . "<tr><td colspan=\"2\" align=\"center\"></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFUSER . "\" />"
    . "<a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=user\">" . __('BACK') . "</a>"
    . "<input id=\"nbTeam\" type=\"hidden\" name=\"nbTeam\" value=\"". $nbTeam ."\" /></div></form><br /></div></div>\n";

}

function update_user($userId) {
    global $language;

    require_once 'Includes/nkUserSocial.php';
    require_once 'Includes/hash.php';

    $dbrUser = nkDB_selectOne(
        'SELECT pseudo
        FROM '. USER_TABLE .'
        WHERE id = '. nkDB_escape($userId)
    );

    $_POST['nick'] = checkNickname($_POST['nick'], $dbrUser['pseudo']);

    if (($error = getCheckNicknameError($_POST['nick'])) !== false) {
        printNotification($error, 'error');
        redirect('index.php?file=Admin&page=user&op=edit_user&id_user='. $userId, 2);
        return;
    }

    if ($_POST['mail'] == "")
    {
        printNotification(_EMPTYFIELD, 'error');
        redirect("index.php?file=Admin&page=user&op=edit_user&id_user=" . $userId, 2);
        return;
    }
    else if ($_POST['pass_reg'] != $_POST['pass_conf'])
    {
        printNotification(_2PASSFAIL, 'error');
        redirect("index.php?file=Admin&page=user&op=edit_user&id_user=" . $userId, 2);
        return;
    }

    $_POST['nick'] = nkHtmlEntities($_POST['nick'], ENT_QUOTES);

    $_POST['signature'] = stripslashes($_POST['signature']);
    $_POST['signature'] = secu_html(nkHtmlEntityDecode($_POST['signature']));

    $_POST['avatar']    = stripslashes($_POST['avatar']);
    $_POST['avatar']    = nkHtmlEntities($_POST['avatar']);

    $data = array(
        'pseudo'    => $_POST['nick'],
        'mail'      => $_POST['mail'],
        'country'   => $_POST['country'],
        'niveau'    => $_POST['niveau'],
        'game'      => $_POST['game'],
        'avatar'    => $_POST['avatar'],
        'signature' => $_POST['signature'],
    );

    if ($_POST['pass_reg'] != '')
        $data['pass'] = nk_hash($_POST['pass_reg']);

    $teamDataCheck = isset($_POST['team'], $_POST['teamRank'])
        && is_array($_POST['team'])
        && is_array($_POST['teamRank']);

    if ($teamDataCheck && $_POST['nbTeam'] == 1 && $_POST['team'][0] == '' && ctype_digit($_POST['teamRank'][0])) {
        $dbrTeamRank = nkDB_selectOne(
            'SELECT ordre
            FROM '. TEAM_RANK_TABLE .'
            WHERE id = '. $_POST['teamRank'][0]
        );

        $data['rang']  = $_POST['teamRank'][0];
        $data['ordre'] = $dbrTeamRank['ordre'];
    }

    foreach (nkUserSocial_getConfig() as $userSocial) {
        if (isset($_POST[$userSocial['field']])) {
            $data[$userSocial['field']] = nkHtmlEntities(stripslashes($_POST[$userSocial['field']]));
        }
    }

    nkDB_update(USER_TABLE, $data, 'id = '. nkDB_escape($userId));

    if ($teamDataCheck) {
        for ($n = 0; $n < $_POST['nbTeam']; $n++) {
            if (isset($_POST['team'][$n], $_POST['teamRank'][$n])
                && ctype_digit($_POST['team'][$n])
                && ctype_digit($_POST['teamRank'][$n])
            ) {
                $check = nkDB_totalNumRows(
                    'FROM '. TEAM_MEMBERS_TABLE .'
                    WHERE userId = '. nkDB_escape($userId) .'
                    AND team = '. (int) $_POST['team'][$n]
                );

                if ($check >= 1) {
                    nkDB_update(TEAM_MEMBERS_TABLE, array(
                            'team'   => $_POST['team'][$n],
                            'rank'   => $_POST['teamRank'][$n]
                        ),
                        'userId = '. nkDB_escape($userId)
                    );
                }
                else {
                    nkDB_insert(TEAM_MEMBERS_TABLE, array(
                        'userId' => $userId,
                        'team'   => $_POST['team'][$n],
                        'rank'   => $_POST['teamRank'][$n]
                    ));
                }
            }
        }
    }

    saveUserAction(_ACTIONMODIFUSER .': '. $_POST['nick']);

    printNotification(_INFOSMODIF, 'success');
    redirect("index.php?file=Admin&page=user", 2);
}

function do_user() {
    require_once 'Includes/nkUserSocial.php';
    require_once 'Includes/hash.php';

    if ($_POST['pass_reg'] == "" || $_POST['pass_conf'] == "" || $_POST['nick'] == "" || $_POST['mail'] == "")
    {
        printNotification(_EMPTYFIELD, 'error');
        redirect("index.php?file=Admin&page=user&op=add_user", 2);
        return;
    }
    else if ($_POST['pass_reg'] != $_POST['pass_conf'])
    {
        printNotification(_2PASSFAIL, 'error');
        redirect("index.php?file=Admin&page=user&op=add_user", 2);
        return;
    }

    $_POST['nick'] = checkNickname($_POST['nick']);

    if (($error = getCheckNicknameError($_POST['nick'])) !== false) {
        printNotification($error, 'error');
        redirect('index.php?file=Admin&page=user&op=add_user', 2);
        return;
    }

    do {
        $userId = sha1(uniqid());
    } while (mysql_num_rows(mysql_query('SELECT * FROM ' . USER_TABLE . ' WHERE id=\'' . $userId . '\' LIMIT 1')) != 0);

    $_POST['nick'] = nkHtmlEntities($_POST['nick'], ENT_QUOTES);

    $_POST['signature'] = stripslashes($_POST['signature']);
    $_POST['signature'] = secu_html(nkHtmlEntityDecode($_POST['signature']));

    $_POST['avatar'] = stripslashes($_POST['avatar']);
    $_POST['avatar'] = nkHtmlEntities($_POST['avatar']);

    $data = array(
        'id'        => $userId,
        'pseudo'    => $_POST['nick'],
        'pass'      => nk_hash($_POST['pass_reg']),
        'mail'      => $_POST['mail'],
        'country'   => $_POST['country'],
        'niveau'    => $_POST['niveau'],
        'game'      => $_POST['game'],
        'avatar'    => $_POST['avatar'],
        'signature' => $_POST['signature'],
        'date'      => time()
    );

    $teamDataCheck = isset($_POST['team'], $_POST['teamRank'])
        && is_array($_POST['team'])
        && is_array($_POST['teamRank']);

    if ($teamDataCheck && $_POST['nbTeam'] == 1 && $_POST['team'][0] == '' && ctype_digit($_POST['teamRank'][0]))
        $data['rang'] = $_POST['teamRank'][0];

    foreach (nkUserSocial_getConfig() as $userSocial) {
        if (isset($_POST[$userSocial['field']])) {
            $data[$userSocial['field']] = nkHtmlEntities(stripslashes($_POST[$userSocial['field']]));
        }
    }

    nkDB_insert(USER_TABLE, $data);

    if ($teamDataCheck) {
        for ($n = 0; $n < $_POST['nbTeam']; $n++) {
            if (isset($_POST['team'][$n], $_POST['teamRank'][$n])
                && ctype_digit($_POST['team'][$n])
                && ctype_digit($_POST['teamRank'][$n])
            ) {
                nkDB_insert(TEAM_MEMBERS_TABLE, array(
                    'userId' => $userId,
                    'team'   => $_POST['team'][$n],
                    'rank'   => $_POST['teamRank'][$n]
                ));
            }
        }
    }

    saveUserAction(_ACTIONADDUSER .': '. $_POST['nick']);

    printNotification(_USERADD, 'success');
    redirect("index.php?file=Admin&page=user", 2);
}

function del_user($id_user)
{
    global $nuked, $user;

    $sql = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $id_user . "'");
    list($nick) = mysql_fetch_array($sql);
    $nick = mysql_real_escape_string($nick);
    $del1 = mysql_query("DELETE FROM " . USER_TABLE . " WHERE id = '" . $id_user . "'");
    $del2 = mysql_query("DELETE FROM " . USER_DETAIL_TABLE . " WHERE user_id = '" . $id_user . "'");
    $del3 = mysql_query("DELETE FROM " . USERBOX_TABLE . " WHERE user_for = '" . $id_user . "'");
    $del4 = delModerator($id_user);

    saveUserAction(_ACTIONDELUSER .': '. $nick);

    printNotification(_USERDEL, 'success');
    redirect("index.php?file=Admin&page=user", 2);
}

function main()
{
    global $nuked, $user, $language;

    if (! array_key_exists('query', $_REQUEST)) $_REQUEST['query'] = '';
    if (! array_key_exists('orderby', $_REQUEST)) $_REQUEST['orderby'] = '';

    if ($_REQUEST['query'] != "")
    {
        $and = "AND (UT.pseudo LIKE '%" . $_REQUEST['query'] . "%')";
        $url_page = "index.php?file=Admin&amp;page=user&amp;query=" . $_REQUEST['query'] . "&amp;orderby=" . $_REQUEST['orderby'];
    }
    else
    {
        $url_page = "index.php?file=Admin&amp;page=user&amp;orderby=" . $_REQUEST['orderby'];
        $and = "";
    }

    $nb_membres = 30;

    $sql3 = mysql_query("SELECT UT.id FROM " . USER_TABLE . " as UT WHERE UT.niveau > 0 " . $and);
    $count = mysql_num_rows($sql3);

    if (! array_key_exists('p', $_REQUEST) || ! $_REQUEST['p']) $_REQUEST['p'] = 1;
    $start = $_REQUEST['p'] * $nb_membres - $nb_membres;
    echo "<link rel=\"stylesheet\" href=\"css/jquery.autocomplete.css\" type=\"text/css\" media=\"screen\" />\n";
    echo "<script type=\"text/javascript\">\n"
    . "<!--\n"
    . "\n"
    . "function deluser(pseudo, id)\n"
    . "{\n"
    . "if (confirm('" . _DELBLOCK . " '+pseudo+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Admin&page=user&op=del_user&id_user='+id;}\n"
    . "}\n"
    . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _USERADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/user.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n";

    nkAdminMenu();

    echo "<div class=\"tab-content\" id=\"tab2\"><form method=\"get\" action=\"index.php\">\n"
    . "<div style=\"text-align: right; margin: 0 20px 0 0;\"><b>" . _SEARCH . " : </b><input type=\"text\" id=\"query\" name=\"query\" size=\"25\" />&nbsp;<input class=\"button\" type=\"submit\" value=\"Ok\" />\n"
    . "<input type=\"hidden\" name=\"file\" value=\"Admin\" />\n"
    . "<input type=\"hidden\" name=\"page\" value=\"user\" /></div></form><br />\n";

    if ($_REQUEST['orderby'] == "date")
    {
        $order_by = "UT.date DESC";
    }
    else if ($_REQUEST['orderby'] == "level")
    {
        $order_by = "UT.niveau DESC, UT.date DESC";
    }
    else if ($_REQUEST['orderby'] == "last_date")
    {
        $order_by = "ST.last_used DESC";
    }
    else if ($_REQUEST['orderby'] == "pseudo")
    {
        $order_by = "UT.pseudo";
    }
    else
    {
        $order_by = "UT.niveau DESC, UT.date DESC";
    }

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\"><tr><td align=\"right\">" . _ORDERBY . " : ";

    if ($_REQUEST['orderby'] == "level" || !$_REQUEST['orderby'])
    {
        echo "<b>" . _LEVEL . "</b> | ";
    }
    else
    {
        echo "<a href=\"index.php?file=Admin&amp;page=user&amp;orderby=level\">" . _LEVEL . "</a> | ";
    }

    if ($_REQUEST['orderby'] == "pseudo")
    {
        echo "<b>" . _NICK . "</b> | ";
    }
    else
    {
        echo "<a href=\"index.php?file=Admin&amp;page=user&amp;orderby=pseudo\">" . _NICK . "</a> | ";
    }

    if ($_REQUEST['orderby'] == "date")
    {
        echo "<b>" . _DATEUSER . "</b> | ";
    }
    else
    {
        echo "<a href=\"index.php?file=Admin&amp;page=user&amp;orderby=date\">" . _DATEUSER . "</a> | ";
    }

    if ($_REQUEST['orderby'] == "last_date")
    {
        echo "<b>" . _LAST. " " ._VISIT . "</b>";
    }
    else
    {
        echo "<a href=\"index.php?file=Admin&amp;page=user&amp;orderby=last_date\">" . _LAST. " " ._VISIT . "</a>";
    }

    echo "&nbsp;</td></tr></table>\n";

    if ($count > $nb_membres)
    {
        echo" <table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\"><tr><td>\n";
        number($count, $nb_membres, $url_page);
        echo "</td></tr></table>\n";
    }

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _LEVEL . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DATEUSER . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _LAST. " " ._VISIT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $req = "SELECT UT.id, UT.pseudo, UT.niveau, UT.date, ST.last_used FROM " . USER_TABLE . " as UT LEFT OUTER JOIN " . SESSIONS_TABLE . " as ST ON UT.id=ST.user_id WHERE UT.niveau > 0 " . $and . " ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_membres;
    $sql = mysql_query($req);
    while (list($id_user, $pseudo, $niveau, $date, $last_used) = mysql_fetch_array($sql))
    {
        $date = nkDate($date);
        $last_used == '' ? $last_used = '-' : $last_used = nkDate($last_used);

        echo "<tr>\n"
        . "<td>&nbsp;" . $pseudo . "</td>\n"
        . "<td align=\"center\">" . $niveau . "</td>\n"
        . "<td align=\"center\">" . $date . "</td>\n"
        . "<td align=\"center\">" . $last_used . "</td>\n"
        . "<td align=\"center\"><a href=\"index.php?file=Admin&amp;page=user&amp;op=edit_user&amp;id_user=" . $id_user . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITUSER . "\" /></a></td>\n"
        . "<td align=\"center\">";

        if ($user[0] == $id_user)
        {
            echo "-";
        }
        else
        {
            echo "<a href=\"javascript:deluser('" . addslashes($pseudo) . "', '" . $id_user . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELETEUSER . "\" /></a>";
        }

        echo "</td></tr>\n";
    }

    if ($count == 0 && $_REQUEST['query'] != "")
    {
        echo "<tr><td colspan=\"5\" align=\"center\">" . _NORESULTFOR . " <b><i>" . $_REQUEST['query'] . "</i></b></td></tr>\n";
    }
    else if ($count == 0)
    {
        echo "<tr><td colspan=\"5\" align=\"center\">" . _NOUSERINDB . "</td></tr>\n";
    }

    echo "</table>\n";

    if ($count > $nb_membres)
    {
        echo" <table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\"><tr><td>\n";
        number($count, $nb_membres, $url_page);
        echo "</td></tr></table>\n";
    }

    echo "<div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function main_ip()
{
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
    . "<!--\n"
    . "\n"
    . "function delip(titre, id)\n"
    . "{\n"
    . "if (confirm('" . _DELBLOCK . " '+titre+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Admin&page=user&op=del_ip&ip_id='+id;}\n"
    . "}\n"
    . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _BAN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/user.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(7);

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _MAIL . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _IP . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $sql = mysql_query("SELECT id, ip, pseudo, email FROM " . BANNED_TABLE . " ORDER BY id DESC");
    $nbip = mysql_num_rows($sql);

    if ($nbip > 0)
    {
        while (list($ip_id, $ip, $pseudo, $email) = mysql_fetch_array($sql))
        {
            $pseudo = nkHtmlEntities($pseudo);


            echo "<tr>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $pseudo . "</td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $email . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $ip . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=user&amp;op=edit_ip&amp;ip_id=" . $ip_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISIP . "\" /></a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delip('" . $ip . "','" . $ip_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISIP . "\" /></a></td></tr>\n";
        }
    }
    else
    {
        echo "<tr><td align=\"center\" colspan=\"5\">" ._NOIPINDB. "</td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=user&amp;op=add_ip\">" . _ADDIP . "</a><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=user\">" . __('BACK') . "</a></div>\n"
    . "<br /></div></div>\n";
}

function add_ip()
{
    global $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _USERADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/user.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=user&amp;op=send_ip\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr><td><b>" . _NICK . " : </b></td><td><input type=\"text\" name=\"pseudo\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _MAIL . " : </b></td><td><input type=\"text\" name=\"email\" size=\"40\" /></td></tr>\n"
    . "<tr><td><b>" . _IP . " : </b></td><td><input type=\"text\" name=\"ip\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . __('DURING') . " : </b></td><td>\n"
    . "<select id=\"dure\" name=\"dure\">\n"
    . "<option value=\"86400\">". _1JOUR ."</option>\n"
    . "<option value=\"604800\">". _7JOUR ."</option>\n"
    . "<option value=\"2678400\">". _1MOIS ."</option>\n"
    . "<option value=\"31708800\">". _1AN ."</option>\n"
    . "<option value=\"0\">". _AVIE ."</option>\n"
    . "</select></td></tr>\n"
    . "<tr><td colspan=\"2\"><b>" . __('REASON') . " :</b><br /><textarea class=\"editor\" name=\"texte\" rows=\"10\" cols=\"55\"></textarea></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _TOBAN . "\" /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=user&amp;op=main_ip\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function edit_ip($ip_id)
{
    global $language;

    $sql = mysql_query("SELECT ip, pseudo, email, dure, texte FROM " . BANNED_TABLE . " WHERE id = '" . $ip_id . "'");
    list($ip, $pseudo, $email, $dure, $text_ban) = mysql_fetch_array($sql);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _USERADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/user.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=user&amp;op=modif_ip\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr><td><b>" . _NICK . " : </b></td><td><input type=\"text\" name=\"pseudo\" size=\"30\" value=\"" . $pseudo . "\" /></td></tr>\n"
    . "<tr><td><b>" . _MAIL . " : </b></td><td><input type=\"text\" name=\"email\" size=\"40\" value=\"" . $email . "\" /></td></tr>\n"
    . "<tr><td><b>" . _IP . " : </b></td><td><input type=\"text\" name=\"ip\" size=\"30\" value=\"" . $ip . "\" /></td></tr>\n"
    . "<tr><td><b>" . __('DURING') . " : </b></td><td>\n"
    . "<select id=\"dure\" name=\"dure\" value=\"" . $dure . "\">\n"
    . "<option value=\"86400\">". _1JOUR ."</option>\n"
    . "<option value=\"604800\">". _7JOUR ."</option>\n"
    . "<option value=\"2678400\">". _1MOIS ."</option>\n"
    . "<option value=\"31708800\">". _1AN ."</option>\n"
    . "<option value=\"0\">". _AVIE ."</option>\n"
    . "</select></td></tr>\n"
    . "<tr><td colspan=\"2\"><b>" . __('REASON') . " :</b><br /><textarea class=\"editor\" name=\"texte\" rows=\"10\" cols=\"55\">" . $text_ban . "</textarea></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"ip_id\" value=\"" . $ip_id . "\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISIP . "\" /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=user&amp;op=main_ip\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";

}

function send_ip($ip, $pseudo, $email, $dure, $texte)
{
    global $nuked, $user;
    $pseudo = mysql_real_escape_string(stripslashes($pseudo));
    $texte = mysql_real_escape_string(stripslashes($texte));
    if($dure == 0 || $dure ==86400 ||$dure ==604800 ||$dure ==2678400 ||$dure == 31708800)
    {
        $sql = mysql_query("INSERT INTO " . BANNED_TABLE . " ( `id` , `ip` , `pseudo` , `email` ,`date` ,`dure` , `texte` ) VALUES ( '' , '" . $ip . "' , '" . $pseudo . "' , '" . $email . "', '" . time() . "' , '" . $dure . "' , '" . $texte . "' )");
    }
    else
    {
        return;
    }

    saveUserAction(_ACTIONADDBAN .': '. $pseudo);

    printNotification(_IPADD, 'success');
    redirect("index.php?file=Admin&page=user&op=main_ip", 2);
}

function modif_ip($ip_id, $ip, $pseudo, $email, $dure, $texte)
{
    global $nuked, $user;
    $pseudo = mysql_real_escape_string(stripslashes($pseudo));
    $texte = mysql_real_escape_string(stripslashes($texte));

    if($dure == 0 || $dure ==86400 ||$dure ==604800 ||$dure ==2678400 ||$dure == 31708800)
    {
    $sql = mysql_query("UPDATE " . BANNED_TABLE . " SET ip = '" . $ip . "', pseudo = '" . $pseudo . "', email = '" . $email . "', dure = '" . $dure . "', texte = '" . $texte . "' WHERE id = '" . $ip_id . "'");
    }
    else
    {
        return;
    }

    saveUserAction(_ACTIONMODIFBAN .': '. $pseudo);

    printNotification(_IPMODIF, 'success');
    redirect("index.php?file=Admin&page=user&op=main_ip", 2);
}

function del_ip($ip_id)
{
    global $nuked, $user;
        $sql2 = mysql_query("SELECT pseudo FROM " . BANNED_TABLE . " WHERE id = '" . $ip_id . "'");
    list($pseudo) = mysql_fetch_array($sql2);
    $pseudo = mysql_real_escape_string($pseudo);
    $sql = mysql_query("DELETE FROM " . BANNED_TABLE . " WHERE id = '" . $ip_id . "'");

    saveUserAction(_ACTIONSUPBAN .': '. $pseudo);

    printNotification(_IPDEL, 'success');
    redirect("index.php?file=Admin&page=user&op=main_ip", 2);
}

function validation($id_user)
{
    global $nuked;

    $date2 = nkDate(time());
    $sql = mysql_query("SELECT pseudo, mail FROM " . USER_TABLE . " WHERE id = '" . $id_user . "'");
    list($pseudo, $mail) = mysql_fetch_array($sql);

    $upd = mysql_query("UPDATE " . USER_TABLE . " SET niveau = 1 WHERE id = '" . $id_user . "'");

$subject = $nuked['name'] . " : " . _REGISTRATION . ", " . $date2;
$corps = $pseudo . ", " . _VALIDREGISTRATION . "\r\n" . $nuked['url'] . "/index.php?file=User&op=login_screen\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
$from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];

$subject = @nkHtmlEntityDecode($subject);
$corps = @nkHtmlEntityDecode($corps);
$from = @nkHtmlEntityDecode($from);

mail($mail, $subject, $corps, $from);

    printNotification(_USERVALIDATE, 'success');
    redirect("index.php?file=Admin&page=user&op=main_valid", 2);
}

function main_valid()
{
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
. "<!--\n"
. "\n"
. "function deluser(pseudo, id)\n"
. "{\n"
. "if (confirm('" . _DELBLOCK . " '+pseudo+' ! " . _CONFIRM . "'))\n"
. "{document.location.href = 'index.php?file=Admin&page=user&op=del_user&id_user='+id;}\n"
. "}\n"
. "// -->\n"
. "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _USERVALIDATION . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/user.php\" rel=\"modal\">\n"
. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
. "</div></div>\n"
. "<div class=\"tab-content\" id=\"tab2\">\n";

nkAdminMenu(6);

echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
. "<tr>\n"
. "<td style=\"width: 20%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
. "<td style=\"width: 20%;\" align=\"center\"><b>" . _MAIL . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _DATEUSER . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _VALIDUSER . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $theday = time();
    $sql = mysql_query("SELECT id, pseudo, mail, date FROM " . USER_TABLE . " WHERE niveau = 0 ORDER BY date");
    $nb_user = mysql_num_rows($sql);
    $compteur = 0;
    while (list($id_user, $pseudo, $mail, $date) = mysql_fetch_array($sql))
    {
        if ($nuked['validation'] == "admin")
        {
            $limit_time = $date + 864000;
        }
        else
        {
            $limit_time = $date + 86400;
        }

        $user_date = nkDate($date);

        if ($limit_time < $theday)
        {
            $compteur++;
            $del = mysql_query("DELETE FROM " . USER_TABLE . " WHERE niveau = 0 AND id = '" . $id_user . "'");
        }


        echo "<tr>"
        . "<td style=\"width: 20%;\">&nbsp;" . $pseudo . "</td>"
        . "<td style=\"width: 25%;\" align=\"center\"><a href=\"mailto:" . $mail . "\">" . $mail . "</a></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">" . $user_date . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=user&amp;op=validation&amp;id_user=" . $id_user . "\"><img style=\"border: 0;\" src=\"images/valid.gif\" alt=\"\" title=\"" . _VALIDTHISUSER . "\" /></a></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=user&amp;op=edit_user&amp;id_user=" . $id_user . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITUSER . "\" /></a></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:deluser('" . addslashes($pseudo) . "', '" . $id_user . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELETEUSER . "\" /></a></td></tr>\n";
    }
    if ($compteur > 0)
    {
        if($compteur == 1)
        {
            $text = "".$compteur." "._1USNOTACTION."";
        }
        else
        {
            $text = "".$compteur." "._USNOTACTION."";
        }

        saveNotification($text, 3);
    }
    if ($nb_user == 0)
    {
        echo "<tr><td align=\"center\" colspan=\"6\">" . _NOUSERVALIDATION . "</td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=user\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

/**
    * Delete moderator from FORUM_TABLE with a user ID
    * @param integer $idUser : a user ID
    * @return bool : true if delete success, false if not
    */
function delModerator($idUser)
{
    $resultQuery = mysql_query("SELECT id,moderateurs FROM " . FORUM_TABLE . " WHERE moderateurs LIKE '%" . $idUser . "%'");
    while (list($forumID, $listModos) = mysql_fetch_row($resultQuery))
    {
        if (is_int(strpos($listModos, '|'))) //Multiple moderators in this category
        {
            $tmpListModos = explode('|', $listModos);
            $tmpKey = array_search($idUser, $tmpListModos);
            if ($tmpKey !== false)
            {
                unset($tmpListModos[$tmpKey]);
                $tmpListModos = implode('|', $tmpListModos);
                $updateQuery = mysql_query("UPDATE " . FORUM_TABLE . " SET moderateurs = '" . $tmpListModos . "' WHERE id = '" . $forumID . "'");
            }
        }
        else
        {
            if ($idUser == $listModos) // Only one moderator in this category
            {
                $updateQuery = mysql_query("UPDATE " . FORUM_TABLE . " SET moderateurs = '' WHERE id = '" . $forumID . "'");
            }
            // Else, no moderator in this category
        }
    }
    if ($resultQuery)
        return true;
    else
        return false;
}


function getUserSocialList() {
    static $dbrUsersSocial;

    if (! isset($dbrUsersSocial)) {
        $dbrUsersSocial = nkDB_selectMany(
            'SELECT name, field, translateName, active
            FROM '. USER_SOCIAL_TABLE
        );
    }

    return $dbrUsersSocial;
}

// Display editing user social settings form
function main_config() {
    global $nuked;

    require_once 'Includes/nkUserSocial.php';
    require_once 'Includes/nkForm.php';
    require_once 'modules/Admin/config/userSocial.php';

    $userSocialList = getUserSocialList();
    $userSocialForm = getUserSocialFormCfg();

    $userSocialItems = array();

    foreach ($userSocialList as $userSocial) {
        if ($userSocial['active'] == 1)
            $value = 'on';
        else
            $value = 'off';

        $userSocialItems[$userSocial['field']] = array(
            'label'             => nkUserSocial_getLabel($userSocial),
            'type'              => 'checkbox',
            'inputValue'        => 'on',
            'value'             => $value
        );
    }

    $userSocialForm['items'] = array_merge($userSocialItems, $userSocialForm['items']);
    $userSocialForm['items']['user_social_level']['value'] = $nuked['user_social_level'];

    echo applyTemplate('contentBox', array(
        'title'     => _USERCONFIG, //__('ADMIN_USER_SOCIAL'),
        'helpFile'  => 'User',
        'content'   => nkForm_generate($userSocialForm)
    ));

    nkTemplate_addJS(
        '$("#usCheckAll").on("click", function() {' ."\n"
        . "\t" .'if ($(this).attr("data-click-state") == 1) {' ."\n"
        . "\t\t" .'$(this).attr("data-click-state", 0);' ."\n"
        . "\t\t" .'$(this).val("'. _UNCHECKALL .'");' ."\n"
        . "\t\t" .'$("#userSocialForm input[type=checkbox]").prop("checked", false);' ."\n"
        . "\t" .'} else {' ."\n"
        . "\t\t" .'$(this).attr("data-click-state", 1);' ."\n"
        . "\t\t" .'$(this).val("'. _CHECKALL .'");' ."\n"
        . "\t\t" .'$("#userSocialForm input[type=checkbox]").prop("checked", true);' ."\n"
        . "\t" .'}' ."\n\n"
        . "\t" .'return false;' ."\n"
        . '});' ."\n\n",
        'jqueryDomReady'
    );
}

// Save user social settings
function send_config() {
    global $nuked;

    require_once 'Includes/nkUserSocial.php';

    $userSocialList = getUserSocialList();
 
    foreach ($userSocialList as $userSocial) {
        if (isset($_POST[$userSocial['field']]) && $_POST[$userSocial['field']] == 'on')
            $active = 1;
        else
            $active = 0;

        if ($active != $userSocial['active']) {
            nkDB_update(USER_SOCIAL_TABLE, array(
                    'active' => $active
                ),
                'field = \''. $userSocial['field'] .'\''
            );
        }
    }

    if (isset($_POST['user_social_level'])
        && $_POST['user_social_level'] >= 0
        && $_POST['user_social_level'] <= 9
    ) {
        if ($_POST['user_social_level'] != $nuked['user_social_level']) {
            nkDB_update(CONFIG_TABLE, array(
                    'value' => $_POST['user_social_level']
                ),
                'name = \'user_social_level\''
            );
        }
    }

    saveUserAction(__('ACTION_MODIF_USER_SOCIAL'));// _ACTIONMODIFUSER .'.'

    printNotification(__('USER_SOCIAL_MODIFIED'), 'success');// _CONFIGUPDATED
    redirect('index.php?file=Admin&page=user', 2);
}

function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=user">
                    <img src="modules/Admin/images/icons/members.png" alt="icon" />
                    <span><?php echo _USERADMIN; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=user&amp;op=add_user">
                    <img src="modules/Admin/images/icons/adduser.png" alt="icon" />
                    <span><?php echo _ADDUSER; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 4 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=user&amp;op=main_config">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _USERCONFIG; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 6 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=user&amp;op=main_valid">
                    <img src="modules/Admin/images/icons/validuser.png" alt="icon" />
                    <span><?php echo _USERVALIDATION; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 7 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=user&amp;op=main_ip">
                    <img src="modules/Admin/images/icons/banuser.png" alt="icon" />
                    <span><?php echo _BAN; ?></span>
                </a>
            </li>               
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($GLOBALS['op']) {
    case "main_config":
        main_config();
        break;

    case "send_config":
        send_config();
        break;

    case "update_user":
        update_user($_REQUEST['id_user']);
        break;

    case "add_user":
        add_user();
        break;

    case "do_user":
        do_user();
        break;

    case "edit_user":
        edit_user($_REQUEST['id_user']);
        break;

    case "del_user":
        del_user($_REQUEST['id_user']);
        break;

    case "main_ip":
        main_ip();
        break;

    case "add_ip":
        add_ip();
        break;

    case "edit_ip":
        edit_ip($_REQUEST['ip_id']);
        break;

    case "send_ip":
        send_ip($_REQUEST['ip'], $_REQUEST['pseudo'], $_REQUEST['email'],$_REQUEST['dure'], $_REQUEST['texte']);
        break;

    case "modif_ip":
        modif_ip($_REQUEST['ip_id'], $_REQUEST['ip'], $_REQUEST['pseudo'], $_REQUEST['email'],$_REQUEST['dure'], $_REQUEST['texte']);
        break;

    case "del_ip":
        del_ip($_REQUEST['ip_id']);
        break;

    case "main_valid":
        main_valid();
        break;

    case "validation":
        validation($_REQUEST['id_user']);
        break;

    case "main":
        main();
        break;

    case 'getTeamSelector' :
        getTeamSelector();
        break;

    default:
        main();
        break;
}

?>