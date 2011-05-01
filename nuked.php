<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN 1.7 - PHP Portal                                              //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
	exit('You can\'t run this file alone.');
}

connect();

$nuked = array();

$sql_conf = mysql_query("SELECT name, value FROM " . $db_prefix . "_config");
while ($row = mysql_fetch_array($sql_conf))
{
	$nuked[$row['name']] = htmlentities($row['value'], ENT_NOQUOTES);
}
unset( $sql_conf, $row );

$nuked['prefix'] = $db_prefix;
$nuked['inscription_charte'] = secu_html(html_entity_decode($nuked['inscription_charte']));
$nuked['inscription_mail'] = secu_html(html_entity_decode($nuked['inscription_mail']));
$nuked['footmessage'] = secu_html(html_entity_decode($nuked['footmessage']));
$nuked['defie_charte'] = secu_html(html_entity_decode($nuked['defie_charte']));
$nuked['recrute_charte'] = secu_html(html_entity_decode($nuked['recrute_charte']));


include ("Includes/constants.php");
session_set_save_handler('session_open', 'session_close', 'session_read', 'session_write', 'session_delete', 'session_gc');

@session_name('nuked');
@session_start();
if (session_id() == '') {
	exit('Erreur dans la création de la session annonyme');
}

include ("Includes/nkSessions.php");


if (!isset($_REQUEST['file']) || $_REQUEST['file'] == null)
{
	$_REQUEST['file'] = $nuked['index_site'];
}

if (!isset($_REQUEST['op']) || $_REQUEST['op'] == null)
{
	$_REQUEST['op'] = "index";
}

if ($_REQUEST[$nuked['cookiename'] . '_user_theme'] != "" && is_file("themes/" . $_REQUEST[$nuked['cookiename'] . '_user_theme'] . "/theme.php"))
{
	$theme = $_REQUEST[$nuked['cookiename'] . '_user_theme'];
}
else
{
	$theme = $nuked['theme'];
}

if ($_REQUEST[$nuked['cookiename'] . '_user_langue'] != "" && is_file("lang/" . $_REQUEST[$nuked['cookiename'] . '_user_langue'] . ".lang.php"))
{
	$language = $_REQUEST[$nuked['cookiename'] . '_user_langue'];
}
else
{
	$language = $nuked['langue'];
}

if (!isset($_REQUEST['nuked_nude']))
{
	if ($language == "french" && preg_match("`WIN`", PHP_OS)) setlocale (LC_TIME, "french");
	else if ($language == "french" && preg_match("`BSD`", PHP_OS)) setlocale (LC_TIME, "fr_FR.ISO8859-1");
	else if ($language == "french") setlocale (LC_TIME, "fr_FR");
	else setlocale (LC_TIME, $language);
}

function session_open($path, $name)
{
  return true;
}

function session_close()
{
  return true;
}

function session_read($id)
{
  global $nuked;

  connect();

  $sql = mysql_query("SELECT session_vars FROM " . TMPSES_TABLE . " WHERE session_id = '$id'");

  if ($sql === false)
  {
    return '';
  }
  else
  {
    return mysql_result($sql, 0);
  }
}

function session_write($id, $data)
{
  global $nuked;
  $id = mysql_escape_string($id);
  $data = mysql_escape_string($data);

  connect();

  $sql = mysql_query("INSERT INTO " . TMPSES_TABLE . " (session_id, session_start, session_vars) VALUES ('$id', " . time() . ", \"$data\")");


  if ($sql === false || mysql_affected_rows() == 0)
    $sql = mysql_query("UPDATE " . TMPSES_TABLE . " SET session_vars = \"$data\" WHERE session_id = '$id'");

  return $sql !== false;
}

function session_delete($id)
{
  global $nuked;
  $id = mysql_escape_string($id);

  connect();

  $sql = mysql_query("DELETE FROM " . TMPSES_TABLE . " WHERE session_id = '$id'");

  return $sql;
}

function session_gc($maxlife)
{
  global $nuked;
  $id = mysql_escape_string($id);
  $time = time() - $maxlife;

  connect();

  mysql_query("DELETE FROM " . TMPSES_TABLE . " WHERE session_start < $time");

  return true;
}

function autolink($text)
{
  $txt = '';
  $index = 0;

  while($index < strlen($text))
  {
    $pos = strpos($text, '<', $index);
    if ($pos === false)
      $pos = strlen($text);
    $txt .= preg_replace('`http://[^ <]+`i', '<a href="$0" onclick="window.open(this.href); return false;">$0</a>', substr($text, $index, $pos - $index));
    $index = $pos;

    $pos = strpos($text, '>', $index);
    if ($pos === false)
      $pos = strlen($text);
    $txt .= substr($text, $index, $pos - $index);
    $index = $pos;
  }
  return $txt;
}

function connect()
{
	global $global, $db, $language;
	$db = @mysql_connect($global['db_host'], $global['db_user'], $global['db_pass']);
	if (!$db)
	{
		if($language == "french")
		{
			echo "<div style=\"text-align: center;\">Veuillez nous excuser, le site web est actuellement indisponible !<br /></div>";
			echo "<div>Information :<br />Connexion SQL impossible.</div>";
		}
		else
		{
			echo "<div style=\"text-align: center;\">Sorry but the website is not available !<br /></div>";
			echo "<div>Information :<br />SQL connection impossible.</div>";
		}
		exit();
	}
	else
	{
		$connect = @mysql_select_db($global['db_name'], $db);
		if (!$connect)
		{
			if($language == "french")
			{
				echo "<div style=\"text-align: center;\">Veuillez nous excuser, le site web est actuellement indisponible !<br /></div>";
				echo "<div>Information :<br />Nom de base de données sql incorrect.</div>";
			}
			else
			{
				echo "<div style=\"text-align: center;\">Sorry but the website is not available !<br /></div>";
				echo "<div>Information :<br />Database SQL name incorrect.</div>";
			}
			exit();
		}
	}
}

