<?php

$bgcolor1 = '#FFF';
$bgcolor3 = '#DDD';

function replaceBBCodeUpdate1($var) {
    return '<img style="border: 0;" src="'. checkimg($var[1]) .'" alt="" />';
}

function replaceBBCodeUpdate2($var) {
    return '<a href="'. checkimg($var[3]) .'" class="thickbox" alt=""><img style="border: 0;" width="'. $var[1] .'" height="'. $var[2] .'" src="'. checkimg($var[3]) .'" alt="" /></a>';
}

function checkimg($url) {
    $url = rtrim($url);
    $ext = strrchr($url, ".");
    $ext = substr($ext, 1);

    if (
        ! preg_match("`\.php`i", $url)
        && ! preg_match("`\.htm`i", $url)
        && ! preg_match("`\.[a-z]htm`i", $url)
        && substr($url, -1) != "/"
        && (
            preg_match("`jpg`i", $ext)
            || preg_match("`jpeg`i", $ext)
            || preg_match("`gif`", $ext)
            || preg_match("`png`i", $ext)
            || preg_match("`bmp`i", $ext)
        )
    )
        return $url;

    return 'images/noimagefile.gif';
}

function BBcode($texte, $db_prefix, $imgMaxWidth = 0, $imgClic = false) {
    global $bgcolor3, $bgcolor1;
    if ($texte != ""){
        $texte = " " . $texte;
        $texte = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3"  onclick="window.open(this.href); return false;">\2://\3</a>', $texte);
        $texte = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\2.\3"  onclick="window.open(this.href); return false;">\2.\3</a>', $texte);
        $texte = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $texte);
        $texte = str_replace("\r", "", $texte);
        $texte = str_replace("\n", "<br />", $texte);
        $texte = preg_replace("/\[color=(.*?)\](.*?)\[\/color\]/i", "<span style=\"color: \\1;\">\\2</span>", $texte);
        $texte = preg_replace("/\[size=(.*?)\](.*?)\[\/size\]/i", "<span style=\"font-size: \\1px;\">\\2</span>", $texte);
        $texte = preg_replace("/\[font=(.*?)\](.*?)\[\/font\]/i", "<span style=\"font-family: \\1;\">\\2</span>", $texte);
        $texte = preg_replace("/\[align=(.*?)\](.*?)\[\/align\]/i", "<div style=\"text-align: \\1;\">\\2</div>", $texte);
        $texte = str_replace("[b]", "<strong>", $texte);
        $texte = str_replace("[/b]", "</strong>", $texte);
        $texte = str_replace("[i]", "<em>", $texte);
        $texte = str_replace("[/i]", "</em>", $texte);
        $texte = str_replace("[li]", "<ul><li>", $texte);
        $texte = str_replace("[/li]", "</li></ul>", $texte);
        $texte = str_replace("[u]", "<span style=\"text-decoration: underline;\">", $texte);
        $texte = str_replace("[/u]", "</span>", $texte);
        $texte = str_replace("[center]", "<div style=\"text-align: center;\">", $texte);
        $texte = str_replace("[/center]", "</div>", $texte);
        $texte = str_replace("[strike]", "<span style=\"text-decoration: line-through;\">", $texte);
        $texte = str_replace("[/strike]", "</span>", $texte);
        $texte = str_replace("[blink]", "<span style=\"text-decoration: blink;\">", $texte);
        $texte = str_replace("[/blink]", "</span>", $texte);
        $texte = preg_replace("/\[flip\](.*?)\[\/flip\]/i", "<div style=\"width: 100%;filter: FlipV;\">\\1</div>", $texte);
        $texte = preg_replace("/\[blur\](.*?)\[\/blur\]/i", "<div style=\"width: 100%;filter: blur();\">\\1</div>", $texte);
        $texte = preg_replace("/\[glow\](.*?)\[\/glow\]/i", "<div style=\"width: 100%;filter: glow(color=red);\">\\1</div>", $texte);
        $texte = preg_replace("/\[glow=(.*?)\](.*?)\[\/glow\]/i", "<div style=\"width: 100%;filter: glow(color=\\1);\">\\2</div>", $texte);
        $texte = preg_replace("/\[shadow\](.*?)\[\/shadow\]/i", "<div style=\"width: 100%;filter: shadow(color=red);\">\\1</div>", $texte);
        $texte = preg_replace("/\[shadow=(.*?)\](.*?)\[\/shadow\]/i", "<div style=\"width: 100%;filter: shadow(color=\\1);\">\\2</div>", $texte);
        $texte = preg_replace("/\[email\](.*?)\[\/email\]/i", "<a href=\"mailto:\\1\">\\1</a>", $texte);
        $texte = preg_replace("/\[email=(.*?)\](.*?)\[\/email\]/i", "<a href=\"mailto:\\1\">\\2</a>", $texte);
        $texte = str_replace("[quote]", "<br /><table style=\"background: " . $bgcolor3 . ";width:100%;\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\"><tr><td style=\"background: #FFFFFF;color: #000000\"><div id=\"quote\" style=\"border: 0; overflow: auto;\"><strong>" . _QUOTE . " :</strong><br />", $texte);
        $texte = preg_replace("/\[quote=(.*?)\]/i", "<br /><table style=\"background: " . $bgcolor3 . ";width:100%;\" cellpadding=\"3\" cellspacing=\"1\"  border=\"0\"><tr><td style=\"background: #FFFFFF;color: #000000\"><div id=\"quote\" style=\"border: 0; overflow: auto;\"><strong>\\1 " . _HASWROTE . " :</strong></div>", $texte);
        $texte = str_replace("[/quote]", "</div></td></tr></table><br />", $texte);
        $texte = str_replace("[code]", "<br /><table style=\"background: " . $bgcolor3 . ";width:100%;\" cellpadding=\"3\" cellspacing=\"1\" border=\"0\"><tr><td style=\"background: #FFFFFF;color: #000000\"><div id=\"code\" style=\"border: 0; overflow: auto;\"><strong>" . _CODE . " :</strong><pre class=\"brush:php;\" >", $texte);
        $texte = str_replace("[/code]", "</pre></div></td></tr></table>", $texte);
        $texte = preg_replace_callback('/\[img\](.*?)\[\/img\]/i', 'replaceBBCodeUpdate1' , $texte);
        $texte = preg_replace_callback('/\[img=(.*?)x(.*?)\](.*?)\[\/img\]/i', 'replaceBBCodeUpdate2', $texte);
        $texte = preg_replace("/\[flash\](.*?)\[\/flash\]/i", "<object type=\"application/x-shockwave-flash\" data=\"\\1\"><param name=\"movie\" value=\"\\1\" /><param name=\"pluginurl\" value=\"http://www.macromedia.com/go/getflashplayer\" /></object>", $texte);
        $texte = preg_replace("/\[flash=(.*?)x(.*?)\](.*?)\[\/flash\]/i", "<object type=\"application/x-shockwave-flash\" data=\"\\3\" width=\"\\1\" height=\"\\2\"><param name=\"movie\" value=\"\\3\" /><param name=\"pluginurl\" value=\"http://www.macromedia.com/go/getflashplayer\" /></object>", $texte);
        $texte = preg_replace("/\[url\]www.(.*?)\[\/url\]/i", "<a href=\"http://www.\\1\" onclick=\"window.open(this.href); return false;\">\\1</a>", $texte);
        $texte = preg_replace("/\[url\](.*?)\[\/url\]/i", "<a href=\"\\1\" onclick=\"window.open(this.href); return false;\">\\1</a>", $texte);
        $texte = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/i", "<a href=\"\\1\" onclick=\"window.open(this.href); return false;\">\\2</a>", $texte);
        $texte = preg_replace("#\[s\](http://)?(.*?)\[/s\]#si", "<img style=\"border: 0;\" src=\"images/icones/\\2\" alt=\"\" />", $texte);
        $texte = ltrim($texte);

        $sql = mysql_query('SELECT code, url, name FROM `'.$db_prefix.'_smilies`') or die (mysql_error());
        while(list($code, $url, $name) = mysql_fetch_array($sql)){
            $texte = str_replace($code, '<img src="images/icones/'.$url.'" alt="'.$name.'" />', $texte);
        }
    }
    return($texte);
}

?>
