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

global $nuked, $theme, $language, $bgcolor1, $bgcolor2, $bgcolor3, $user, $cookie_captcha;
translate("modules/Textbox/lang/" . $language . ".lang.php");
include("modules/Textbox/config.php");

// Inclusion système Captcha
include_once("Includes/nkCaptcha.php");

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == "off") $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;

if ($user)
{
    $visiteur = $user[1];
}
else
{
    $visiteur = 0;
}
$sql2 = mysql_query("SELECT bid, active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($id, $active) = mysql_fetch_array($sql2);

?>
<script type="text/javascript">
<!--
function maj_shoutbox() {

	if(document.getElementById("textbox").style.paddingTop != "0px")
	{
	document.getElementById("textbox").style.textAlign = "center";
	document.getElementById("textbox").innerHTML = "<img src=\"images/loading.gif\" alt=\"Loading\" /><br /><?php echo _LOADINPLSWAIT; ?>";
	document.getElementById("textbox").style.paddingTop = "150px";
	}

	var fichier = 'index.php?file=Textbox&op=ajax&nuked_nude=index';
  var requete;

	if (window.XMLHttpRequest) requete = new XMLHttpRequest();
	else if (window.ActiveXObject) requete = new ActiveXObject("Microsoft.XMLHTTP");
	else alert('<?php echo _LOADINGERRORS; ?>');
	requete.open('get',fichier,true);
	requete.onreadystatechange = function()  {
		if(requete.readyState == 4 && requete.status==200 && requete.responseText != "")
		{
			document.getElementById("textbox").style.textAlign = "left";
			document.getElementById("textbox").innerHTML = requete.responseText;
			document.getElementById("textbox").style.paddingTop = "0px";
			setTimeout('suivant()','25000');
		}
	}

requete.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=iso-8859-1');
requete.send(null);
}
function suivant()
{
document.getElementById("affichetextbox").innerHTML = "";
maj_shoutbox();
}
function trim(string)
{
return string.replace(/(^\s*)|(\s*$)/g,'');
}
function maFonctionAjax(auteur,texte,code)
{
	if (trim(document.getElementById('textbox_auteur').value) == "")
	{
	alert('<?php echo _NONICKNAME; ?>');
	return false;
	}
	if (document.getElementById('textbox_auteur').value == '<?php echo _NICKNAME; ?>')
	{
	alert('<?php echo _NONICKNAME; ?>');
	return false;
	}
	if (trim(document.getElementById('textbox_texte').value) == "")
	{
	alert('<?php echo _NOTEXT; ?>');
	return false;
	}
	if (document.getElementById('textbox_texte').value == '<?php echo _YOURMESS; ?>')
	{
	alert('<?php echo _NOTEXT; ?>');
	return false;
	}
 var OAjax;
 if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
 else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
 OAjax.open('POST',"index.php?file=Textbox&page=submit",true);
 document.getElementById("affichetextbox").innerHTML = "<div style=\"text-align:center;\"><b><?php echo _PLEASEWAITTXTBOX; ?></b></div>";
  OAjax.onreadystatechange = function()
  {
	if (OAjax.readyState == 4 && OAjax.status==200)
	{
		if (document.getElementById)
		{
			var message = OAjax.responseText.substr(OAjax.responseText.search(/\<div id\=\"ajax_message\"[^>]*\>/));
			message = message.substr(0, message.search(/<\/div>/) + 6);
			document.getElementById("affichetextbox").innerHTML = "<b>" + message + "</b>";
			document.getElementById("textbox_texte").value = "<?php echo _YOURMESS; ?>";
			maj_shoutbox();
		}
	}
  }
  	texte = texte.replace("+","%2B");
	OAjax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded; charset=iso-8859-1');
	OAjax.send('auteur='+auteur+'&texte='+texte+'&code_confirm='+code+'');
	return true;
}
-->
</script>

<?php
if ($visiteur >= 2)
            {
                echo "<script type=\"text/javascript\">\n"
		. "<!--\n"
		. "\n"
		. "function del_shout(pseudo, id)\n"
		. "{\n"
		. "if (confirm('" . _DELETETEXT . " '+pseudo+' ! " . _CONFIRM . "'))\n"
		. "{document.location.href = 'index.php?file=Textbox&page=admin&op=del_shout&mid='+id;}\n"
		. "}\n"
		. "\n"
		. "// -->\n"
		. "</script>\n";
		}
if ($active == 3 || $active == 4)
{
    $width = $mbox_width;
    $height = $mbox_height;
    $max_chars = $max_mstring;
    $mess_max = $max_mtexte;
    $pseudo_max = $max_mpseudo;

}
else
{
    $width = $box_width;
    $height = $box_height;
    $max_chars = $max_string;
    $mess_max = $max_texte;
    $pseudo_max = $max_pseudo;
}

echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"98%\" cellspacing=\"1\" cellpadding=\"2\"><tr><td>\n"
. "<div id=\"textbox\" style=\"width: " . $width . "; height: " . $height . "; overflow: auto;\">\n"
. "<p>\n"
. "<img src=\"images/loading.gif\" alt=\"Loading\" /><br />\n"
. _LOADINPLSWAIT . "\n"
. "</p></div></td></tr></table>\n"
. "<script type=\"text/javascript\">maj_shoutbox();</script>\n";
echo "<div id=\"affichetextbox\"></div><div>\n";
if ($active == 3 || $active == 4)
{
    if ($visiteur >= nivo_mod("Textbox"))
    {
        echo "<form method=\"post\" onsubmit=\"maFonctionAjax(this.textbox_auteur.value,this.textbox_texte.value, this.code.value); return false;\" action=\"\" ><div style=\"text-align: center;\">\n";

        if (!$user)
        {
            echo "<input id=\"textbox_auteur\" type=\"text\" name=\"auteur\" size=\"40\" maxlength=\"100\" value=\"" . _NICKNAME . "\" onclick=\"if(this.value=='" . _NICKNAME . "'){this.value=''}\" /><br />\n";
        }
        else
        {
            echo "<input id=\"textbox_auteur\" type=\"hidden\" name=\"auteur\" value=\"" . $user[2] . "\" />\n";
        }

        echo "<input id=\"textbox_texte\" type=\"text\" name=\"texte\" size=\"50\" value=\"" . _YOURMESS . "\"  onclick=\"if(this.value=='" . _YOURMESS . "'){this.value=''}\" /><br />\n";

		if ($captcha == 1) create_captcha(3);
		else echo "<input id=\"code\" type=\"hidden\" value=\"0\" />\n";

	echo "<br /><input type=\"submit\" value=\"" . _SEND . "\" />&nbsp;<br /><br />\n"
	. "<a href=\"#\" onclick=\"javascript:window.open('index.php?file=Textbox&amp;nuked_nude=index&amp;op=smilies&amp;textarea=textbox_texte','smilies','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=200,height=350,top=100,left=470');return(false)\">\n"
	. "<b>" . _SMILEY . "</b></a></div><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Textbox\">" . _SEEARCHIVES . "</a> ]</div></form><br />\n";
    }
}
else
{
    if ($visiteur >= nivo_mod("Textbox"))
    {
        echo"<form method=\"post\" onsubmit=\"maFonctionAjax(this.textbox_auteur.value,this.textbox_texte.value, this.code.value); return false;\" action=\"\" ><div style=\"text-align: center;\">\n";

        if (!$user)
        {
            echo "<input id=\"textbox_auteur\" type=\"text\" name=\"auteur\" maxlength=\"100\" value=\"" . _NICKNAME . "\" style=\"width:70%;\" onclick=\"if(this.value=='" . _NICKNAME . "'){this.value=''}\" /><br />\n";
        }
        else
        {
            echo "<input id=\"textbox_auteur\" type=\"hidden\" name=\"auteur\" value=\"" . $user[2] . "\" />\n";
        }

        echo "<input id=\"textbox_texte\" type=\"text\" name=\"texte\" value=\"" . _YOURMESS . "\"  style=\"width:90%;\" onclick=\"if(this.value=='" . _YOURMESS . "'){this.value=''}\" /><br /><table>\n";

	if ($captcha == 1) create_captcha(2);
	else echo "<input id=\"code\" type=\"hidden\" value=\"0\" />\n";

	echo "</table><input type=\"submit\" value=\"" . _SEND . "\"/><br /><br />\n"
	. "<a href=\"#\" onclick=\"javascript:window.open('index.php?file=Textbox&amp;nuked_nude=index&amp;op=smilies&amp;textarea=textbox_texte','smilies','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=200,height=350,top=100,left=470');return(false)\">\n"
	. "<b>" . _SMILEY . "</b></a></div><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Textbox\">" . _SEEARCHIVES . "</a> ]</div></form><br />\n";
    }
}
echo"</div>\n";
?>