function banip()
{
	global $nuked, $user_ip, $user, $language;

	$theday = time();

	$verif1 = mysql_query("SELECT id, pseudo, date, dure FROM " . BANNED_TABLE . " WHERE ip = '" . $user_ip . "'");
	list($id1, $pseudo1, $date1, $dure1) = mysql_fetch_array($verif1);
	if(isset($user[2]) && $user[2] != "")
	{
		$verif2 = mysql_query("SELECT id, pseudo, date, dure FROM " . BANNED_TABLE . " WHERE pseudo = '" . $user[2] . "'");
		list($id2, $pseudo2, $date2, $dure2) = mysql_fetch_array($verif2);
	}
	if(isset($_COOKIE['ip_ban']))
	{
	$verif3 = mysql_query("SELECT id, pseudo, date, dure FROM " . BANNED_TABLE . " WHERE ip = '" . $_COOKIE['ip_ban'] . "'");
	list($id3, $pseudo3, $date3, $dure3) = mysql_fetch_array($verif3);
	}
    $not = false;
	if(mysql_num_rows($verif1) > 0)
	{
		$limit_time = $date1 + $dure1;

        if ($limit_time < $theday AND $dure1 > 0)
        {
            $del1 = mysql_query("DELETE FROM " . BANNED_TABLE . " WHERE ip = '" . $user_ip . "'");
			if($language == "french")
			{
				$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$theday."', '4', '".$pseudo1." n\'est plus banni, sa période est arrivée à expiration: [<a href=\"index.php?file=Admin&page=user&op=main_ip\">Lien</a>].')");
			}
			else
			{
				$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$theday."', '4', '".$pseudo1." isn\'t ban, this period is arrived at expiration: [<a href=\"index.php?file=Admin&page=user&op=main_ip\">Link</a>].')");
			}
			$not = true;
			if(isset($_COOKIE['ip_ban']))
			{
				$_COOKIE['ip_ban'] = "";
			}
        }
	}
	if(isset($verif2) && mysql_num_rows($verif2) > 0 AND $user[2] != "")
		{
			$limit_time = $date2 + $dure2;

			if ($limit_time < $theday AND $dure2 > 0)
			{
				$del2 = mysql_query("DELETE FROM " . BANNED_TABLE . " WHERE pseudo = '" . $user[2] . "'");
				if($not ==false)
				{
					if($language == "french")
					{
						$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$theday."', '4', '".$pseudo2." n\'est plus banni, sa pÃ©riode est arrivÃ© Ã  expiration: [<a href=\"index.php?file=Admin&page=user&op=main_ip\">lien</a>].')");
					}
					else
					{
						$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$theday."', '4', '".$pseudo2." isn\'t ban, this period is arrived at expiration: [<a href=\"index.php?file=Admin&page=user&op=main_ip\">lien</a>].')");
					}
					$not = true;
				}
				if(isset($_COOKIE['ip_ban']))
				{
					$_COOKIE['ip_ban'] = "";
				}
			}
		}
	if(isset($verif3) && mysql_num_rows($verif3) > 0 AND isset($_COOKIE['ip_ban']))
		{
			$limit_time = $date3 + $dure3;

			if ($limit_time < $theday AND $dure3 > 0)
			{
				$del3 = mysql_query("DELETE FROM " . BANNED_TABLE . " WHERE ip = '" . $_COOKIE['ip_ban'] . "'");
				if($not ==false)
				{
					if($language == "french")
					{
						$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$theday."', '4', '".$pseudo3." n\'est plus banni, sa période est arrivée à expiration: [<a href=\"index.php?file=Admin&page=user&op=main_ip\">lien</a>].')");
					}
					else
					{
						$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$theday."', '4', '".$pseudo3." isn\'t ban, this period is arrived at expiration: [<a href=\"index.php?file=Admin&page=user&op=main_ip\">Link</a>].')");
					}
					$not = true;
				}
			}
		}
		else
		{
			$_COOKIE['ip_ban'] = "";
		}
	if(!isset($_COOKIE['ip_ban']))
	{
		$ip_ban = "";
	}
	else
	{
		$ip_ban = $_COOKIE['ip_ban'];
	}
	if ($ip_ban != "")
	{
		if ($ip_ban != $user_ip)
		{
			$sql = mysql_query("SELECT pseudo, email, texte FROM " . BANNED_TABLE . " WHERE ip = '" . $ip_ban . "'");
			$nb_ban = mysql_num_rows($sql);

			if ($nb_ban > 0)
			{
				$sql2 = mysql_query("SELECT id FROM " . BANNED_TABLE . " WHERE ip = '" . $user_ip . "'");
				$check_ban = mysql_num_rows($sql2);

				if ($check_ban == 0)
				{
					list($pseudo_ban, $email_ban, $texte_ban) = mysql_fetch_array($sql);
					$insert = mysql_query("INSERT INTO " . BANNED_TABLE . " ( `id` , `ip` , `pseudo`, `email`, `texte` ) VALUES ('', '" . $user_ip . "', '" . $pseudo_ban . "', '" . $email_ban . "', '" . $texte_ban . "')");
				}

				$ip_ban = $user_ip;
			}
			else
			{
				$ip_ban = "";
			}
		}
	}
	else
	{
		$nb_ban = 0;
		$sql = mysql_query("SELECT ip, pseudo FROM " . BANNED_TABLE . " ORDER BY id");
		while (list($ip_banned, $pseudo_banned) = mysql_fetch_array($sql))
		{
			if ($nb_ban == 0)
			{
				$bip = explode(".", $ip_banned);

				if (isset($bip[3]) && $bip[3] != "")
				{
					$banlist = $ip_banned;
					$verif_ip = $user_ip;
				}
				else
				{
					$banlist = $bip[0] . $bip[1] . $bip[2];
					$uip = explode(".", $user_ip);
					$verif_ip = $uip[0] . $uip[1] . $uip[2];
				}

				if ($verif_ip == $banlist)
				{
					$ip_ban = $ip_banned;
					$nb_ban++;
				}
				else if ($user[2] != "" && $pseudo_banned == $user[2])
				{
					$ip_ban = $ip_banned;
					$nb_ban++;
				}
				else
				{
					$ip_ban = "";
				}
			}
		}
	}
	return $ip_ban;
}

