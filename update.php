<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//      Reste à faire :
//          - Ajouter les 2 étapes intallation facile et avancée
//          - Construction de la fonction de routage pour les commandes à executer suivants les versions à mettre à jour
//          - Terminer la mise en place des langues
//          - Rajouter les infos sur les erreurs du check de l'hebergement
//          - Ajouter la possibilité de forcer l'installation en cas de warning sur l'extension file info
//          - Corriger le problème d'encodage de la sauvegarde SQL
//
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

define('INDEX_CHECK', 1);
include('conf.inc.php');

    class update {
        private $data;
        private $step;
        private $db_save_name;
        private $array_lang = array('english', 'french');
        private $array_lien_step = array(
                                                                    1 => array( '_SELECTLANG' => 'update.php?action=checkLang'),
                                                                    2 => array('_CHECKVERSION' => 'update.php?action=checkVersion'),
                                                                    3 => array('_CHECKCOMPATIBILITY' => 'update.php?action=checkCompatibility'),
                                                                    4 => array('_CHECKACTIVATION' => 'update.php?action=checkStats'),
                                                                    5 => array('_DBSAVE' => 'update.php?action=checkSave'),
                                                                    6 => array('_CHECKTYPEUPDATE' => 'update.php?action=checkTypeUpdate')
                                                                );
        private $array_version = array('1.7.6', '1.7.7', '1.7.8', '1.7.9 RC1', '1.7.9 RC2', '1.7.9 RC3', '1.7.9 RC4', '1.7.9 RC5', '1.7.9 RC5.3');
        
        function __construct(){
            $this->initSession();
            $this->routeUser();
        }
        
        private function initSession(){
            session_start();
            if(isset($_SESSION['active']) && $_SESSION['active'] === true){
                foreach($_SESSION as $k => $v){
                    $this->data[$k] = $v;
                }
            }
            $_SESSION['active'] = true;
            $this->importLang();
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Methodes du core, elles permettent l'affichage des pages
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        private function checkLang(){
            $this->step = $this->checkStep(1);
            self::viewTop();
            $this->viewMenu($this->step);   
            echo '<div style="text-align: center;padding:40px;">
                        <h3>Select your language : </h3>
                        <form method="post" action="update.php?action=setLang" >
                            <select name="lang_install">';
            if ($handle = opendir('lang/')){
                while ($f = readdir($handle)){
                    if ($f != ".." && $f != "." && $f != "index.html"){
                        list ($langfile, ,) = explode ('.', $f);
                        echo '<option value="' . $langfile . '" ';
                        if($this->data['lang_install'] == $langfile)
                            echo 'selected="selected" ';
                        echo '>'.$langfile.'</option>';
                    }
                }                
                closedir($handle);
            }                    
            echo '         </select>
                            <input type="submit" name="ok" value="send" />
                        </form>
                    </div>';
            self::viewInfos($this->step);
            self::viewBottom();
        }
        
        private function setLang(){
            if(isset($_REQUEST['lang_install']) && in_array($_REQUEST['lang_install'], $this->array_lang)){
                $_SESSION['lang_install'] = $_REQUEST['lang_install'];
            }
            self::redirect('update.php?action=checkVersion', 0);
        }
        
        private function checkVersion(){
            global $db_prefix;
            $this->step = $this->checkStep(2);
            self::viewTop();
            $this->viewMenu($this->step);
            if(!isset($this->data['version'])){
                $this->bddConnect();
                $sql_version = mysql_query ('SELECT value FROM '.$db_prefix.'_config WHERE name=\'version\' ');
                list($version) = mysql_fetch_array($sql_version);
                $_SESSION['version'] = $this->data['version'] = $version;
            }
            echo '<div style="text-align: center;padding:40px;">
                        <h3 style="margin-bottom:30px;" >'. _CURRENTVERSIONUSED .' : '.$this->data['version'].'</h3>
                        <form action="update.php?action=setVersion" method="post" >
                            <input type="button" name="conf_version" value="'. _CONFIRM .'" onclick="document.location=\'update.php?action=checkCompatibility\';" />
                            <input type="button" name="another_version" value="'. _NOOTHERVERSION .'" onclick="document.location=\'update.php?action=setVersion\';" />
                        </form>
                    </div>';
            self::viewInfos($this->step);
            self::viewBottom();

        }
        
        private function setVersion(){
            if(isset($_REQUEST['version']) && in_array($_REQUEST['version'], $this->array_version)){
                $_SESSION['version'] = $_REQUEST['version'];
                self::redirect('update.php?action=checkCompatibility', 0);
            }
            else{
                $this->step = $this->checkStep(2);
                self::viewTop();
                $this->viewMenu($this->step);
                echo '<div style="text-align: center;padding:40px;">
                            <h3>'. _PLSSELECTVERSION .'</h3>
                            <form action="update.php?action=setVersion" method="post" >
                                <label>Version&nbsp;:&nbsp;<select name="version">';
                foreach($this->array_version as $k){
                    echo '<option value="'.$k.'" >'.$k.'</option>';
                }
                echo '        </select></label>
                                <p>
                                    '. _WARNCHANGEVERSION .'
                                </p>                                
                                <input type="submit" name="valider" value="'. _CONFIRM.'" /> <input type="button" value="'._BACK.'" onclick="document.location=\'update.php?action=checkVersion\';" />
                            </form>
                        </div>';
                self::viewInfos($this->step);
                self::viewBottom();
            }
        }
        
        private function checkCompatibility(){
            $this->step = $this->checkStep(3);
            self::viewTop();
            $this->viewMenu($this->step);
            echo '<div style="text-align: center;padding:40px;">
                        <h3 style="margin-bottom:30px;" >'. _CHECKCOMPATIBILITYHOSTING .'</h3>
                        <table style="width:500px;margin:20px auto;border:1px solid #ddd;">
                            <tr>
                                <td style="width:80%;"><b>'._COMPOSANT.'</b></td>
                                <td style="width:20%;text-align:center;"><b>'._COMPATIBILITY.'</b></td>';
            $array_requirements = $this->requirements();
            foreach($array_requirements as $k => $v){
                $src = $v == 1 ? 'img/ok.png' : 'img/nook.png';
                $src = $v == 2 ? 'img/warning.png' : $src;
                echo '<tr>
                                <td>'.constant($k).'</td>
                                <td style="text-align:center;"><img src='.$src.' alt="" />
                            </tr>'; 
            }
            echo '</table>';            
            $compatibility = in_array(3, $array_requirements) ? false : true;
            if($compatibility === true){
                
            echo '<form action="#" method="post" >
                            <input type="button" value="'._CONTINUE.'" onclick="document.location=\'update.php?action=checkStats\';" />
                        </form>
                    </div>';
            }
            else{
                echo '<p>'._BADHOSTING.'</p>';
            }
            self::viewInfos($this->step);
            self::viewBottom();
        }
        
        private function checkStats(){
            $this->step = $this->checkStep(4);
            self::viewTop();
            $this->viewMenu($this->step);
            $checked = isset($this->data['stats']) && $this->data['stats'] === false ? '' : 'checked="checked" ';
            echo '<div style="text-align: center;padding:40px;">
                        <h3 style="margin-bottom:30px;" >Activation des statistiques anonymes</h3>
                        <p>
                            Afin d\'améliorer au mieux le CMS Nuked Klan, en tenant compte de l\'utilisation des administrateurs de sites NK,<br/>
                            nous avons mis en place sur cette nouvelle version un système d\'envoi de statistiques anonymes.
                        </p>
                        <p>
                            Vous avez le choix d\'activer ou non ce système, mais sachez qu\'en l\'activant vous permettrez à l\'équipe de Developpement/Marketing<br/>
                            de mieux répondre à vos attentes.
                        </p>
                        <p>
                            Pour une totale transparence, lors de l\'envoi des statistiques, vous serez informé dans l\'administration, des données envoyées.<br/>
                            Sachez qu\'à tout moment vous aurez la possibilité de désactiver l\'envoi des statistiques dans les préférences générales de votre administration.
                        </p>
                        <form action="update.php?action=setStats" method="post" >
                            <label><input type="checkbox" name="conf_stats" '.$checked.' style="margin-top:20px;" />&nbsp; Oui j\'autorise l\'envoi de statistiques anonymes à Nuked-Klan</label>
                            <input type="submit"  value="Valider" style="margin-top:20px;" />
                        </form>
                    </div>';
            self::viewInfos($this->step);
            self::viewBottom();
        }
        
        private function setStats(){
            $_SESSION['stats'] = $_REQUEST['conf_stats'] == 'on' ? true : false;
            self::redirect('update.php?action=checkSave', 0);
        }
        
        private function checkSave(){
            $this->step = $this->checkStep(5);
            self::viewTop();
            $this->viewMenu($this->step);
            echo '<div style="text-align: center;padding:40px;">
                        <h3 style="margin-bottom:30px;" >Sauvegarde de votre base de donnée actuelle</h3>
                        <form action="update.php?action=makeSave" method="post" >
                            <input type="submit" value="Sauvegarder"  />
                            <input type="button" value="Non merci!" onclick="document.location=\'update.php?action=checkTypeUpdate\';" />
                        </form>
                    </div>';
            self::viewInfos($this->step);
            self::viewBottom();
        }
        
        private function makeSave(){
            $this->step = $this->checkStep(5);
            self::viewTop();
            $this->viewMenu($this->step);
            if($this->createBackupBdd()){                
                echo '<div style="text-align: center;padding:40px;">
                        <h3 style="margin-bottom:30px;" >Base de donnée sauvegardée</h3>
                        <p>
                            Votre base de donnée à bien été sauvegardée, vous pouvez la télécharger ici :
                        </p>
                        <a href="'.$this->db_save_name.'">Sauvegarde</a>
                        <form action="#" method="post" style="margin-top:20px;">
                            <input type="button" value="'._CONTINUE.'" onclick="document.location=\'update.php?action=checkTypeUpdate\';" />
                        </form>
                    </div>';
            }
            else{
                echo '<div style="text-align: center;padding:40px;">
                        <h3 style="margin-bottom:30px;" >Une erreur est survenue !!!</h3>
                        <p>
                            Une erreur est survenue lors de la sauvegarde de votre base de donnée, veuillez réessayer.
                        </p>
                        <a href="update.php?action=checkSave">[ '._BACK.' ]</a> 
                    </div>';
            }
            self::viewInfos($this->step);
            self::viewBottom();
        }

        private function checkTypeUpdate(){
            $this->step = $this->checkStep(6);
            self::viewTop();
            $this->viewMenu($this->step);            
            echo '<div style="text-align: center;padding:40px;">
                        <h3>'._WELCOMEINSTALL.'</h3>
                        <p>'._GUIDEINSTALL.'</p>
                        <form action="update.php?action=setLang" method="post" >
                            <input type="button" name="upgradespeed" value="'._UPGRADESPEED.'" onclick="document.location=\'update.php?action=edit_config\';" />
                            <input type="button" name="upgrade" value="'._UPGRADE.'" onclick="document.location=\'update.php?action=edit_config_assistant\';" />
                        </form>
                    </div>';
            self::viewInfos($this->step);
            self::viewBottom();
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Methodes de services, appelées pour effectuer une tâche précises
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        private function importLang(){
            if(isset($this->data['lang_install']) && in_array($this->data['lang_install'], $this->array_lang)){
                include('lang/'.$this->data['lang_install'].'.lang.php');
            }
            else{
                include('lang/english.lang.php');
            }
        }
        
        private function checkStep($this_step){
            if(isset($_SESSION['step']) && $_SESSION['step'] <= $this_step){
                $_SESSION['step'] = $this_step;
            }
            if(!isset($_SESSION['step']))
                $_SESSION['step'] = 1;                
            return $this_step;             
        }
        
        private function bddConnect(){
            global $global;

            $db = mysql_connect($global['db_host'], $global['db_user'], $global['db_pass'])
                or die ('<div style="text-align: center;">Error ! Database connexion failed<br />Check your user\'s name/password</div>');
            $connect= mysql_select_db($global['db_name'], $db)
                or die ('<div style="text-align: center;">Error ! Database connexion failed<br />Check your database\'s name</div>');
        }
        
        private function requirements(){
            $array_requirements = array();
            $array_requirements['_PHPVERSION'] = version_compare(phpversion() > 0, '5.1') ? 1 : 3;
            $array_requirements['_MYSQLEXT'] = extension_loaded('mysql') > 0 ? 1 : 3;
            $array_requirements['_SESSIONSEXT'] = extension_loaded('session') > 0 ? 1 : 3;
            $array_requirements['_ZIPEXT'] = extension_loaded('zip') > 0 ? 1 : 3;
            $array_requirements['_FILEINFOEXT'] = extension_loaded('fileinfo') > 0 ? 1 : 2;
            $array_requirements['_HASHEXT'] = function_exists('hash') > 0 ? 1 : 3;
            $array_requirements['_GDEXT'] = extension_loaded('gd') > 0 ? 1 : 3;
            $array_requirements['_TESTCHMOD'] = is_writable(dirname(__FILE__)) > 0 ? 1 : 3;
            return $array_requirements;
        }
        
        private function createBackupBdd(){
            global $global;
            
            $this->bddConnect();
            $array_sqlTables = array();             
            $result = mysql_query('SHOW TABLES');
            while($row = mysql_fetch_row($result)){
              $array_sqlTables[] = $row[0];
            }
            $return = "#------------------------------------------\n"
                                ."# Save of database for Nuked-Klan\n"
                                ."# Database: ".$global['db_name']."\n"
                                ."# Date: ".strftime("%c")."\n"
                                ."#------------------------------------------\n\n";
            foreach($array_sqlTables as $table){
                $result = mysql_query('SELECT * FROM '.$table);
                $num_fields = mysql_num_fields($result);            
                $return.= 'DROP TABLE IF EXISTS '.$table.';';
                $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
                $return.= "\n\n".$row2[1].";\n\n";
                $return.= "#------------------------------------------\n"
                                    ."# Data inserts for ".$table."\n"
                                    ."#------------------------------------------\n\n";
                for ($i = 0; $i < $num_fields; $i++) {
                    while($row = mysql_fetch_row($result)){
                        $return.= 'INSERT INTO '.$table.' VALUES(';
                        for($j=0; $j<$num_fields; $j++){
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = preg_replace("#\\n#","\\n",$row[$j]);
                            if (isset($row[$j]))
                                $return.= '"'.$row[$j].'"' ;
                            else
                                $return.= '""';
                            if ($j<($num_fields-1))
                                $return.= ',';
                        }
                        $return.= ");\n";
                    }
                }  
                $return.="\n\n\n";
            }
            $this->db_save_name = $global['db_name'].'_'.time().'.sql';
            $handle = fopen($this->db_save_name, 'w+');
            fwrite($handle,$return);
            fclose($handle);
            return  true;
        }        
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Methodes d'affichage, appelées pour effectuer la mise en page
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        static function viewTop(){
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
                        <head>
                            <title>Installation de Nuked-klan</title>
                            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                            <meta http-equiv="content-style-type" content="text/css" />
                            <link rel="stylesheet" href="modules/Admin/css/reset.css" type="text/css" media="screen" />
                            <link rel="stylesheet" href="modules/Admin/css/style.css" type="text/css" media="screen" />
                            <link rel="stylesheet" href="modules/Admin/css/invalid.css" type="text/css" media="screen" />
                            <script type="text/javascript" src="modules/Admin/scripts/jquery-1.6.1.min.js"></script>
                            <script type="text/javascript" src="modules/Admin/scripts/simpla.jquery.configuration.js"></script>
                            <script type="text/javascript" src="modules/Admin/scripts/facebox.js"></script>
                        </head>
                        <body>
                                <div id="sidebar" style="float:left;position:inherit;">
                                    <div id="sidebar-wrapper">
                                        <a href="http://www.nuked-klan.org">
                                            <img id="logo" src="modules/Admin/images/logo.png" alt="Simpla Admin logo" />
                                        </a>';
        }
        
        private function viewMenu($step){
            echo '<ul id="main-nav">';
            $nb_step = isset($_SESSION['step']) ? $_SESSION['step'] : 1;
            for($i = 1; $i <= $nb_step; $i++){               
                echo '<li><a href="#" class="nav-top-item no-submenu ';                            
                if($i == $step)
                    echo 'current';                                
                echo '">Etape '.$i.'</a>';
                $nb_liens = count($this->array_lien_step[$i]);
                if($nb_liens > 0){
                    echo '<ul>';
                    foreach($this->array_lien_step[$i] as $k => $v){
                        echo '<li><a href="'.$v.'" ';
                        if($i == $step)
                            echo 'class="current" ';
                        echo '>'.constant($k).'</a></li>';
                    }
                    echo '</ul>';
                } 
                echo '</li>';
            }
            echo '</ul></div></div><div style="float:left;width:70%;" >';
        }
        
        static function viewInfos($step){
            if($step > 4) $step = $step - 4; // A modifier en cas d'ajout d'infos
            echo '<hr style="margin-top:30px;margin-bottom:40px;width:80%;" />
                    <div style="width:560px;height:263px;overflow:hidden;margin:auto;">';
                    
                        switch($step){
                            case'1':
                            $a = _DECOUVERTE;
                            $b = _DECOUVERTE1;
                            break;
                            case'2':
                            $a = _NEWSADMIN;
                            $b = _NEWSADMIN1;
                            break;
                            case'3':
                            $a = _PROCHE;
                            $b = _PROCHE1;
                            break;
                            case'4':
                            $a = _SIMPLIFIE;
                            $b = _SIMPLIFIE1;
                            break;
                        }                        
                        echo '<div id="slide'.$step.'" style="display:block;width:560px;height:263px;">
                                    <h2>'.$a.'</h2>
                                    <p>
                                        <img src="img/img_slide_0'.$step.'.jpg" alt="" style=" float:right;" width="215" height="145" />
                                        '.$b.'
                                    </p>
                                </div>                        
                    </div>';
        }
        
        static function viewBottom(){
            echo '</div></body></html>';
        }
               
        static function redirect($url, $tps){
            $temps = $tps * 1000;        
            echo '<script type="text/javascript">function redirect(){window.location=\'' . $url . '\'}setTimeout(\'redirect()\',\'' . $temps .'\');</script>';
        }
        
        private function routeUser(){
           $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
           
            switch($action){
                case'checkLang':
                $this->checkLang();
                break;
                case'setLang':
                $this->setLang();
                break;
                case'checkVersion':
                $this->checkVersion();
                break;
                case'setVersion':
                $this->setVersion();
                break;
                case'checkCompatibility':
                $this->checkCompatibility();
                break;
                case'checkStats':
                $this->checkStats();
                break;
                case'setStats':
                $this->setStats();
                break;
                case'checkSave':
                $this->checkSave();
                break;
                case'makeSave':
                $this->makeSave();
                break;
                case'checkTypeUpdate':
                $this->checkTypeUpdate();
                break;
                default:
                $this->checkLang();
                break;
            }
        }
    }
        
    $update = new update();
?>