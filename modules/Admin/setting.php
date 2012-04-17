<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $language;

translate('modules/Admin/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');

$visiteur = ($user) ? $user[1] : 0;

if ($visiteur == 9)
{
    function select_theme($mod)
    {
        $handle = opendir('themes/');
        while (false !== ($f = readdir($handle)))
        {
            if ($f != '.' && $f != '..' && $f != 'CVS' && $f != 'index.html' && !preg_match('`[.]`', $f))
            {
                if ($mod == $f) $checked = 'selected="selected"';
                else $checked = '';

                if (is_file('themes/' . $f . '/theme.php')) echo '<option value="' . $f . '" ' . $checked . '>' . $f . '</option>';
            }
        }
        closedir($handle);
    }

    function select_langue($mod)
    {
        if ($rep = opendir('lang/'))
        {
            while (false !== ($f = readdir($rep)))
            {
                if ($f != '..' && $f != '.' && $f != 'index.html')
                {
                    list ($langfile, ,) = explode ('.', $f);

                    if ($mod == $langfile) $checked = "selected=\"selected\"";
                    else $checked = "";

                    echo "<option value=\"" . $langfile . "\" " . $checked . ">" . $langfile . "</option>\n";
                }
            }
            closedir($rep);
        }
    }

    function select_mod($mod)
    {
        global $nuked;

        $sql = mysql_query('SELECT nom FROM ' . MODULES_TABLE . ' ORDER BY nom');
        while (list($nom) = mysql_fetch_array($sql))
        {
            if ($mod == $nom) $checked = 'selected="selected"';
            else $checked = '';

            if (is_file('modules/' . $nom . '/index.php')) echo '<option value="' . $nom . '" ' . $checked . '>' . $nom . '</option>',"\n";
        }
    }
    
    function select_timeformat($tft)
    {
        global $nuked;
            
            $timeformatTable = array(
                "%A, %B %d, %Y - %H:%M:%S",
                "%A, %d %B, %Y - %H:%M:%S",
                "%A, %Y, %d %B  - %H:%M:%S",
                "%A, %B %d, %Y  - %I:%M:%S %p",
                "%A, %d %B, %Y  - %I:%M:%S %p",
                "%A, %Y, %d %B  - %I:%M:%S %p",
                "%A, %d. %B %Y  - %I:%M:%S %p",
                "%a %Y-%m-%d %H:%M:%S",
                "%a %m/%d/%Y %H:%M:%S",
                "%a %d/%m/%Y %H:%M:%S",
                "%a %Y/%m/%d %H:%M:%S",
                "%B %d, %Y - %H:%M:%S",
                "%d %B, %Y - %H:%M:%S",
                "%Y, %B %d  - %H:%M:%S",
                "%a %m/%d/%Y %I:%M:%S %p",
                "%a %d/%m/%Y %I:%M:%S %p",
                "%a %Y/%m/%d %I:%M:%S %p",
                "%B %d, %Y - %I:%M:%S %p",
                "%d %B, %Y - %I:%M:%S %p",
                "%Y, %B %d  - %I:%M:%S %p",
                "%d. %B %Y  - %I:%M:%S %p",
                "%Y-%m-%d %H:%M:%S",
                "%m/%d/%Y",
                "%d/%m/%Y",
                "%m/%d/%Y - %H:%M:%S",
                "%d/%m/%Y - %H:%M:%S",
                "%Y/%m/%d - %H:%M:%S",
                "%d.%m.%Y - %H:%M:%S",
                "%m/%d/%Y - %I:%M:%S %p",
                "%d/%m/%Y - %I:%M:%S %p",
                "%Y/%m/%d - %I:%M:%S %p",
                "%b %d %Y - %H:%M:%S",
                "%d %b %Y - %H:%M:%S",
                "%Y %b %d - %H:%M:%S",
                "%b %d %Y - %I:%M:%S %p",
                "%d %b %Y - %I:%M:%S %p",
                "%Y %b %d - %I:%M:%S %p",
            );
            
            foreach($timeformatTable as $key)
            {
                $checked = ($tft == $key) ? 'selected="selected"' : '';
                $day = time();
                date_default_timezone_set($nuked['datezone']);
                // iconv pour éviter les caractère spéciaux dans la date
                $echo = iconv('UTF-8','ISO-8859-1',strftime($key, $day));
                echo "<option value=\"" . $key . "\" " . $checked . ">" . $echo . "</option>\n";
            }
    }
    
    function select_timezone($tze)
    {
        global $nuked;
            
            $timezoneTable = array( "-1200" => "(GMT -12:00) Eniwetok, Kwajalein",
                                    "-1100" => "(GMT -11:00) Midway Island, Samoa",
                                    "-1000" => "(GMT -10:00) Hawaii",
                                    "-0900" => "(GMT -9:00) Alaska",
                                    "-0800" => "(GMT -8:00) Pacific Time (US & Canada)",
                                    "-0700" => "(GMT -7:00) Mountain Time (US & Canada)",
                                    "-0600" => "(GMT -6:00) Central Time (US & Canada), Mexico City",
                                    "-0500" => "(GMT -5:00) Eastern Time (US & Canada), Bogota, Lima",
                                    "-0400" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
                                    "-0330" => "(GMT -3:30) Newfoundland",
                                    "-0300" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
                                    "-0200" => "(GMT -2:00) Mid-Atlantic",
                                    "-0100" => "(GMT -1:00 hour) Azores, Cape Verde Islands",
                                    "+0000" => "(GMT) Western Europe Time, London, Lisbon, Casablanca",
                                    "+0100" => "(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris",
                                    "+0200" => "(GMT +2:00) Kaliningrad, South Africa",
                                    "+0300" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
                                    "+0330" => "(GMT +3:30) Tehran",
                                    "+0400" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
                                    "+0430" => "(GMT +4:30) Kabul",
                                    "+0500" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
                                    "+0530" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
                                    "+0600" => "(GMT +6:00) Almaty, Dhaka, Colombo",
                                    "+0700" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
                                    "+0800" => "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
                                    "+0900" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
                                    "+0930" => "(GMT +9:30) Adelaide, Darwin",
                                    "+1000" => "(GMT +10:00) Eastern Australia, Guam, Vladivostok",
                                    "+1100" => "(GMT +11:00) Magadan, Solomon Islands, New Caledonia",
                                    "+1200" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka"
                                   );
            foreach($timezoneTable as $cle=>$valeur) 
            {
                $checked = ($tze == $cle) ? 'selected="selected"' : '';
                echo '<option value="' . $cle . '" ' . $checked . '>' . $valeur . '</option>';
            }
    }

    function edit_config()
    {
        global $nuked, $language;

        admintop();
    
        echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
        . '<div class="content-box-header"><h3>' . _PREFGEN . '</h3>',"\n";
        ?>
        <script type="text/javascript">
        <!--
        // Interdire les caractères spéciaux (pour le nom des cookies)
        function special_caract(evt) {
            var keyCode = evt.which ? evt.which : evt.keyCode;
            if (keyCode==9) return true;
            var interdit = 'ààâäãçéèêëìîïòôöõµùûüñ &\?!:\.;,\t#~"^¨@%\$£?²¤§%\*()[]{}-_=+<>|\\/`\'';
            if (interdit.indexOf(String.fromCharCode(keyCode)) >= 0) {
                alert('<?php echo _SPECCNOTALLOW; ?>');
                return false;
            }
        }
        -->
        </script>
        <?php

        echo "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/preference.php\"  rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a></div>\n"
        . "</div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><br/>\n"
        ."<div style=\"width:80%; margin:auto;\">\n"
        . "<div class=\"notification attention png_bg\">\n"
        . "<div>" . _INFOSETTING . "</div></div></div><br/>\n"
        . "<form method=\"post\" action=\"index.php?file=Admin&amp;page=setting&amp;op=save_config\">\n"
        . "<div style=\"width:96%\"><table style=\"margin-left: 2%;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr><td colspan=\"2\"><big><b>" . _GENERAL . "</b></big></td></tr>\n"
        . "<tr><td>" . _SITENAME . " :</td><td><input type=\"text\" name=\"name\" size=\"40\" value=\"" . $nuked['name'] . "\" /></td></tr>\n"
        . "<tr><td>" . _SLOGAN . " : </td><td><input type=\"text\" name=\"slogan\" size=\"40\" value=\"" . $nuked['slogan'] . "\" /></td></tr>\n"
        . "<tr><td>" . _TAGPRE . " :</td><td><input type=\"text\" name=\"tag_pre\" size=\"10\" value=\"" . $nuked['tag_pre'] . "\" />&nbsp;" . _TAGSUF . " :<input type=\"text\" name=\"tag_suf\" size=\"10\" value=\"" . $nuked['tag_suf'] . "\" /></td></tr>\n"
        . "<tr><td>" . _SITEURL . " :</td><td><input type=\"text\" name=\"url\" size=\"40\" value=\"" . $nuked['url'] . "\" /></td></tr>\n"
        . "<tr><td>" . _DATEFORMAT . " :</td><td><select name=\"dateformat\">\n";

        select_timeformat($nuked['dateformat']);
        
        echo "</select></td></tr>\n";
        echo "<tr><td>" . _DATEZONE . " :</td><td><select name=\"datezone\">\n";

        select_timezone($nuked['datezone']);
        $time = time();
        $date = nkDate($time);
        echo "</select><br /><span>" . _DATEADJUST ."&nbsp;" . $date . " </span></td></tr><tr><td>" . _ADMINMAIL . " :</td><td><input type=\"text\" name=\"mail\" size=\"40\" value=\"" . $nuked['mail'] . "\" /></td></tr>\n"
        . "<tr><td>" . _FOOTMESS . " :</td><td><textarea class=\"editor\" name=\"footmessage\" cols=\"50\" rows=\"6\">" . $nuked['footmessage'] . "</textarea></td></tr>\n"    
        . "<tr><td>" . _SITESTATUS . " :</td><td><select name=\"nk_status\">\n";

        if ($nuked['nk_status'] == "open")
        {
            $checked11 = "selected=\"selected\"";
            $checked12 = "";
        }
        else if ($nuked['nk_status'] == "closed")
        {
            $checked12 = "selected=\"selected\"";
            $checked11 = "";
        }
        if ($nuked['screen'] == "on") $screen = "checked=\"checked\"";
        else $screen = "";

        echo "<option value=\"open\" " . $checked11 . ">" . _OPENED . "</option>\n"
        . "<option value=\"closed\" " . $checked12 . ">" . _CLOSED . "</option>\n"
        . "</select></td></tr><tr><td>" . _SITEINDEX . " :</td><td><select name=\"index_site\">\n";

        select_mod($nuked['index_site']);

        echo "</select></td></tr><tr><td>" . _THEMEDEF . " :</td><td><select name=\"theme\">\n";

        select_theme($nuked['theme']);

        echo "</select></td></tr><tr><td>" . _LANGDEF . " :</td><td><select name=\"langue\">\n";

        select_langue($nuked['langue']);

        echo "</select></td></tr>\n";

        if ($nuked['inscription'] == "on")
    {
            $checked1 = "selected=\"selected\"";
            $checked2 = "";
            $checked3 = "";
    }
        else if ($nuked['inscription'] == "off")
    {
            $checked2 = "selected=\"selected\"";
            $checked1 = "";
            $checked3 = "";
    }
        else if ($nuked['inscription'] == "mail")
    {
            $checked3 = "selected=\"selected\"";
            $checked1 = "";
            $checked2 = "";
    }


        if ($nuked['inscription_avert'] == "on") $checked4 = "checked=\"checked\"";
        else $checked4 = "";


        if ($nuked['validation'] == "auto")
    {
            $checked5 = "selected=\"selected\"";
            $checked6 = "";
            $checked7 = "";
    }
        else if ($nuked['validation'] == "admin")
    {
            $checked6 = "selected=\"selected\"";
            $checked5 = "";
            $checked7 = "";
    }
        else if ($nuked['validation'] == "mail")
    {
            $checked7 = "selected=\"selected\"";
            $checked5 = "";
            $checked6 = "";
    }

	if($nuked['stats_share'] == "1") $checkedstats = "checked=\"checked\"";
	    else  $checkedstats = "";

    if ($nuked['avatar_upload'] == "on") $checked8 = "checked=\"checked\"";
        else $checked8 = "";

    if ($nuked['avatar_url'] == "on") $checked9 = "checked=\"checked\"";
        else $checked9 = "";

    if ($nuked['user_delete'] == "on") $checked10 = "checked=\"checked\"";
        else $checked10 = "";
        
    if ($nuked['video_editeur'] == "on") $checked14 = "checked=\"checked\"";
        else $checked14 = "";

    if ($nuked['scayt_editeur'] == "on") $checked13 = "checked=\"checked\"";
        else $checked13 = "";

    $checked12 = ($nuked['time_generate'] == 'on') ? 'checked="checked"' : '';

    $nuked['level_analys']==-1?$level_analys=_OFFMODULE:$level_analys=$nuked['level_analys'];
    echo "<tr><td>" . _SCREENHOT . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"screen\" value=\"on\" " . $screen . " /></td></tr>\n"
    . "<tr><td>" . _REGISTRATION . " :</td><td><select name=\"inscription\">\n"
    . "<option value=\"on\" " . $checked1 . ">" . _OPEN . "</option>\n"
    . "<option value=\"off\" " . $checked2 . ">" . _CLOSE . "</option>\n"
    . "<option value=\"mail\" " . $checked3 . ">" . _BYMAIL . "</option></select></td></tr>\n"
    . "<tr><td>" . _VALIDATION . " :</td><td><select name=\"validation\">\n"
    . "<option value=\"auto\" " . $checked5 . ">" . _AUTO . "</option>\n"
    . "<option value=\"admin\" " . $checked6 . ">" . _ADMINISTRATOR . "</option>\n"
    . "<option value=\"mail\" " . $checked7 . ">" . _BYMAIL . "</option></select></td></tr>\n"
    . "<tr><td>" . _USERDELETE . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"user_delete\" value=\"on\" " . $checked10 . " /></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><big><b>" . _EDITEUR . "</b></big></td></tr>\n"
    . "<tr><td>" . _VIDEOEDITEUR . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"video_editeur\" value=\"on\" " . $checked14 . " /></td></tr>\n"
    . "<tr><td>" . _SCAYTEDITEUR . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"scayt_editeur\" value=\"on\" " . $checked13 . " /></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><big><b>" . _SITEMEMBERS . "</b></big></td></tr>\n"
    . "<tr><td>" . _NUMBERMEMBER . " :</td><td><input type=\"text\" name=\"max_members\" size=\"2\" value=\"" . $nuked['max_members'] . "\" /></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><big><b>" . _AVATARS . "</b></big></td></tr>\n"
    . "<tr><td>" . _AVATARUPLOAD . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"avatar_upload\" value=\"on\" " . $checked8 . " /></td></tr>\n"
    . "<tr><td>" . _AVATARURL . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"avatar_url\" value=\"on\" " . $checked9 . " /></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><big><b>" . _REGISTRATION . "</b></big></td></tr>"
    . "<tr><td>" . _REGISTERMAIL . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"inscription_avert\" value=\"on\" " . $checked4 . " /></td></tr>\n"
    . "<tr><td>" . _REGISTERDISC . " :</td><td><textarea class=\"editor\" name=\"inscription_charte\" cols=\"50\" rows=\"6\">" . $nuked['inscription_charte'] . "</textarea></td></tr>\n"
    . "<tr><td>" . _REGISTERTXT . " :</td><td><textarea class=\"editor\" name=\"inscription_mail\" cols=\"50\" rows=\"6\">" . $nuked['inscription_mail'] . "</textarea></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><big><b>" . _STATS . "</b></big></td></tr>\n"
    . "<tr><td>" . _VISITTIME . " :</td><td><input type=\"text\" name=\"visit_delay\" size=\"2\" value=\"" . $nuked['visit_delay'] . "\" /></td></tr>\n"
    . "<tr><td>" . _LEVELANALYS . " :</td><td><select name=\"level_analys\"><option value=\"" . $nuked['level_analys'] . "\">" . $level_analys . "</option>\n"
    . "<option value='-1'>" . _OFFMODULE . "</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select></td></tr>\n"
    . "<tr><td>" . _DISPLYGNRATETME . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"time_generate\" value=\"on\" " . $checked12 . " /></td></tr>\n";
	include("Includes/nkStats.php");
	$data = getStats($nuked);
			
	$string = "";
	foreach($data as $donnee => $value)
	{
		$string .= "<div style='display:inline-block; width:300px;'><span style='font-weight:bold'>".$donnee ." : </span><span>". $value ."</span></div>";
	}
	echo "<tr><td>" . _SHARESTATS . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"stats_share\" value=\"1\" " . $checkedstats . " />  (<a href=\"index.php?file=Admin&page=setting\" id=seestats>" . _SEESHARE ."</a>)<br/><small>". _SHAREREASON."</small></td></tr>\n"
	."<tr style='display:none' id=seestatsblock><td colspan=2>". $string ."</td></tr>\n"	
    . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><big><b>" . _OPTIONCONNEX . "</b></big></td></tr>\n"
    . "<tr><td>" . _COOKIENAME . " :</td><td><input type=\"text\" name=\"cookiename\" size=\"20\" value=\"" . $nuked['cookiename'] . "\" onkeypress=\"return special_caract(event);\" /></td></tr>\n"
    . "<tr><td>" . _CONNEXMIN . " :</td><td><input type=\"text\" name=\"sess_inactivemins\" size=\"2\" value=\"" . $nuked['sess_inactivemins'] . "\" /></td></tr>\n"
    . "<tr><td>" . _CONNEXDAY . " :</td><td><input type=\"text\" name=\"sess_days_limit\" size=\"3\" value=\"" . $nuked['sess_days_limit'] . "\" /></td></tr>\n"
    . "<tr><td>" . _CONNEXSEC . " :</td><td><input type=\"text\" name=\"nbc_timeout\" size=\"3\" value=\"" . $nuked['nbc_timeout'] . "\" /></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><big><b>" . _METATAG . "</b></big></td></tr>\n"
    . "<tr><td>" . _METAWORDS . " :</td><td><input type=\"text\" name=\"keyword\" size=\"40\" value=\"" . $nuked['keyword'] . "\" /></td></tr>\n"
    . "<tr><td>" . _METADESC . " :</td><td><textarea name=\"description\" cols=\"50\" rows=\"6\">" . $nuked['description'] . "</textarea></td></tr>\n"
    . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" name=\"ok\" value=\"" . _MODIF . "\" /></div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div></form><br />\n";
    echo "</div></div></div>\n";
        adminfoot();
    }

    function save_config()
    {
        global $nuked, $user;
        
		if ($_REQUEST['stats_share'] != "1") $_REQUEST['stats_share'] = "0";
        if ($_REQUEST['inscription_avert'] != "on") $_REQUEST['inscription_avert'] = "off";
        if ($_REQUEST['time_generate'] != 'on') $_REQUEST['time_generate'] = 'off';
        if ($_REQUEST['avatar_upload'] != "on") $_REQUEST['avatar_upload'] = "off";
        if ($_REQUEST['avatar_url'] != "on") $_REQUEST['avatar_url'] = "off";
        if ($_REQUEST['user_delete'] != "on") $_REQUEST['user_delete'] = "off";
        if ($_REQUEST['video_editeur'] != "on") $_REQUEST['video_editeur'] = "off";
        if ($_REQUEST['scayt_editeur'] != "on") $_REQUEST['scayt_editeur'] = "off";
        if ($_REQUEST['screen'] != "on") $_REQUEST['screen'] = "off";
        if (substr($_REQUEST['url'], -1) == "/") $_REQUEST['url'] = substr($_REQUEST['url'], 0, -1);
        $_REQUEST['cookiename'] = str_replace(' ','',$_REQUEST['cookiename']);
        
        $_REQUEST['inscription_charte'] = html_entity_decode($_REQUEST['inscription_charte']);
        $_REQUEST['inscription_mail'] = html_entity_decode($_REQUEST['inscription_mail']);
        $_REQUEST['footmessage'] = html_entity_decode($_REQUEST['footmessage']);

        if($_REQUEST['theme'] !== $nuked['theme'])
            mysql_query('UPDATE ' . USER_TABLE . ' SET user_theme = ""');
        
        $sql = mysql_query("SELECT name, value  FROM " . CONFIG_TABLE);
        while (list($config_name, $config_value) = mysql_fetch_array($sql))
        {
            $default_config[$config_name] = $config_value;
            $new[$config_name] = (isset($_REQUEST[$config_name])) ? $_REQUEST[$config_name] : $default_config[$config_name];
            $new_value = mysql_real_escape_string(stripslashes($new[$config_name]));
            $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $new_value . "' WHERE name = '" . $config_name . "'");
        }
        // Action
        $texteaction = "". _ACTIONSETTING ."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        admintop();
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _CONFIGSAVE . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php', 'index.php?file=Admin');\n"
            ."}\n"
            ."</script>\n";
        adminfoot();
    }

    switch ($_REQUEST['op'])
    {
        case "save_config":
            save_config($_POST);
            break;

        default:
            edit_config();
            break;
    }

}
else if ($visiteur > 1)
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
}
else
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
}
?>