function get_blok($side)
{
	global $user, $nuked;

	if ($side == "gauche")
	{
		$active = 1;
	}
	else if ($side == "droite")
	{
		$active = 2;
	}
	else if ($side == "centre")
	{
		$active = 3;
	}
	else if ($side == "bas")
	{
		$active = 4;
	}

	$aff_good_bl = "block_" . $side;

	$sql = mysql_query("SELECT * FROM " . BLOCK_TABLE . " WHERE active = '" . $active . "' ORDER BY position");
	while ($blok = mysql_fetch_array($sql))
	{
		$blok['titre'] = htmlentities($blok['titre']);

		$test_page = "";
		$bl_nivo = $blok['nivo'];
		$blok['page'] = explode("|", $blok['page']);

		foreach ($blok['page'] as $mod)
		{
			if (isset($_REQUEST['file']) && $_REQUEST['file'] == $mod || $mod == "Tous") $test_page = "ok";
		}

		if ($user) $visiteur = $user[1];
		else $visiteur = 0;

		if ($visiteur >= $bl_nivo && $test_page == "ok")
		{
			include_once("Includes/blocks/block_" . $blok['type'] . ".php");
			$function = "affich_block_" . $blok['type'];
			$blok = $function($blok);

			if ($blok['content'] != "") $aff_good_bl($blok);
		}
	}
}


function checkimg($url)
{
	$url = rtrim( $url );
	$ext = strrchr( $url, '.' );
	$ext = substr( $ext, 1 );

	if ( !preg_match( '#\.(([a-z]?)htm|php)#i', $url ) && substr( $url, -1 ) != '/' && preg_match( '#jpg|jpeg|gif|png|bmp#i', $ext ) )
	{
		return $url;
	}
	else
	{
		return 'images/noimagefile.gif';
	}
}

function icon($texte)
{
	global $nuked;

	$texte = str_replace("mailto:", "mailto!", $texte);
	$texte = str_replace("http://", "_http_", $texte);
	$texte = str_replace("&quot;", "_QUOT_", $texte);
	$texte = str_replace("&#039;", "_SQUOT_", $texte);
	$texte = str_replace("&agrave;", "à", $texte);
	$texte = str_replace("&acirc;", "â", $texte);
	$texte = str_replace("&eacute;", "é", $texte);
	$texte = str_replace("&egrave;", "è", $texte);
	$texte = str_replace("&ecirc;", "ê", $texte);
	$texte = str_replace("&ucirc;", "û", $texte);

	$sql = mysql_query("SELECT code, url, name FROM " . SMILIES_TABLE . " ORDER BY id");
	while (list($code, $url, $name) = mysql_fetch_array($sql))
	{
		$texte = str_replace($code, "<img src=\"images/icones/" . $url . "\" alt=\"\" title=\"$name\" />", $texte);
	}

	$texte = str_replace("mailto!", "mailto:", $texte);
	$texte = str_replace("_http_", "http://", $texte);
	$texte = str_replace("_QUOT_", "&quot;", $texte);
	$texte = str_replace("_SQUOT_", "&#039;", $texte);
	$texte = str_replace("à", "&agrave;", $texte);
	$texte = str_replace("â", "&acirc;", $texte);
	$texte = str_replace("é", "&eacute;", $texte);
	$texte = str_replace("è", "&egrave;", $texte);
	$texte = str_replace("ê", "&ecirc;", $texte);
	$texte = str_replace("û", "&ucirc;", $texte);

	return($texte);
}

function smiley($textarea)
{
	global $nuked;

	$sql = mysql_query("SELECT code, url, name FROM " . SMILIES_TABLE . " ORDER BY id LIMIT 0, 15");
	while (list($code, $url, $name) = mysql_fetch_array($sql))
	{
		$name = htmlentities($name);

		echo "&nbsp;<a href=\"javascript:insertAtCaret('" . $textarea ."', '$code')\"><img style=\"border: 0;\" src=\"images/icones/" . $url . "\" alt=\"\" title=\"" . $name . "\" /></a>";
	}

	echo "<br />[ <a href=\"#\" onclick=\"javascript:window.open('index.php?file=Textbox&amp;nuked_nude=index&amp;op=smilies&amp;textarea=" . $textarea . "','smilies','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=200,height=350,top=100,left=470');return(false)\">" . _MORESMILIES . "</a> ]\n";
}

function secu_url($url){
	$info = parse_url(strtolower($url));
	if ($info !== false) {
		return strrchr($info['path'], '.') != '.php'
			&& (!isset($info['query']) || $info['query'] == '');
	} else {
		return false;
	}
}

