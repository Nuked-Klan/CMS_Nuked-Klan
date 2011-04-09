<?php

define ("INDEX_CHECK", 1);
include ("./../../global.php");
include ("./../../nuked.php"); 
@include ("./../../conf.inc.php");
			connect();
			@session_name('nuked');
			@session_start();
			include ("../../../Includes/constants.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#emotions_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/emotions.js"></script>
</head>
<body style="display: none">
	<div align="center">
		<div class="title">{#emotions_dlg.title}:<br /><br /></div>

		<table border="0" cellspacing="0" cellpadding="4">
		<?php
		$compteur = 1;
		$sql = mysql_query("SELECT code, url, name FROM " . SMILIES_TABLE . " ORDER BY id LIMIT 0, 15");
		while (list($code, $url, $name) = mysql_fetch_array($sql))
		{
			if($compteur ==1)
			{
				echo "<tr>\n";
			}
			$name = htmlentities($name);
			
			echo "<td><a href=\"javascript:EmotionsDialog.insert('".$url."','".$name."');\"><img src=\"images/icones/".$url."\" width=\"18\" height=\"18\" border=\"0\" alt=\"{#emotions_dlg.cool}\" title=\"{#emotions_dlg.cool}\" /></a></td>\n";
			
			if($compteur ==4)
			{
				echo "</tr>\n";
			}
			$compteur++;
		}
		?>
		</table>
	</div>
</body>
</html>
