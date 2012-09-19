<?php
    $bgcolor1 = '#FFF';
    $bgcolor3 = '#DDD';

    $date = time();

    include('../conf.inc.php');
    if($this->data['version']['rev'] == '7' || $this->data['version']['rev'] == '8'){
        if(!isset($_SESSION['hash'])){
            $_SESSION['hash'] = addslashes(@sha1(uniqid(''), true));
        }
    }
    elseif($this->data['version']['rev'] == '9'){
        $_SESSION['hash'] = HASHKEY;
    }

    function UpdatePassCrypt($pass, $decal = null){
        $builder = '';
        $decal = $decal === null ? rand(0, 15) :$decal;
        $pass = sha1($pass);
        for ($i = 0; $i < strlen($pass) * 2; $i++){
            if ($i % 2 == 0){
                $builder .= $pass[$i / 2];
            }
            else{
                $builder .= substr($_SESSION['hash'] , ($i / 2 + $decal) % 20, 1);
            }
        }
        return '%'.dechex($decal).md5($builder);
    }
    
    function replaceBBCodeUpdate1($var) {
        return "<img style=\"border: 0;\" src=\"" . checkimg($var[1]) . "\" alt=\"\" />";
    }
    
    function replaceBBCodeUpdate2($var) {
        return "<a href=\"" . checkimg($var[3]) . "\" class=\"thickbox\" alt=\"\"><img style=\"border: 0;\" width=\"" . $var[1] . "\" height=\"" . $var[2] . "\" src=\"" . checkimg($var[3]) . "\" alt=\"\" /></a>";
    }

    function checkimg($url){
        $url = rtrim($url);
        $ext = strrchr($url, ".");
        $ext = substr($ext, 1);
        if (!preg_match("`\.php`i", $url) && !preg_match("`\.htm`i", $url) && !preg_match("`\.[a-z]htm`i", $url) && substr($url, -1) != "/" && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`", $ext) || preg_match("`png`i", $ext) || preg_match("`bmp`i", $ext))) $img = $url;
        else $img = "images/noimagefile.gif";
        return($img);
    }

    function BBcode($texte, $db_prefix, $imgMaxWidth = 0, $imgClic = FALSE){        
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

    $bbUpdate = array(
        '_block' => array('bid', 'content'),
        '_calendar' => array('id', 'description'),
        '_comment' => array('id', 'comment'),
        '_config' => array('inscription_mail', 'value'),
        '_config_x1' => array('inscription_charte', 'value'),
        '_config_x2' => array('footmessage', 'value'),
        '_config_x3' => array('recrute_charte', 'value'),
        '_config_x4' => array('recrute_mail', 'value'),
        '_config_x5' => array('defie_charte', 'value'),
        '_config_x6' => array('defie_mail', 'value'),
        '_contact' => array('id', 'message'),
        '_defie' => array('id', 'comment'),
        '_discussion' => array('id', 'texte'),
        '_downloads' => array('id', 'description'),
        '_downloads_cat' => array('cid', 'description'),
        '_forums' => array('id', 'comment'),
        '_forums_messages' => array('id', 'txt'),
        '_gallery' => array('sid', 'description'),
        '_gallery_cat' => array('cid', 'description'),
        '_guestbook' => array('id', 'comment'),
        '_irc_awards' => array('id', 'text'),
        '_liens' => array('id', 'description'),
        '_liens_cat' => array('cid', 'description'),
        '_match' => array('warid', 'report'),
        '_news' => array('id', 'texte'),
        '_news_x1' => array('id', 'suite'),
        '_recrute' => array('id', 'comment'),
        '_sections' => array('artid', 'content'),
        '_sections_cat' => array('secid', 'description'),
        '_serveur_cat' => array('cid', 'description'),
        '_shoutbox' => array('id', 'texte'),
        '_userbox' => array('mid', 'message'),
        '_users' => array('id', 'signature')
    );

    function tableExist($tablename, $prefix){
        $sql = mysql_query("SHOW TABLES FROM `".$_SESSION['db_name']."` LIKE '".$prefix."_".$tablename."'") or die (mysql_error());
        $count = mysql_num_rows($sql);
        return $count == 0 ? false : true;
    }

    function fieldExist($tablename, $fieldname, $prefix){
        $sql = mysql_query("SHOW COLUMNS FROM `".$prefix."_".$tablename."` LIKE '".$fieldname."'") or die (mysql_error());
        $count = mysql_num_rows($sql);
        return $count == 0 ? false : true;
    }

    
    switch($table){
        case'table_match':
                            if(!fieldExist('match', 'map', $db_prefix)){
                                $sql = mysql_query('SELECT warid, map_1, map_2, map_3, score_team, score_adv FROM `'.$db_prefix.'_match`') or die(mysql_error());
                                $i=0;
                                $array_matchs = array();
                                while(list($id, $map_1, $map_2, $map_3, $score_team, $score_adv) = mysql_fetch_array($sql)){
                                    $array_matchs[$i]['id'] = $id;
                                    $array_matchs[$i]['map'] = $map_1.'|'.$map_2.'|'.$map_3;
                                    $array_matchs[$i]['score_team'] = $score_team;
                                    $array_matchs[$i]['score_adv'] = $score_adv;
                                    $i++;
                                }
                                mysql_query('ALTER TABLE `'.$db_prefix.'_match` DROP `map_1`, DROP `map_2`, DROP `map_3`') or die(mysql_error());
                                mysql_query('ALTER TABLE `'.$db_prefix.'_match` ADD `map` TEXT NOT NULL ,
                                                                ADD `tscore_team` FLOAT DEFAULT NULL ,
                                                                ADD `tscore_adv` FLOAT DEFAULT NULL
                                                                ') or die(mysql_error());
                                $i=0;
                                foreach($array_matchs as $k){
                                    mysql_query('UPDATE `'.$db_prefix.'_match` SET map = \''.mysql_real_escape_string($array_matchs[$i]['map']).'\',
                                                                        tscore_team = \''.$array_matchs[$i]['score_team'].'\',
                                                                        tscore_adv = \''.$array_matchs[$i]['score_adv'].'\'
                                                                        WHERE warid = \''.$array_matchs[$i]['id'].'\' ') or die(mysql_error());
                                    $i++;
                                }
                            }
                            echo 'OK';
        break;
    	case'table_match_files':
                                if(!tableExist('match_files', $db_prefix)){
                                    mysql_query('ALTER TABLE `'.$db_prefix.'_fichiers_joins` RENAME AS `'.$db_prefix.'_match_files`') or die (mysql_error());
                                }
                                echo 'OK';
    	break;
        case'table_action':
                            if(!tableExist('action', $db_prefix)){
                                mysql_query("CREATE TABLE `" . $db_prefix . "_action` (
                                    `id` int(11) NOT NULL auto_increment,
                                    `date` varchar(30) NOT NULL default '0',
                                    `pseudo`  text NOT NULL,
                                    `action`  text NOT NULL,
                                    PRIMARY KEY  (`id`)
                                  ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;")or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'table_banned':
                            if(!fieldExist('banned', 'date', $db_prefix)){
                                mysql_query('ALTER TABLE `' . $db_prefix . '_banned` ADD `date` VARCHAR(20)  NULL AFTER `email`')or die(mysql_error());
                            }
                            if(!fieldExist('banned', 'dure', $db_prefix)){
                                mysql_query('ALTER TABLE `' . $db_prefix . '_banned` ADD `dure` VARCHAR(20)  NULL AFTER `date`')or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'table_comment_mod':
                            if(!tableExist('comment_mod', $db_prefix)){
                                mysql_query("CREATE TABLE `" . $db_prefix . "_comment_mod` (
                                    `id` int(11) NOT NULL auto_increment,
                                    `module` text NOT NULL,
                                    `active` int(1) NOT NULL,
                                    PRIMARY KEY  (`id`)
                                  ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;")or die(mysql_error());
                                
                                mysql_query("INSERT INTO `" . $db_prefix . "_comment_mod` (`id`, `module`, `active`) VALUES
                                    (1, 'news', 1),
                                    (2, 'download', 1),
                                    (3, 'links', 1),
                                    (4, 'survey', 1),
                                    (5, 'wars', 1),
                                    (6, 'gallery', 1),
                                    (7, 'sections', 1);")or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'table_contact':
                            if(!tableExist('contact', $db_prefix)){
                                $sql = mysql_query('SHOW TABLES FROM `'.$this->data['db_name'].'` ') or die (mysql_error());
                                while(list($table_db) = mysql_fetch_array($sql)){
                                    $array_db[] = $table_db;
                                }
                                if(!in_array($db_prefix.'contact', $array_db)){
                                    mysql_query("CREATE TABLE `" . $db_prefix . "_contact` (
                                        `id` int(11) NOT NULL auto_increment,
                                        `titre` varchar(200) NOT NULL default '',
                                        `message` text NOT NULL,
                                        `email` varchar(80) NOT NULL default '',
                                        `nom` varchar(200) NOT NULL default '',
                                        `ip` varchar(50) NOT NULL default '',
                                        `date` varchar(30) NOT NULL default '',
                                        PRIMARY KEY  (`id`),
                                        KEY `titre` (`titre`)
                                    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;") or die (mysql_error());
                                }
                            }
                            echo 'OK';
        break;
        case'table_discussion':
                            if(!tableExist('discussion', $db_prefix)){
                                mysql_query("CREATE TABLE `" . $db_prefix . "_discussion` (
                                    `id` int(11) NOT NULL auto_increment,
                                    `date` varchar(30) NOT NULL default '0',
                                    `pseudo`  text NOT NULL,
                                    `texte`  text NOT NULL,
                                    PRIMARY KEY  (`id`)
                                  ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;")or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'table_erreursql':
                            if(!tableExist('erreursql', $db_prefix)){
                                mysql_query("CREATE TABLE `".$db_prefix."_erreursql` (
                                    `id` int(11) NOT NULL auto_increment,
                                    `date` varchar(30) NOT NULL default '0',
                                    `lien`  text NOT NULL,
                                    `texte`  text NOT NULL,
                                    PRIMARY KEY  (`id`)
                                  ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;")or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'table_games':
                            if(!fieldExist('games', 'map', $db_prefix)){
                                mysql_query("ALTER TABLE `" . $db_prefix . "_games` ADD `map` TEXT NOT NULL;")or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'table_notification':
                            if(!tableExist('notification', $db_prefix)){
                                mysql_query("CREATE TABLE `" . $db_prefix . "_notification` (
                                    `id` int(11) NOT NULL auto_increment,
                                    `date` varchar(30) NOT NULL default '0',
                                    `type`  text NOT NULL,
                                    `texte`  text NOT NULL,
                                    PRIMARY KEY  (`id`)
                                  ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;")or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'table_tmpses':
                            if(!tableExist('tmpses', $db_prefix)){
                                mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_tmpses` (
                                    `session_id` varchar(64) NOT NULL,
                                    `session_vars` text NOT NULL,
                                    `session_start` bigint(20) NOT NULL,
                                    PRIMARY KEY (`session_id`)
                                    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;")or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'table_users':
                            if(!fieldExist('users', 'erreur', $db_prefix)){
                                mysql_query("ALTER TABLE `" . $db_prefix . "_users` ADD `erreur` INT(10) NOT NULL default '0'")or die(mysql_error());
                            }
                            if(!fieldExist('users', 'token', $db_prefix)){
                                mysql_query("ALTER TABLE `" . $db_prefix . "_users` ADD `token` VARCHAR(13) NULL DEFAULT NULL, ADD `token_time` varchar(10) NOT NULL DEFAULT '0'")or die(mysql_error());
                            }
                            echo 'OK';
        break;
        case'update_bbcode':
                            if($this->data['version']['rev'] == 7 || $this->data['version']['rev'] == 8){
                                if (!tableExist('contact', $db_prefix)) {
                                    unset($bbUpdate['_contact']);
                                }
                                foreach($bbUpdate as $k => $v){
                                    if(preg_match('#_x[0-9]{1}#', $k)) $k = substr($k, 0, -3);
                                    
                                    if ($k == '_config')
                                    {
                                        $select = 'SELECT value FROM `'.$db_prefix.'_config` WHERE name=\''.$v[0].'\' ';
                                    }
                                    else
                                    {
                                        $select = 'SELECT '.$v[0].', '.$v[1].' FROM '.$db_prefix.$k.' ';
                                    }
                                    
                                    $sql = mysql_query($select) or die(mysql_error());
                                    while($row = mysql_fetch_row($sql)){
                                        if(isset($row[1])){
                                            $row[1] = mysql_real_escape_string(bbcode(stripslashes($row[1]), $db_prefix));
                                        }
                                        else{
                                            $row[0] = mysql_real_escape_string(bbcode(stripslashes($row[0]), $db_prefix));
                                        }
                                        
                                        if ($k == '_config')
                                        {
                                            $update = "UPDATE `".$db_prefix.$k."` SET value='".$row[0]."' WHERE name='".$v[0]."' ";
                                        }
                                        else
                                        {
                                            $update = "UPDATE `".$db_prefix.$k."` SET ".$v[1]."='".$row[1]."' WHERE ".$v[0]."='".$row[0]."' ";
                                        }
                                        mysql_query($update) or die (mysql_error());
                                    }
                                }
                            }
                             echo 'OK';
        break;
        case'update_config':
                            $sql = mysql_query('SELECT value FROM `'.$db_prefix.'_config` WHERE name=\'version\' ')or die (mysql_error());
                            list($old_version) = mysql_fetch_array($sql);
                            if($old_version != '1.7.9'){
                                mysql_query('UPDATE `' . $db_prefix . '_config` SET value = \'1.7.9\' WHERE name = \'version\'')or die(mysql_error());
                                if($this->data['version']['rev'] == 7 || $this->data['version']['rev'] == 8){
                                    // version 1.7.7 ou 1.7.8                                
                                    mysql_query('UPDATE `' . $db_prefix . '_config` SET value = \'Impact_Nk\' WHERE name = \'theme\'')or die(mysql_error());
                                    mysql_query('UPDATE `' . $db_prefix . '_config` SET value = \'quakenet.org\' WHERE name = \'irc_serv\'')or die(mysql_error());
                                    mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'screen\', \'on\')')or die(mysql_error());
                                    mysql_query('INSERT INTO `' . $db_prefix . '_modules` (`nom`, `niveau`, `admin`) VALUES (\'Stats\', 0, 2)')or die(mysql_error());
                                    if(!tableExist('contact', $db_prefix)){
                                        mysql_query('INSERT INTO `' . $db_prefix . '_modules` (`nom`, `niveau`, `admin`) VALUES (\'Contact\', 0, 3)')or die(mysql_error());
                                        $recup_mail = mysql_query("SELECT value FROM `" . $db_prefix . "_config` WHERE name = 'mail'")or die(mysql_error());
                                        list($mail_admin) = mysql_fetch_array($recup_mail)or die(mysql_error());
                                        mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'contact_mail\', \''.$mail_admin.'\')')or die(mysql_error());
                                        mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'contact_flood\', \'60\')')or die(mysql_error());
                                    }
                                }
                                if($old_version != '1.7.9 RC6'){
                                    // Tout sauf 1.7.9 RC6
                                    if ($this->data['lang_install'] == 'french'){
                                        mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'dateformat\', \'%d/%m/%Y - %H:%M:%S\')')or die(mysql_error());
                                        mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'datezone\', \'1\')')or die(mysql_error());
                                    }
                                    else{
                                        mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'dateformat\', \'%m/%d/%Y - %H:%M:%S\')')or die(mysql_error());
                                        mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'datezone\', \'0\')')or die(mysql_error());
                                    }
                                    mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'time_generate\', \'on\')')or die(mysql_error());
                                    mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'video_editeur\', \'on\')')or die(mysql_error());
                                    mysql_query('INSERT INTO `' . $db_prefix . '_config` (name, value) VALUES (\'scayt_editeur\', \'on\')')or die(mysql_error());
                                }
                            }
                            echo 'OK';
        break;
        case'update_pass':
                            $sql = mysql_query('SELECT pass FROM `'.$db_prefix.'_users` ORDER BY RAND() LIMIT 1') or die(mysql_error());
                            list($result) = mysql_fetch_array($sql);
                            if(substr($result, 0, 1) != '%' && substr($result, 0, 1) != '#'){
                                $sql_user = mysql_query("SELECT id, pass FROM `" . $db_prefix . "_users`");
                                while (list($userid, $userpass) = mysql_fetch_row($sql_user)){
                                    mysql_query('UPDATE `' . $db_prefix . '_users` SET pass=\'' . UpdatePassCrypt($userpass) . '\' WHERE id = \'' . $userid . '\'')or die(mysql_error());
                                }
                            }

                            echo 'OK';
        break;
        case 'table_smilies' :
            $sql = mysql_query('SELECT id FROM `'.$db_prefix.'_smilies` WHERE code LIKE \'%\\\'%\'') or die(mysql_error());
            $i = 0;
            while ( $idSmiley = mysql_fetch_row($sql)){
                $i++;
                mysql_query('UPDATE `' . $db_prefix . '_smilies` SET code=\'#smiley' . $i . '#\'' . ' WHERE id = \'' . $idSmiley[0] . '\'') or die(mysql_error());
            }
            echo 'OK';
        break;
        case'remove_style':
                            mysql_query("DROP TABLE IF EXISTS  `" . $db_prefix . "_style` ")or die(mysql_error());
                            echo 'OK';
        break;
        case'remove_editeur':
                            mysql_query("DROP TABLE IF EXISTS `" . $db_prefix . "_editeur` ")or die(mysql_error());
                            if($this->data['version']['rev'] == '9'){
                                $sql = mysql_query('SELECT id, comment FROM `'.$db_prefix.'_forums` ')or die(mysql_error());
                                while(list($id, $comment) = mysql_fetch_array($sql)){
                                    $comment = str_replace('<p>', '', $comment);
                                    $comment = str_replace('</p>', '', $comment);
                                    mysql_query('UPDATE `'.$db_prefix.'_forums` set comment = \''.mysql_real_escape_string($comment).'\' WHERE id=\''.$id.'\' ')or die(mysql_error());
                                }
                            }
                            echo 'OK';
        break;
        case'remove_package_manager':
                            mysql_query("DROP TABLE IF EXISTS `" . $db_prefix . "_packages` ")or die(mysql_error());
                            echo 'OK';
        break;
        case'remove_turkish.lang.php':
                            if (is_file('../modules/404/lang/turkish.lang.php')){
                                $path = '../modules/404/lang/turkish.lang.php';
                                if(is_writeable($path)){
                                    @chmod ($path, 0755);
                                    @unlink($path);
                                    if (is_file($path)){
                                        echo _DELTURKISH;
                                        exit();
                                    }
                                }
                                else{
                                    echo _DELTURKISH;
                                    exit();
                                }
                            }
                            echo 'OK';
        break;
     case 'table_forums_read':
          $req = mysql_query("SELECT * FROM `" . $db_prefix . "_forums_read` ") or die(mysql_error());
          if ($req) {
               $users = array();
               while ($data = mysql_fetch_assoc($req)) {
                    if (!isset($data['id'])) {
                         echo "OK";
                         break;
                    }
                    if (!isset($users[$data['user_id']]['thread_id']) || empty($users[$data['user_id']]['thread_id']))
                         $users[$data['user_id']]['thread_id'] = ',';

                    if (!isset($users[$data['user_id']]['forum_id']) || empty($users[$data['user_id']]['forum_id']))
                         $users[$data['user_id']]['forum_id'] = ',';
                    if (strrpos($users[$data['user_id']]['thread_id'], ',' . $data['thread_id'] . ',') === false)
                         $users[$data['user_id']]['thread_id'] .= $data['thread_id'] . ',';
                    if (strrpos($users[$data['user_id']]['forum_id'], ',' . $data['forum_id'] . ',') === false)
                    $users[$data['user_id']]['forum_id'] .= $data['forum_id'] . ',';
               }
               if (sizeof($users)) {
                    mysql_query("DROP TABLE IF EXISTS `" . $db_prefix . "_forums_read`") or die(mysql_error());
                    $SQL = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_forums_read`(
                              `user_id` varchar(20) NOT NULL PRIMARY KEY,
                              `forum_id` text NOT NULL,
                              `thread_id` text NOT NULL
                         ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
                    mysql_query($SQL) or die(mysql_error());
                    $SQL = "INSERT INTO `" . $db_prefix . "_forums_read` (user_id, forum_id, thread_id) VALUES ";
                    $i = 0;
                    foreach ($users as $key => $user) {
                         if ($i > 0)
                              $SQL .= ', ';
                         $SQL .= "('" . $key . "', '" . $user['forum_id'] . "', '" . $user['thread_id'] . "') ";
                         $i++;
                    }
                    mysql_query($SQL) or die(mysql_error());
               }
          }
          echo "OK";
          break;
        default:
            echo _WRONGTABLENAME;
        break;
    }    
?>