function secu_css($Style){
	$AllowedProprieties = array(
		'display',
		'margin-left',
		'margin-right',
		'float',
		'padding',
		'text-decoration',
		'text-align',
		'color',
		'align',
		'vertical-align',
		'margin',
		'border',
		'background-color',
		'width',
		'height',
		'border-color',
		'background-image',
		'border-width',
		'padding-left',
		'padding-right',
		'font-size',
		'font-family'
	);
	$Style = explode(';', $Style);
	$Style = array_map('trim', $Style);

	foreach ($Style as $id=>$Element){
		preg_match('/ *([^ :]+) *: *(( |.)*)/', $Element, $Phased);
		if (!in_array($Phased[1], $AllowedProprieties)) {
			unset($Style[$id]);
		} elseif (preg_match('/url *\\( *\'?"? *([^ \'"]+) *"?\'?\\)/', $Element, $Phased) > 0) {
			if (!secu_url($Phased[1])) {
				unset($Style[$id]);
			}
		}
	}
	return implode(';', $Style);
}

function secu_args($matches){
	$allowedTags = array(
		'a' => array(
			'href',
			'name',
			'target',
			'title'
		),
		'p' => array(
			'style'
		),
		'span' => array(
			'style'
		),
		'div' => array(
		),
		'br' => array(
		),
		'b' => array(
		),
		'u' => array(
		),
		'i' => array(
		),
		'ul' => array(
		),
		'ol' => array(
		),
		'sub' => array(
		),
		'sup' => array(
		),
		'li' => array(
		),
		'blockquote' => array(
		),
		'hr' => array(
		),
		'em' => array(
		),
		'strong' => array(
		),
		'table' => array(
			'dir',
			'lang',
			'border',
			'cellspacing',
			'cellpadding',
			'frame',
			'rules',
			'align',
			'summary',
			'width',
			'style',
			'valign'
		),
		'tbody' => array(
			'dir',
			'lang',
			'border',
			'cellspacing',
			'cellpadding',
			'frame',
			'rules',
			'align',
			'summary',
			'width',
			'style',
			'valign'
		),
		'tr' => array(
			'dir',
			'lang',
			'border',
			'cellspacing',
			'cellpadding',
			'frame',
			'rules',
			'align',
			'summary',
			'width',
			'style',
			'valign'
		),
		'td' => array(
			'dir',
			'lang',
			'border',
			'cellspacing',
			'cellpadding',
			'frame',
			'rules',
			'align',
			'summary',
			'width',
			'style',
			'valign'
		),
		'th' => array(
			'dir',
			'lang',
			'border',
			'cellspacing',
			'cellpadding',
			'frame',
			'rules',
			'align',
			'summary',
			'width',
			'style',
			'valign'
		),
		'caption' => array(
		),
		'img' => array(
			'src',
			'style',
			'title',
			'dir',
			'lang',
			'alt',
			'width',
			'height'
		),
	);
	if (in_array(strtolower($matches[1]), array_keys($allowedTags))) {
		preg_match_all('/([^ =]+)=(&quot;((.(?<!&quot;))*)|[^ ]+)/', $matches[2], $args);

		//Supprime les attributs interdit
		foreach ($args[1] as $id=>$attribute){
			if (!in_array($attribute, $allowedTags[$matches[1]]))
				foreach ($args as $part=>$g)
					unset($args[$part][$id]);
		}

		//Met en forme les attributs restants
		foreach ($args[2] as $id=>$val){
			$args[1][$id] = trim(strtolower($args[1][$id]));
			$val = trim($val);
			if (preg_match('/^&quot;/', $val, $g))
				$val .= ';';
			$args[2][$id] = trim(html_entity_decode($val), " \t\n\r\0\"");
			if ($args[1][$id] == 'style') {
				$args[2][$id] = secu_css($args[2][$id]);
			} elseif ($args[1][$id] == 'src') {
				if(!secu_url($args[2][$id]))
					$args[2][$id] = 'images/_blank.jpg';
			}
		}

		$RetStr = '<' . $matches[1];
		foreach ($args[1] as $id=>$attribute){
			$RetStr .= ' ' . $attribute . '="' . $args[2][$id] . '"';
		}
		if ($matches[3] == '/') {
			$RetStr .= ' />';
		} else {
			$RetStr .= '>';
		}
		return $RetStr;

		//Balises de fermeture
	} else if (substr($matches[1], 0, 1) == '/'
	&& in_array(strtolower(substr($matches[1], 1)), array_keys($allowedTags))) {
		return '<' . $matches[1] . '>';

		//Balses interdites
	} else {
		return $matches[0];
	}
}

