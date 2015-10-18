<?php
/**
 * bbcode.class.php
 *
 * Manage BBcode
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class bbcode {

    /*
     * Store smilies list
     */
    private $_smiliesList;

    /*
     * Constructor. Set used vars
     */
    public function __construct() {
        $this->_smiliesList = $this->_loadSmilies();
    }

    /*
     * Callback function for replace a image
     */
    private function _replaceImg($var) {
        return '<img style="border: 0;" src="'. self::_checkImg($var[1]) .'" alt="" />';
    }

    /*
     * Callback function for replace a image with defined size
     */
    private function _replaceImgWithSize($var) {
        $url = self::_checkImg($var[3]);

        return '<a href="'. $url .'" class="thickbox" alt=""><img style="border: 0;" width="'. $var[1] .'" height="'. $var[2] .'" src="'. $url .'" alt="" /></a>';
    }

    /*
     * Check if url is a image
     */
    private function _checkImg($url) {
        $url = rtrim($url);

        // TODO : Definir setlocale
        if (in_array(strtolower(pathinfo($url, PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'gif', 'png', 'bmp')))
            return $url;

        return 'images/noimagefile.gif';
    }

    /*
     * Translate bbcode
     */
    public function apply($text) {
        if ($text != '') {
            $i18n = i18n::getInstance();

            $text = ' '. $text;
            $text = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3"  onclick="window.open(this.href); return false;">\2://\3</a>', $text);
            $text = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\2.\3"  onclick="window.open(this.href); return false;">\2.\3</a>', $text);
            $text = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $text);
            $text = str_replace("\r", '', $text);
            $text = str_replace("\n", '<br />', $text);
            $text = preg_replace('/\[color=(.*?)\](.*?)\[\/color\]/i', '<span style="color: \1;">\2</span>', $text);
            $text = preg_replace('/\[size=(.*?)\](.*?)\[\/size\]/i', '<span style="font-size: \1px;">\2</span>', $text);
            $text = preg_replace('/\[font=(.*?)\](.*?)\[\/font\]/i', '<span style="font-family: \1;">\2</span>', $text);
            $text = preg_replace('/\[align=(.*?)\](.*?)\[\/align\]/i', '<div style="text-align: \1;">\2</div>', $text);
            $text = str_replace('[b]', '<strong>', $text);
            $text = str_replace('[/b]', '</strong>', $text);
            $text = str_replace('[i]', '<em>', $text);
            $text = str_replace('[/i]', '</em>', $text);
            $text = str_replace('[li]', '<ul><li>', $text);
            $text = str_replace('[/li]', '</li></ul>', $text);
            $text = str_replace('[u]', '<span style="text-decoration: underline;">', $text);
            $text = str_replace('[/u]', '</span>', $text);
            $text = str_replace('[center]', '<div style="text-align: center;">', $text);
            $text = str_replace('[/center]', '</div>', $text);
            $text = str_replace('[strike]', '<span style="text-decoration: line-through;">', $text);
            $text = str_replace('[/strike]', '</span>', $text);
            $text = str_replace('[blink]', '<span style="text-decoration: blink;">', $text);
            $text = str_replace('[/blink]', '</span>', $text);
            $text = preg_replace('/\[flip\](.*?)\[\/flip\]/i', '<div style="width: 100%;filter: FlipV;">\1</div>', $text);
            $text = preg_replace('/\[blur\](.*?)\[\/blur\]/i', '<div style="width: 100%;filter: blur();">\1</div>', $text);
            $text = preg_replace('/\[glow\](.*?)\[\/glow\]/i', '<div style="width: 100%;filter: glow(color=red);">\1</div>', $text);
            $text = preg_replace('/\[glow=(.*?)\](.*?)\[\/glow\]/i', '<div style="width: 100%;filter: glow(color=\1);">\2</div>', $text);
            $text = preg_replace('/\[shadow\](.*?)\[\/shadow\]/i', '<div style="width: 100%;filter: shadow(color=red);">\1</div>', $text);
            $text = preg_replace('/\[shadow=(.*?)\](.*?)\[\/shadow\]/i', '<div style="width: 100%;filter: shadow(color=\1);">\2</div>', $text);
            $text = preg_replace('/\[email\](.*?)\[\/email\]/i', '<a href="mailto:\1">\1</a>', $text);
            $text = preg_replace('/\[email=(.*?)\](.*?)\[\/email\]/i', '<a href="mailto:\1">\2</a>', $text);
            $text = str_replace('[quote]', '<br /><table style="background: #DDD;width:100%;" cellpadding="3" cellspacing="1" border="0"><tr><td style="background: #FFF;color: #000"><div id="quote" style="border: 0; overflow: auto;"><strong>'. $i18n['QUOTE'] .' :</strong><br />', $text);
            $text = preg_replace('/\[quote=(.*?)\]/i', '<br /><table style="background: #DDD;width:100%;" cellpadding="3" cellspacing="1"  border="0"><tr><td style="background: #FFF;color: #000"><div id="quote" style="border: 0; overflow: auto;"><strong>\1 '. $i18n['HAS_WROTE'] .' :</strong></div>', $text);
            $text = str_replace('[/quote]', '</div></td></tr></table><br />', $text);
            $text = str_replace('[code]', '<br /><table style="background: #DDD;width:100%;" cellpadding="3" cellspacing="1" border="0"><tr><td style="background: #FFF;color: #000"><div id="code" style="border: 0; overflow: auto;"><strong>'. $i18n['CODE'] .' :</strong><pre class="brush:php;">', $text);
            $text = str_replace('[/code]', '</pre></div></td></tr></table>', $text);
            $text = preg_replace_callback('/\[img\](.*?)\[\/img\]/i', array($this, '_replaceImg') , $text);
            $text = preg_replace_callback('/\[img=(.*?)x(.*?)\](.*?)\[\/img\]/i', array($this, '_replaceImgWithSize'), $text);
            $text = preg_replace('/\[flash\](.*?)\[\/flash\]/i', '<object type="application/x-shockwave-flash" data="\1"><param name="movie" value="\1" /><param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" /></object>', $text);
            $text = preg_replace('/\[flash=(.*?)x(.*?)\](.*?)\[\/flash\]/i', '<object type="application/x-shockwave-flash" data="\3" width="\1" height="\2"><param name="movie" value="\3" /><param name="pluginurl" value="http://www.macromedia.com/go/getflashplayer" /></object>', $text);
            $text = preg_replace('/\[url\]www.(.*?)\[\/url\]/i', '<a href="http://www.\1" onclick="window.open(this.href); return false;">\1</a>', $text);
            $text = preg_replace('/\[url\](.*?)\[\/url\]/i', '<a href="\1" onclick="window.open(this.href); return false;">\1</a>', $text);
            $text = preg_replace('/\[url=(.*?)\](.*?)\[\/url\]/i', '<a href="\1" onclick="window.open(this.href); return false;">\2</a>', $text);
            $text = preg_replace('#\[s\](http://)?(.*?)\[/s\]#si', '<img style="border: 0;" src="images/icones/\2" alt="" />', $text);
            $text = ltrim($text);

            foreach ($this->_smiliesList as $smilies)
                $text = str_replace(
                    $smilies['code'],
                    '<img src="images/icones/'. $smilies['url'] .'" alt="'. $smilies['name'] .'" />',
                    $text
                );

        }

        return $text;
    }

    /*
     * Load smilies data and store it in PHP session
     */
    private function _loadSmilies() {
        $session = PHPSession::getInstance();

        if (isset($session['smiliesList']))
            return $session['smiliesList'];

        $sql = 'SELECT code, url, name
            FROM `'. $session['db_prefix'] .'_smilies`';

        return ($session['smiliesList'] = db::getInstance()->load()->selectMany($sql));
    }

}

?>