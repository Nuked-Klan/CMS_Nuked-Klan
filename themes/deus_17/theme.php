<?php
// ****************************************************************************
// ** Theme deus_17 - Nuked-KlaN b1.7
// **
// ** Design by DeuS & PHP by MaStErPsX
// ** This theme is valid HTML 4.01, XHTML 1.0 Strict & CSS
// **
// ** http://www.nkdeus.com
// ** http://www.nuked-klan.org
// ****************************************************************************
if (!defined("INDEX_CHECK"))
{
	die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

function top()
{
    global $nuked, $user, $bgcolor2;

     echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
     . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\"><head>\n"
     . "<meta name=\"keywords\" content=\"" . $nuked['keyword'] . "\" />\n"
     . "<meta name=\"Description\" content=\"" . $nuked['description'] . "\" />\n"
	 . '<link rel="shortcut icon"  href="'. $nuked['url'] .'/images/favicon.ico" />'
	 . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniéres news" href="'. $nuked['url'] .'/rss/news_rss.php" type="application/rss+xml" />'
	 . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniers articles" href="'. $nuked['url'] .'/rss/sections_rss.php" type="application/rss+xml" />'
	 . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniers téléchargements" href="'. $nuked['url'] .'/rss/download_rss.php" type="application/rss+xml" />'
	 . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniers liens" href="'. $nuked['url'] .'/rss/links_rss.php" type="application/rss+xml" />'
	 . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniéres images" href="'. $nuked['url'] .'/rss/gallery_rss.php" type="application/rss+xml" />'
	 . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniers sujets" href="'. $nuked['url'] .'/rss/forum_rss.php" type="application/rss+xml" />'
	 . '<link rel="search" type="application/opensearchdescription+xml" href="'. $nuked['url'] .'/opensearch.php" title="Nuked-Klan" />'
	 . "<title>" . $nuked['name'] . " - " . $nuked['slogan'] . "</title>\n";

    if ($_REQUEST['file'] == $nuked['index_site'] && $_REQUEST['page'] == "") {
        $flash = "header.swf";
        $img = "header.gif";
    } else {
        $flash = "headerm.swf";
        $img = "headerm.gif";
    }

	?><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="content-style-type" content="text/css" />
	<link rel="shortcut icon"  href="<?php echo $nuked['url']; ?>/images/favicon.ico" />
	<link title="style" type="text/css" rel="stylesheet" href="themes/deus_17/style.css" />
	<script type="text/javascript" src="themes/deus_17/flash.js"></script>
	</head><body class="fond">

	<table style="margin-left: auto;margin-right: auto;text-align: left;" width="1000" cellpadding="0" cellspacing="0" border="0"><tr><td>
	<script type="text/javascript">show_flash('1000', '286', '<?php echo "themes/deus_17/images/" . $flash; ?>', '#232D46', '<?php echo "titre=" . text2flash($nuked['name']) . "&amp;slogan=" . text2flash($nuked['slogan']); ?>');</script>
	</td></tr></table>

	 <!-- <table style="margin-left: auto;margin-right: auto;text-align: left;" width="1000" cellpadding="0" cellspacing="0" border="0">
	<tr><td><a href="index.php"><img class="image" style="border: 0;" src="themes/deus_17/images/<?php echo $img; ?>" title="<?php echo $nuked['name']; ?>" alt="" /></a></td></tr></table> -->

	<table style="margin-left: auto;margin-right: auto;text-align: left;" width="1000" border="0" cellpadding="0" cellspacing="0"><tr>
	<td style="width: 66px;background-image:url(themes/deus_17/images/barreG.gif);" valign="top">
	<img class="image" src="themes/deus_17/images/pixel.gif" width="66" alt="" /></td><td style="width: 177px;background-image:url(themes/deus_17/images/blokGbg.gif);" valign="top"><?php

	get_blok('gauche');

	echo "</td><td style=\"width: 14px;background-image:url(themes/deus_17/images/barreGcentre.gif);\" valign=\"top\">\n"
	. "<img class=\"image\" src=\"themes/deus_17/images/pixel.gif\" width=\"14px\" height=\"1\" alt=\"\" /></td>\n"
	. "<td style=\"width: 100%;background:" . $bgcolor2 . ";\" valign=\"top\">\n";

    if ($_REQUEST['op'] == "index" && $_REQUEST['file'] != "Admin" && $_REQUEST['page'] != "admin")
    {
        get_blok('centre');
    }
}


function footer()
{
    global $nuked;

    if ($_REQUEST['op'] == "index" && $_REQUEST['file'] != "Admin" && $_REQUEST['page'] != "admin")
    {
        echo "<br />";
        get_blok('bas');
    }

    if ($_REQUEST['file'] == $nuked['index_site'] && $_REQUEST['page'] == "" && $_REQUEST['op'] != "sendfriend")
    {
        echo "</td><td style=\"width: 14px;background-image:url(themes/deus_17/images/barreDcentre.gif);\" valign=\"top\">\n"
        . "<img class=\"image\" src=\"themes/deus_17/images/pixel.gif\" width=\"15\" alt=\"\" /></td>\n"
        . "<td style=\"width: 177px;background-image:url(themes/deus_17/images/blokDbg.gif);\" valign=\"top\">\n";

        get_blok('droite');

    }
    else
    {
        echo "</td><td style=\"width: 4px;background-image:url(themes/deus_17/images/blokDbg.gif);\"><img class=\"image\" src=\"themes/deus_17/images/pixel.gif\" width=\"4\" height=\"1\" alt=\"\" />\n";
    }

	?></td><td style="width: 66px;background-image:url(themes/deus_17/images/barreD.gif);" valign="top"><img class="image" src="themes/deus_17/images/pixel.gif" width="68" height="1" alt="" /></td>
	</tr></table><table style="margin-left: auto;margin-right: auto;text-align: left;" width="1000" border="0" cellpadding="0" cellspacing="0"><?php

    if ($_REQUEST['file'] == $nuked['index_site'] && $_REQUEST['page'] == "") {
        echo "<tr><td style=\"width: 100%;\"><img class=\"image\" src=\"themes/deus_17/images/footerhaut.gif\" width=\"1000\" height=\"75\" alt=\"\" /></td></tr>\n";
    }
    else
    {
        echo "<tr><td style=\"width: 100%;\"><img class=\"image\" src=\"themes/deus_17/images/footerhautm.gif\" width=\"1000\" height=\"75\" alt=\"\" /></td></tr>\n";
    }

	?><tr><td style="width: 100%;">
	<object type="application/x-shockwave-flash" data="themes/deus_17/images/footerbas.swf" width="1000" height="100">
 	<param name="movie" value="themes/deus_17/images/footerbas.swf" />
	<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
	<param name="wmode" value="transparent" />
	<param name="menu" value="false" />
	<param name="quality" value="best" />
	<param name="scale" value="exactfit" />
	</object></td></tr></table>

	<!-- <tr><td style="width: 100%;"><img class="image" src="themes/deus_17/images/footerbas.gif" alt="" /></td></tr></table> -->

	<div style="text-align: center;" class="copyright"><?php echo $nuked['footmessage'] . "<br /></div>";
}


function news($data)
{
    $posted = _NEWSPOSTBY . "&nbsp;<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($data['auteur']) . "\">" . $data['auteur'] . "</a>&nbsp;" . _THE . "&nbsp;". $data['date']. "&nbsp;" . _AT . "&nbsp;" . $data['heure'];
    $comment = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $data['id'] . "\">" . _NEWSCOMMENT . "</a>&nbsp;(" . $data['nb_comment'] . ")&nbsp;" . $data['printpage']. "&nbsp;" . $data['friend'];

	?><table width="485" cellpadding="0" cellspacing="0" border="0"><tr>

	<td style="width: 485px;height: 34px;background-image:url(themes/deus_17/images/newstitre.gif);font-size: 12px;color:#1F2941;" align="center">
	<b><?php echo $data['titre']; ?></b></td></tr></table>

	<!--  <td style="width: 485px;height: 34px;" align="center">
	<object type="application/x-shockwave-flash" data="themes/deus_17/images/newstitre.swf" width="485" height="34">
 	<param name="movie" value="themes/deus_17/images/newstitre.swf" />
	<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
	<param name="wmode" value="transparent" />
	<param name="menu" value="false" />
	<param name="quality" value="best" />
	<param name="scale" value="exactfit" />
	<param name="flashvars" value="titre=<?php echo text2flash($data['titre']); ?>" />
	</object></td></tr></table> -->

	<table style="margin-left: auto;margin-right: auto;text-align: left;" border="0" width="98%" cellpadding="0" cellspacing="2">
	<tr><td valign="top"><?php echo $posted; ?></td></tr>
	<tr><td><?php echo $data['image'] . "<br />" . $data['texte']; ?></td></tr>
	<tr><td style="width: 100%;" valign="bottom" align="right"><?php echo $comment; ?>&nbsp;&nbsp;</td></tr></table>
	<img class="image" src="themes/deus_17/images/newsbas.gif" alt="" /><br /><?php

}


function block_gauche($block)
{

	?><table width="176" border="0" cellspacing="0" cellpadding="0"><tr>

	<td style="height: 34px;background-image:url(themes/deus_17/images/blokGtitre.gif);font-size: 12px;color:#1F2941;" align="center">
    	<b><?php echo $block['titre']; ?></b></td></tr><tr><td>

	<!-- <td style="width: 176px;height: 34px;" align="center">
	<object type="application/x-shockwave-flash" data="themes/deus_17/images/blokGtitre.swf" width="176" height="34">
 	<param name="movie" value="themes/deus_17/images/blokGtitre.swf" />
	<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
	<param name="wmode" value="transparent" />
	<param name="menu" value="false" />
	<param name="quality" value="best" />
	<param name="scale" value="exactfit" />
	<param name="flashvars" value="titre=<?php echo text2flash($block['titre']); ?>" />
	</object></td></tr><tr><td> -->

	<table width="100%" border="0" cellspacing="0" cellpadding="2"><tr><td><?php echo $block['content']; ?></td></tr></table></td></tr>
	<tr><td style="height: 6px;background-image:url(themes/deus_17/images/blokGbas.gif);"><img class="image" src="themes/deus_17/images/pixel.gif" height="6" alt="" /></td></tr></table><?php

}


function block_droite($block)
{

	?><table width="176" border="0" cellspacing="0" cellpadding="0"><tr>

	<td style="height: 34px;background-image:url(themes/deus_17/images/blokDtitre.gif);font-size: 12px;color:#1F2941;" align="center">
    	<b><?php echo $block['titre']; ?></b></td></tr><tr><td>
	<!-- <td style="width: 176px;height: 34px;" align="center">
	<object type="application/x-shockwave-flash" data="themes/deus_17/images/blokDtitre.swf" width="176" height="34">
 	<param name="movie" value="themes/deus_17/images/blokDtitre.swf" />
	<param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" />
	<param name="wmode" value="transparent" />
	<param name="menu" value="false" />
	<param name="quality" value="best" />
	<param name="scale" value="exactfit" />
	<param name="flashvars" value="titre=<?php echo text2flash($block['titre']); ?>" />
	</object></td></tr><tr><td> -->

	<table width="100%" border="0" cellspacing="0" cellpadding="2"><tr><td><?php echo $block['content']; ?></td></tr></table></td></tr>
	<tr><td style="height: 6px;background-image:url(themes/deus_17/images/blokDbas.gif);"><img class="image" src="themes/deus_17/images/pixel.gif" height="6" alt="" /></td></tr></table><?php

}


function block_centre($block)
{
     echo "<table style=\"background: #232D46;\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td>\n"
     . "<table style=\"background: #485A84;\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"8\"><tr>\n"
     . "<td align=\"center\"><b>" . $block['titre'] . "</b></td></tr><tr><td>" . $block['content'] . "</td></tr></table></td></tr></table><br />\n";
}


function block_bas($block)
{
     echo "<table style=\"background: #232D46;\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td>\n"
     . "<table style=\"background: #485A84;\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"8\"><tr>\n"
     . "<td align=\"center\"><b>" . $block['titre'] . "</b></td></tr><tr><td>" . $block['content'] . "</td></tr></table></td></tr></table><br />\n";
}


function opentable()
{
    echo "<table style=\"background: #232D46;\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\"><tr><td>\n"
     . "<table style=\"background: #485A84;\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"8\"><tr><td>\n";
}


function closetable()
{
    echo "</td></tr></table></td></tr></table>\n";
}


function text2flash($str)
{
    $a = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
    $b = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
    $str = @html_entity_decode($str);
    $str = strtr($str, $a, $b);
    return($str);
}


?>