function secu_html($texte)
{
	global $bgcolor3, $nuked;
	/* balise html interdite*/
	$texte = str_replace(array('&lt;', '&gt;', '&quot;'), array('<', '>', '"'), $texte);
	$texte = stripslashes($texte);
	$texte = htmlspecialchars($texte);
	$texte = str_replace('&amp;', '&', $texte);
	
	/*balise autorisée*/
	$texte = preg_replace_callback('/&lt;([^ &]+)[[:blank:]]?((.(?<!&gt;))*)&gt;/', 'secu_args', $texte);

	preg_match_all('`<(/?)([^/ >]+)(| [^>]*([^/]))>`', $texte, $Tags, PREG_SET_ORDER);
	//preg_match_all('`</([^/ >]+)(| [^>/]*)>`', $texte, $CloseTag, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

	$TagList = array();
	$bad = false;
	foreach ($Tags as $Match){
		$TagName = $Match[3] == ''?$Match[2].$Match[4]:$Match[2];
		if ($Match[1] == '/') {
			$bad = $bad | array_pop($TagList) != $TagName;
		} else {
			array_push($TagList, $TagName);
		}
	}

    if ($_REQUEST['mess_id'])
    {
        $f_sql = mysql_query("SELECT auteur FROM " . $nuked['prefix'] . "_forums_messages WHERE id = '" . $_REQUEST['mess_id'] . "' AND forum_id = '" . $_REQUEST['forum_id'] . "'") or die (mysql_error());
        list($f_author) = mysql_fetch_array($f_sql);
        $f_quote = _QUOTE . ' ' . _BY . ' ' . $f_author;
    }
    else $f_quote = _QUOTE;
            
    $bad = $bad | count($TagList) > 0;
     $texte = str_replace("<blockquote>", "<br /><table style=\"background: " . $bgcolor3 . ";\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\" border=\"0\"><tr><td style=\"background: #FFFFFF;color: #000000\"><div id=\"quote\" style=\"border: 0; overflow: auto;\"><b>" . $f_quote . " :</b><br />", $texte);
     $texte = str_replace("</blockquote>", "</div></td></tr></table><br />", $texte);

	if ($bad) {
		return('Le code HTML est mal formaté');
	} else{
		return $texte;
	}

}

function redirect($url, $tps)
{
	$temps = $tps * 1000;

	echo "<script type=\"text/javascript\">\n"
	. "<!--\n"
	. "\n"
	. "function redirect() {\n"
	. "window.location='" . $url . "'\n"
	. "}\n"
	. "setTimeout('redirect()','" . $temps ."');\n"
	. "\n"
	. "// -->\n"
	. "</script>\n";
}

function number($count, $each, $link) {

	$current = $_REQUEST['p'];

	if ($each > 0) {

		if ($count <= 0)     $count   = 1;
		if (empty($current)) $current = 1; // On renormalise la page courante...

		// Calcul du nombre de pages
		$n = ceil($count / intval($each)); // on arrondit à  l'entier sup.

		// Début de la chaine d'affichage
		$output = "<b>" . _PAGE . " :</b> ";

		for ($i = 1; $i <= $n; $i++) {

			if ($i == $current) {

				$output .= sprintf
				(
					'<b>[%d]</b> ',
					$i
				);

			}
			// On est autour de la page actuelle : on affiche
			elseif (abs($i - $current) <= 4) {

				$output .= sprintf
				(
					"<a href=\"" . $link . "&amp;p=%d\">%d</a> ",
					$i, $i
				);

			}
			// On affiche quelque chose avant d'omettre les pages inutiles
			else {

				// On est avant la page courante
				if (!isset($first_done) && $i < $current) {

					$output .= sprintf
					(
						'...
						<a href="' . $link . '&amp;p=%d" title="' . _PREVIOUSPAGE . '">&laquo;</a> ',
						$current-1
					);
					$first_done = true;

				}
				// Après la page courante
				elseif (!isset($last_done) && $i > $current) {

					$output .= sprintf
					(
						'<a href="' . $link . '&amp;p=%d" title="' . _NEXTPAGE . '">&raquo;</a>
						... ',
						$current+1
					);
					$last_done = true;

				}
				// On a dépassé les cas qui nous intéressent : inutile de continuer
				elseif ($i > $current)
					break;

			}

		}

		$output .= '<br />';

		echo $output;
	}

}

function nbvisiteur()
{
	global $user, $nuked, $user_ip;

	$limite = time() + $nuked['nbc_timeout'];
	$time = time();

	$req = mysql_query("DELETE FROM " . NBCONNECTE_TABLE . " WHERE date < '" . $time."'");

	if ($user_ip != "")
	{
		if ($user[0] != "")
		{
			$where = "WHERE user_id='" . $user[0] . "'";
		}
		else
		{
			$where = "WHERE IP='" . $user_ip . "'";
		}
		$req = mysql_query("SELECT IP FROM " . NBCONNECTE_TABLE . " " . $where);
		$query = mysql_num_rows($req);

		if ($query > 0)
		{
			if ($user[0] != "")
			{
				$req = mysql_query("UPDATE " . NBCONNECTE_TABLE . " SET date = '" . $limite . "', type = '" . $user[1] . "', IP = '" . $user_ip . "', username = '" . $user[2] . "' WHERE user_id = '" . $user[0] . "'");
			}
			else
			{
				$req = mysql_query("UPDATE " . NBCONNECTE_TABLE . " SET date = '" . $limite . "', type = '" . $user[1] . "', user_id = '" . $user[0] . "', username = '" . $user[2] . "' WHERE IP = '" . $user_ip . "'");
			}
		}
		else
		{
			$del = mysql_query("DELETE FROM " . NBCONNECTE_TABLE . " WHERE IP = '" . $user_ip . "'");
			$req = mysql_query("INSERT INTO " . NBCONNECTE_TABLE . " ( `IP` , `type` , `date` , `user_id` , `username` ) VALUES ( '" . $user_ip . "' , '" . $user[1] . "' , '" . $limite . "' , '" . $user[0] . "' , '" . $user[2] . "' )");
		}
	}

	$res = mysql_query("SELECT type FROM " . NBCONNECTE_TABLE . " WHERE type = 0");
	$count[0] = mysql_num_rows($res);
	$res = mysql_query("SELECT type FROM " . NBCONNECTE_TABLE . " WHERE type BETWEEN 1 AND 2");
	$count[1] = mysql_num_rows($res);
	$res = mysql_query("SELECT type FROM " . NBCONNECTE_TABLE . " WHERE type > 2");
	$count[2] = mysql_num_rows($res);
	$count[3] = $count[1] + $count[2];
	$count[4] = $count[0] + $count[3];
	return $count;
}

function nivo_mod($mod)
{

	$sql = mysql_query("SELECT niveau FROM " . MODULES_TABLE . " WHERE nom = '" . $mod . "'");
	if (mysql_num_rows($sql) == 0) {
		return false;
	}
	else
	{
		list($niveau) = mysql_fetch_array($sql);
		return $niveau;
	}
}

function admin_mod($mod)
{
	$sql = mysql_query("SELECT admin FROM " . MODULES_TABLE . " WHERE nom = '" . $mod . "'");
	list($admin) = mysql_fetch_array($sql);
	return $admin;
}

function translate($file_lang)
{
	global $nuked;

	ob_start();
	print eval(" include ('$file_lang'); ");
	$lang_define = ob_get_contents();
	$lang_define = htmlentities($lang_define, ENT_NOQUOTES);
	$lang_define = str_replace("&lt;", "<", $lang_define);
	$lang_define = str_replace("&gt;", ">", $lang_define);
	ob_end_clean();
	return $lang_define;
}

function compteur($file)
{
	$upd = mysql_query("UPDATE " . STATS_TABLE . " SET count = count + 1 WHERE type = 'pages' AND nom = '" . $_GET['file'] . "'");
}
function nk_CSS($str)
{
	if ($str != "")
	{
		$str = str_replace("content-disposition:","&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#100;&#105;&#115;&#112;&#111;&#115;&#105;&#116;&#105;&#111;&#110;&#58;",$str);
		$str = str_replace("content-type:","&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#116;&#121;&#112;&#101;&#58;",$str);
		$str = str_replace("content-transfer-encoding:","&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#116;&#114;&#97;&#110;&#115;&#102;&#101;&#114;&#45;&#101;&#110;&#99;&#111;&#100;&#105;&#110;&#103;&#58;",$str);
		$str = str_replace("include","&#105;&#110;&#99;&#108;&#117;&#100;&#101;",$str);
		$str = str_replace("\<\?","&lt;?",$str);
		$str = str_replace("<\?php","&lt;?php",$str);
		$str = str_replace("\?\>","?&gt;",$str);
		$str = str_replace("script","&#115;&#99;&#114;&#105;&#112;&#116;",$str);
		$str = str_replace("eval","&#101;&#118;&#97;&#108;",$str);
		$str = str_replace("javascript","&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;",$str);
		$str = str_replace("embed","&#101;&#109;&#98;&#101;&#100;",$str);
		$str = str_replace("iframe","&#105;&#102;&#114;&#97;&#109;&#101;",$str);
		$str = str_replace("refresh", "&#114;&#101;&#102;&#114;&#101;&#115;&#104;", $str);
		$str = str_replace("onload", "&#111;&#110;&#108;&#111;&#97;&#100;", $str);
		$str = str_replace("onstart", "&#111;&#110;&#115;&#116;&#97;&#114;&#116;", $str);
		$str = str_replace("onerror", "&#111;&#110;&#101;&#114;&#114;&#111;&#114;", $str);
		$str = str_replace("onabort", "&#111;&#110;&#97;&#98;&#111;&#114;&#116;", $str);
		$str = str_replace("onblur", "&#111;&#110;&#98;&#108;&#117;&#114;", $str);
		$str = str_replace("onchange", "&#111;&#110;&#99;&#104;&#97;&#110;&#103;&#101;", $str);
		$str = str_replace("onclick", "&#111;&#110;&#99;&#108;&#105;&#99;&#107;", $str);
		$str = str_replace("ondblclick", "&#111;&#110;&#100;&#98;&#108;&#99;&#108;&#105;&#99;&#107;", $str);
		$str = str_replace("onfocus", "&#111;&#110;&#102;&#111;&#99;&#117;&#115;", $str);
		$str = str_replace("onkeydown", "&#111;&#110;&#107;&#101;&#121;&#100;&#111;&#119;&#110;", $str);
		$str = str_replace("onkeypress", "&#111;&#110;&#107;&#101;&#121;&#112;&#114;&#101;&#115;&#115;", $str);
		$str = str_replace("onkeyup", "&#111;&#110;&#107;&#101;&#121;&#117;&#112;", $str);
		$str = str_replace("onmousedown", "&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#100;&#111;&#119;&#110;", $str);
		$str = str_replace("onmousemove", "&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#109;&#111;&#118;&#101;", $str);
		$str = str_replace("onmouseover", "&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#111;&#118;&#101;&#114;", $str);
		$str = str_replace("onmouseout", "&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#111;&#117;&#116;", $str);
		$str = str_replace("onmouseup", "&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#117;&#112;", $str);
		$str = str_replace("onreset", "&#111;&#110;&#114;&#101;&#115;&#101;&#116;", $str);
		$str = str_replace("onselect", "&#111;&#110;&#115;&#101;&#108;&#101;&#99;&#116;", $str);
		$str = str_replace("onsubmit", "&#111;&#110;&#115;&#117;&#98;&#109;&#105;&#116;", $str);
		$str = str_replace("onunload", "&#111;&#110;&#117;&#110;&#108;&#111;&#97;&#100;", $str);
		$str = str_replace("document", "&#100;&#111;&#99;&#117;&#109;&#101;&#110;&#116;", $str);
		$str = str_replace("cookie", "&#99;&#111;&#111;&#107;&#105;&#101;", $str);
		$str = str_replace("vbscript", "&#118;&#98;&#115;&#99;&#114;&#105;&#112;&#116;", $str);
		$str = str_replace("location", "&#108;&#111;&#99;&#97;&#116;&#105;&#111;&#110;", $str);
		$str = str_replace("object", "&#111;&#98;&#106;&#101;&#99;&#116;", $str);
		$str = str_replace("vbs", "&#118;&#98;&#115;", $str);
		$str = str_replace("href", "&#104;&#114;&#101;&#102;", $str);
		$str = str_replace("src", "&#115;&#114;&#99;", $str);
		$str = str_replace("expression", "&#101;&#120;&#112;&#114;&#101;&#115;&#115;&#105;&#111;&#110;", $str);
		$str = str_replace("alert", "&#97;&#108;&#101;&#114;&#116;", $str);
	}
	return($str);
}
function visits()
{
	global $nuked, $user_ip, $user;

	$time = time();
	$timevisit = $nuked['visit_delay'] * 60;
	$limite = $time + $timevisit;

	if ($user)
	{
		$sql = mysql_query("SELECT id, date FROM " . STATS_VISITOR_TABLE . " WHERE user_id = '" . $user[0] . "' ORDER by date DESC LIMIT 0, 1");
	}
	else
	{
		$sql = mysql_query("SELECT id, date FROM " . STATS_VISITOR_TABLE . " WHERE ip = '" . $user_ip . "' ORDER by date DESC LIMIT 0, 1");
	}

	list($id, $date) = mysql_fetch_array($sql);

	if ($id != "" && $date > $time)
	{
		$upd = mysql_query("UPDATE " . STATS_VISITOR_TABLE . " SET  date = '" . $limite . "' WHERE id = '" . $id . "'");
	}
	else
	{
		$month = strftime("%m", $time);
		$year = strftime("%Y", $time);
		$day = strftime("%d", $time);
		$hour = strftime("%H", $time);
		$user_referer = mysql_escape_string($_SERVER['HTTP_REFERER']);
		$user_host = strtolower(@gethostbyaddr($user_ip));
		$user_agent = mysql_escape_string($_SERVER['HTTP_USER_AGENT']);

		if ($user_host == $user_ip)
		{
			$host = "";
		}
		else
		{
			if (preg_match('`([^.]{1,})((\.(co|com|net|org|edu|gov|mil))|())((\.(ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|fi|fj|fk|fm|fo|fr|fx|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zr|zw))|())$`', $user_host, $res))
				$host = $res[0];
		}

		$browser = getBrowser();

		$os = getOS();

		$sql2 = mysql_query("INSERT INTO " . STATS_VISITOR_TABLE . " ( `id` , `user_id` , `ip` , `host` , `browser` , `os` , `referer` , `day` , `month` , `year` , `hour` , `date` ) VALUES ( '' , '" . $user[0] . "' , '" . $user_ip . "' , '" . $host . "' , '" . $browser . "' , '" . $os . "' , '" . $user_referer . "' , '" . $day . "' , '" . $month . "' , '" . $year . "' , '" . $hour . "' , '" . $limite . "' )");
	}
}

function verif_pseudo($string = '')
{
	global $nuked;

	$string = trim($string);

	if (!$string || ($string == '') || (preg_match("`[\$\^\(\)'\"?%#<>,;:]`", $string)))
	{
		$string = 'error1';
	}
	if ($string != 'error1')
	{
		$sql = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE pseudo = '" . $string . "'");
		$is_reg = mysql_num_rows($sql);
		if ($is_reg > 0)
		{
			$string = 'error2';
		}
	}
	if ($string != 'error1' && $string != 'error2')
	{
		$sql2 = mysql_query("SELECT pseudo FROM " . BANNED_TABLE . " WHERE pseudo = '" . $string . "'");
		$is_reg2 = mysql_num_rows($sql2);
		if ($is_reg2 > 0)
		{
			$string = 'error3';
		}
	}
	return($string);
}

function trunc_hyperlink($texte)
{
	$texte = preg_replace("/([a-zA-Z]+:\/\/[a-z0-9\_\.\-]+".
	"[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%\=\,\.\;\#\_]+)/i",
	"[<a href=\"$1\" onclick=\"window.open(this.href); return false;\">" . _TLINK . "</a>]", $texte, -1);
	$texte = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1[<a href="http://\2.\3" onclick="window.open(this.href); return false;" title="http://\2.\3">' . _TLINK . '</a>]', $texte);
	$texte = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1[<a href=\"mailto:\\2@\\3\" title=\"\\2@\\3\">" . _TMAIL . "</a>]", $texte);

	return($texte);
}

function UpdateSitmap(){
	global $nuked;
	$Disable = array('Suggest', 'Comment', 'Vote', 'Textbox', 'Members');

	$fp = fopen(dirname(__FILE__).'/sitemap.xml', 'wb');
	if ($fp !== false) {
		$Sitemap = "<?xml version='1.0' encoding='UTF-8'?>\r\n";
		$Sitemap .= "<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\r\n";
		$Sitemap .= "xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"\r\n";
		$Sitemap .= "xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n";

		$sql = "SELECT nom FROM " . MODULES_TABLE . " WHERE niveau = 0";
		$mods = mysql_query($sql);

		while(list($mod) = mysql_fetch_row($mods)){
			if (!in_array($mod, $Disable)) {
				$Sitemap .= "\t<url>\r\n";
				$Sitemap .= "\t\t<loc>$nuked[url]/index.php?file=$mod</loc>\r\n";
				switch($mod){
					case 'News':
						$Last = mysql_result(mysql_query('SELECT date FROM ' . NEWS_TABLE . 'ORDER BY date DESC LIMIT 1'), 0);
						$Last = date('Y-m-d');
						$Sitemap .= "\t\t<priority>0.8</priority>\r\n";
						$Sitemap .= "\t\t<lastmod>$Last</lastmod>\r\n";
						$Sitemap .= "\t\t<changefreq>daily</changefreq>\r\n";
						break;
					case 'Forum':
						$Sitemap .= "\t\t<priority>0.4</priority>\r\n";
						$Sitemap .= "\t\t<lastmod>$Last</lastmod>\r\n";
						$Sitemap .= "\t\t<changefreq>always</changefreq>\r\n";
						break;
					case 'Download':
						$Last = mysql_result(mysql_query('SELECT date FROM ' . DOWNLOAD_TABLE . 'ORDER BY date DESC LIMIT 1'), 0);
						$Last = date('Y-m-d');
						$Sitemap .= "\t\t<priority>0.5</priority>\r\n";
						$Sitemap .= "\t\t<lastmod>$Last</lastmod>\r\n";
						$Sitemap .= "\t\t<changefreq>weekly</changefreq>\r\n";
						break;

					default:
						$Sitemap .= "\t\t<priority>0.5</priority>\r\n";
				} // switch
				$Sitemap .= "\t</url>\r\n";
			}
		}

		$Sitemap .= "</urlset>\r\n";
		fwrite($fp, chr(0xEF) . chr(0xBB)  . chr(0xBF) . utf8_encode($Sitemap)); //Ajout de la marque d'Octet
		fclose($fp);
	}
}

function getOS() {

	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$os = 'Autre';

	$list_os = array(
		// Windows
		'Windows NT 6.1'       => 'Windows 7',
		'Windows NT 6.0'       => 'Windows Vista',
		'Windows NT 5.2'       => 'Windows Server 2003',
		'Windows NT 5.1'       => 'Windows XP',
		'Windows NT 5.0'       => 'Windows 2000',
		'Windows 2000'         => 'Windows 2000',
		'Windows CE'           => 'Windows Mobile',
		'Win 9x 4.90'          => 'Windows Me.',
		'Windows 98'           => 'Windows 98',
		'Windows 95'           => 'Windows 95',
		'Win95'                => 'Windows 95',
		'Windows NT'           => 'Windows NT',

		// Linux
		'Ubuntu'               => 'Linux Ubuntu',
		'Fedora'               => 'Linux Fedora',
		'Linux'                => 'Linux',

		// Mac
		'Macintosh'            => 'Mac',
		'Mac OS X'             => 'Mac OS X',
		'Mac_PowerPC'          => 'Mac OS X',

		 // Autres
		'FreeBSD'              => 'FreeBSD',
		'Unix'                 => 'Unix',
		'Playstation portable' => 'PSP',
		'OpenSolaris'          => 'SunOS',
		'SunOS'                => 'SunOS',
		'Nintendo Wii'         => 'Nintendo Wii',
		'Mac'                  => 'Mac',
		);

	$user_agent = strtolower( $user_agent );

	foreach( $list_os as $k => $v ) {

     if (preg_match("#".strtolower($k)."#", strtolower($user_agent)))
		{
			$os = $v;
			break;
		}
	}
	return $os;
}

function getBrowser() {

	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$browser = 'Autre';

	$list_browser = array(
		'Firefox'   => 'Firefox',
		'Lynx'      => 'Lynx',
		'Konqueror' => 'Konqueror',
		'Netscape'  => 'Netscape',
		'Opera'     => 'Opera',
		'MSIE'      => 'Internet Explorer',
		'Chrome'    => 'Google Chrome',
		'Safari'    => 'Apple Safari',
		'Mozilla'   => 'Mozilla'
	);

	foreach( $list_browser as $k => $v )
	{
		 if (preg_match("#".$k."#i", $user_agent))
		{
			$browser = $v;
			break;
		}
	}
	return $browser;

}
function erreursql($errno, $errstr, $errfile, $errline, $errcontext)
{
	global $user, $nuked, $language;


    switch ($errno) {
		case E_WARNING:
			break;
		case 8192:
			break;
		case 8:
			break;
		default:
			$content = ob_get_clean();
			@include ("conf.inc.php");
			connect();
			@session_name('nuked');
			@session_start();
			if (session_id() == '') {
				exit('Erreur dans la création de la session anonyme');
			}
			$date = time();
			$content = "<b>Veuilliez nous excuser une erreur a été détecté, nous la corrigerons le plus rapidement possible, merci. <br /><br /><b>Information:</b><br /><br /><b>Mon ERREUR</b> [$errno] $errstr<br /> Erreur fatale sur la ligne $errline dans le fichier $errfile, PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />Arrêt...<br />";

			echo $content;

			$texte = "Type: ".$errno." Fichier: ".$errfile." Ligne: ".$errline."";
			$upd = mysql_query("INSERT INTO " . $db_prefix . "_erreursql  (`date` , `lien` , `texte`)  VALUES ('".$date."', '".mysql_escape_string($_SERVER["REQUEST_URI"])."', '".$texte."')");
			if($language == "french")
			{
				$upd2 = mysql_query("INSERT INTO " . $db_prefix . "_notification  (`date` , `type` , `texte`)  VALUES ('".$date."', '4', 'Une erreur sql a été détecté: [<a href=\"index.php?file=Admin&page=erreursql\">Lien</a>].')");
			}
			else
			{
				$upd2 = mysql_query("INSERT INTO " . $db_prefix . "_notification  (`date` , `type` , `texte`)  VALUES ('".$date."', '4', 'A sql error have been detected: [<a href=\"index.php?file=Admin&page=erreursql\">Link</a>].')");
			}
			exit();
			break;
    }

    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}
?>
