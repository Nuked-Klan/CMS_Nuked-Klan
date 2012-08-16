<?php

    class install{
        private $data;
        private $array_lang = array('_ENGLISH' => 'english','_FRENCH' => 'french');
        
        function __construct(){
            $this->initSession();
            $this->importLang();
            $this->routeUser();            
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Méthodes du core, elles permettent l'affichage des pages
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        private function initSession(){       
            session_start();
            if(isset($_SESSION['active']) && $_SESSION['active'] === true){
                foreach($_SESSION as $k => $v){
                    $this->data[$k] = $v;
                }
            }
            $_SESSION['active'] = true;
        }
        
        private function checkLang(){
            echo '<div style="text-align: center;margin:30px auto;">
                        <h2>'._SELECTLANG.' : </h2>
                        <form id="form_lang" name="form_lang" method="post" action="index.php?action=setLang" >
                            <select id="lang_install" name="lang_install" onChange="renameButton();">';
            if(!isset($this->data['lang_install'])){
                $lang_selected = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'fr' ? 'french' : 'english';
            }
            else{
                $lang_selected = $this->data['lang_install'];
            }
            foreach($this->array_lang as $k => $v){
                echo '<option value="'.$v.'" ';
                    if($lang_selected == $v){
                        echo 'selected="selected" ';
                    }
                echo '>'.constant($k).'</option>';
            }
            echo '         </select><br/><br/>
                            <a href="#" style="display:inline-block;margin-top:30px;" class="button" id="button" onclick="document.forms[\'form_lang\'].submit();" >'._SUBMIT.'</a>
                        </form>
                    </div>
                    <script type="text/javascript" >
                        function renameButton(){
                            choix = $("#lang_install").val();
                            if(choix == "french"){ $("#button").html("Valider");}
                            else{$("#button").html("Submit");}
                        }
                    </script>';
        }
        
        private function setLang(){
            if(isset($_REQUEST['lang_install']) && in_array($_REQUEST['lang_install'], $this->array_lang)){
                $_SESSION['lang_install'] = $_REQUEST['lang_install'];
            }
            self::redirect('index.php?action=main', 0);
        }
        
        private function main(){
            if(isset($_REQUEST['type'])){
                if($_REQUEST['type'] == 'install' || $_REQUEST['type'] == 'update'){
                    $_SESSION['type'] = $this->data['type'] = $_REQUEST['type'];
                    self::redirect('index.php?action=checkCompatibility', 0);
                }
            }
            global $global,$dbprefix;
            echo '<div style="text-align: center;margin:30px auto;">
                        <h2>'._WELCOMEINSTALL.'</h2>
                        <p>'._GUIDEINSTALL.'</p>';
            if(is_file('../conf.inc.php')){
                define('INDEX_CHECK', '');
                if(is_file('../Includes/version.php')){
                    include('../Includes/version.php');
                    $version = $nk_version;
                }
                else{
                    include('../conf.inc.php');
                    $this->bddConnect($global['db_host'], $global['db_user'], $global['db_pass'], $global['db_name']);
                    $sql_version = mysql_query ('SELECT value FROM `'.$db_prefix.'_config` WHERE name=\'version\' ') or die (mysql_error());
                    list($version) = mysql_fetch_array($sql_version);
                }
               
                $_SESSION['version'] = $this->checkVersion($version);               
                echo '<h3 style="background:#ECEADB;width:60%;padding:5px;border:1px solid #ddd;margin:20px auto;" >
                            '._DETECTUPDATE.' '.$_SESSION['version']['print'].' '._DETECTUPDATEEND.'
                        </h3>';
                if($this->validVersion($_SESSION['version'])){
                    echo '<a href="index.php?action=main&amp;type=update" class="button" >'._STARTUPDATE.'</a>';
                }
                else{
                    echo '<p>'._BADVERSION.'</p>';
                }
                echo '</div>';
            }
            else{
                echo '<a href="index.php?action=main&amp;type=install" class="button" >'._STARTINSTALL.'</a>
                        </div>';
            }
        }
        
        private function checkCompatibility(){
            echo '<div style="text-align: center;margin:30px auto;">
                        <h3 style="margin-bottom:5px;" >'. _CHECKCOMPATIBILITYHOSTING .'</h3>
                        <table style="width:500px;margin:15px auto;border:1px solid #ddd;text-align:left;background:#fff;" cellpadding="3">
                            <tr>
                                <td style="width:80%;"><b>'._COMPOSANT.'</b></td>
                                <td style="width:20%;text-align:center;"><b>'._COMPATIBILITY.'</b></td>';
            $array_requirements = $this->requirements();
            $i=0;
            foreach($array_requirements as $k => $v){
                $src = $v == 1 ? 'images/ok.png' : 'images/nook.png';
                $src = $v == 2 ? 'images/warning.png' : $src;
                echo '<tr';
                if($i==0){
                    echo ' style="background:#e9e9e9;" ';
                    $i++;
                }
                else{
                    echo ' style="background:#f5f5f5;" ';
                    $i=0;
                }
                echo '>
                                <td>'.constant($k).'</td>
                                <td style="text-align:center;"><img src='.$src.' alt="" />
                            </tr>';
                if($v == 3 || $v == 2){
                    $class_error = $v == 2 ? 'warning' : 'error';
                    echo '<tr>
                                <td colspan="2" class="'.$class_error.'_compatibility" >'.constant($k.'ERROR').'</td>
                            </tr>';
                }
            }
            echo '</table>';            
            $compatibility = (in_array(3, $array_requirements) || in_array(3, $array_requirements)) ? false : true;
            if($compatibility === true){                
                echo '<a href="index.php?action=checkStats" class="button" >'._CONTINUE.'</a>
                    </div>';
            }
            else{
                echo '<p>'._BADHOSTING.'</p>
                        <a href="index.php?action=checkStats" class="button" >'._FORCE.'</a>
                        </div>';
            }
        }
        
        private function checkStats(){
            $checked = isset($this->data['stats']) && $this->data['stats'] === false ? '' : 'checked="checked" ';
            echo '<div style="text-align: center;margin:30px auto;">
                        <h3 style="margin-bottom:30px;" >'._SELECTSTATS.'</h3>
                        '._TXTSTATS.'
                        <form action="index.php?action=setStats" method="post" id="form_stats" >
                            <label><input type="checkbox" name="conf_stats" '.$checked.' style="margin-top:20px;" />&nbsp; '._CONFIRMSTATS.'</label>
                            <br/><a href="#" style="margin-top:20px;" class="button" onclick="document.forms[\'form_stats\'].submit();" >'._CONFIRM.'</a>
                        </form>
                    </div>';
        }
        
        private function setStats(){
            if(isset($_REQUEST['conf_stats'])){                
                $_SESSION['stats'] = $_REQUEST['conf_stats'] == 'on' ? 'yes' : 'no';                
            }
            else{
                $_SESSION['stats'] = 'no';
            }
            if($this->data['type'] == 'update'){
                self::redirect('index.php?action=checkSave', 0);
            }
            else{
                self::redirect('index.php?action=checkTypeInstall', 0);
            }
        }
        
        private function checkSave(){
            echo '<div style="text-align: center;margin:30px auto;">
                        <h3 style="margin-bottom:30px;" >'._SELECTSAVE.'</h3>
                        <a href="index.php?action=makeSave" style="margin-top:20px;" class="button" >'._TOSAVE.'</a>
                        <a href="index.php?action=checkTypeInstall" class="button" >'._NOTHANKS.'</a>
                    </div>';
            $_SESSION['db_save'] = 'no';
        }
        
        private function makeSave(){
                $_SESSION['db_save'] = 'yes';
                echo '<div style="text-align: center;margin:30px auto;">
                        <h3 style="margin-bottom:30px;" >'._DBSAVED.'</h3>
                        <p>'._DBSAVEDTXT.'</p>
                        <p>
                            <a href="index.php?action=createBackupBdd" target="_blank">'._SAVE.'</a>
                        </p>
                        <p>
                            <a href="index.php?action=checkTypeInstall" class="button" >'._CONTINUE.'</a>
                        </p>
                    </div>';
        }
        
        private function checkTypeInstall(){
            if(isset($_REQUEST['assist'])){
                if($_REQUEST['assist'] == $this->data['type'].'assist'){
                    $_SESSION['assist'] = $_REQUEST['assist'];
                    self::redirect('index.php?action=setConfigAssistant', 0);
                }
                elseif($_REQUEST['assist'] == $this->data['type'].'speed'){
                    $_SESSION['assist'] = $_REQUEST['assist'];
                    self::redirect('index.php?action=setConfig', 0);
                }
            }
            if($this->data['type'] == 'install'){
                $speed = _INSTALLSPEED;
                $assist = _INSTALLASSIST;
            }
            elseif($this->data['type'] == 'update'){
                $speed = _UPDATESPEED;
                $assist = _UPDATEASSIST;
            }
            echo '<div style="text-align: center;margin:30px auto;">
                        <h3 style="margin-bottom:30px;" >'. _CHECKTYPEINSTALL .'</h3>
                            <a href="index.php?action=checkTypeInstall&amp;assist='.$this->data['type'].'speed" class="button" >'.$speed.'</a>
                            <a href="index.php?action=checkTypeInstall&amp;assist='.$this->data['type'].'assist" class="button" >'.$assist.'</a> 
                    </div>';
        }
        
        private function setConfig(){
            if($this->data['type'] == 'update'){
                $type = _UPDATESPEED;
                include('../conf.inc.php');
                $host = $global['db_host'];
                $user = $global['db_user'];
                $pass = '';
                $name = $global['db_name'];
                $prefix = $db_prefix;
            }
            elseif($this->data['type'] == 'install'){
                $type = _INSTALLSPEED;
                $host = $user = $name = $pass = '';
                $prefix = 'nuked';
            }
            echo '<div style="text-align: center;margin:30px auto;">
                        <h2>'.$type.'</h2>
                        <form method="post" action="index.php?action=installDB" id="form_config">
                            <h4>' . _CONFIG . '</h4>
                            <div id="config" >';
                            $array_fields = array('host', 'user', 'pass', 'prefix', 'name');
                            foreach($array_fields as $k){
                                echo '<label>
                                            <strong>'.constant('_DB'.strtoupper($k)).'</strong>
                                            <input type="';
                                echo $k == 'pass' ? 'password" autocomplete="off"' : 'text"';
                                if($this->data['type'] == 'update' && $k != 'pass'){
                                    echo ' disabled="disabled"';
                                }
                                echo ' name="db_'.$k.'" id="form_bdd_'.$k.'" value="'.${$k}.'" onblur="checkInputBDD($(this));" />
                                            </label>';
                            }
            echo '</div>
                            <div id="infos" style="text-align: center;margin:30px auto;color:#FF4040;"></div>
                            <div style="text-align: center;">
                                <a href="#" id="submit" class="button" onclick="verifFormBDD(\''.$this->data['type'].'\', \'form_config\', \''.addslashes(_WAIT).'\', \''.addslashes(_ERROR_HOST).'\', \''.addslashes(_ERROR_USER).'\', \''.addslashes(_ERROR_DB).'\', \''.addslashes(_ERROR_PREFIX).'\');"  >' . _SUBMIT . '</a>
                                <a href="index.php?action=checkTypeInstall" class="button" >' . _BACK . '</a>
                            </div>
                        </form>
                    </div>';
        }
        
        private function setConfigAssistant(){
            $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : null;
            if($page == 'set'){
                if($this->data['type'] == 'update'){
                    $type = _UPDATEASSIST;
                    include('../conf.inc.php');
                    $host = $global['db_host'];
                    $user = $global['db_user'];
                    $pass = '';
                    $name = $global['db_name'];
                    $prefix = $db_prefix;
                }
                elseif($this->data['type'] == 'install'){
                    $type = _INSTALLASSIST;
                    $host = $user = $name = $pass = '';
                    $prefix = 'nuked';
                }
                echo '<div style="text-align: center;">
                        <h2>'.$type.'</h2>
                        <form method="post" action="index.php?action=installDB" id="form_config">
                            <h4>' . _CONFIG . '</h4>
                            <div id="config" >';
                            $array_fields = array('host', 'user', 'pass', 'prefix', 'name');
                            foreach($array_fields as $k){
                                echo '<label>
                                            <strong>'.constant('_DB'.strtoupper($k)).'</strong>
                                            <input type="';
                                echo $k == 'pass' ? 'password" autocomplete="off"' : 'text"';
                                if($this->data['type'] == 'update' && $k != 'pass'){
                                    echo ' disabled="disabled"';
                                }
                                echo ' name="db_'.$k.'" id="form_bdd_'.$k.'"  value="'.${$k}.'" onblur="checkInputBDD($(this));" />
                                            </label>
                                            <p><img src="images/info.png" style="float:left;margin-right:5px;" />'.constant('_INSTALLDB'.strtoupper($k)).'</p>';
                            }
                echo '</div>
                            <div id="infos" style="text-align: center;margin:30px auto;color:#FF4040;"></div>
                            <div style="text-align: center;">
                                <a href="#" id="submit" class="button" onclick="verifFormBDD(\''.$this->data['type'].'\', \'form_config\', \''.addslashes(_WAIT).'\', \''.addslashes(_ERROR_HOST).'\', \''.addslashes(_ERROR_USER).'\', \''.addslashes(_ERROR_DB).'\', \''.addslashes(_ERROR_PREFIX).'\');"  >' . _SUBMIT . '</a>';
                if($page == 'set'){
                    echo '<a href="index.php?action=checkTypeInstall" class="button" >' . _BACK . '</a>';
                }
                echo '</div>
                        </form>
                    </div>';
            }
            else{
                echo '<div style="text-align:center;">
                                <img src="images/nk.png"/>
                                <h2><b>'. _NEWNK179.'</b></h2>
                            </div>
                            <div style="width:90%;margin: 20px auto;">';                            
                $array_infos = array('_SECURITE', '_OPTIMISATION', '_ADMINISTRATION', '_BANTEMP', '_SHOUTBOX', '_ERRORSQL', '_MULTIWARS', '_COMSYS', '_EDITWYS', '_CONT', '_ERREURPASS', '_DIFFMODIF');
                foreach($array_infos as $k){
                    echo '<p>
                                    <b>'.constant($k).':</b>
                                    <br />
                                    '.constant($k.'1').'
                                    <br />
                                </p>';
                }                
                echo '</div>
                            <div style="text-align: center;">
                                <a href="index.php?action=setConfigAssistant&page=set" class="button" >' . _CONTINUE . '</a>
                            </div>';
            }
        }
        
        private function installDB(){
            if($this->data['type'] == 'install'){
                $_SESSION['host'] = $_REQUEST['db_host'];
                $_SESSION['user'] = $_REQUEST['db_user'];
                $_SESSION['pass'] = $_REQUEST['db_pass'];
                $_SESSION['db_name'] = $_REQUEST['db_name'];
                $db_prefix = $_SESSION['db_prefix'] = $_REQUEST['db_prefix'];
                $array_text = array( _LOGITXTSUCCESS);
                $error = _LOGITXTERROR;
                $complete = _LOGITXTENDSUCCESS;
                $complete_error_start = _LOGITXTENDERRORSTART;
                $complete_error_end = _LOGITXTENDERROREND;
            }
            elseif($this->data['type'] == 'update'){
                unset($_SESSION['hash']);
                include('../conf.inc.php');
                $_SESSION['host'] = $global['db_host'];
                $_SESSION['user'] = $global['db_user'];
                $_SESSION['pass'] = $_REQUEST['db_pass'];
                $_SESSION['db_name'] = $global['db_name'];
                $_SESSION['db_prefix'] = $db_prefix;
                $array_text = array( _LOGUTXTSUCCESS, _LOGUTXTUPDATE, _LOGUTXTUPDATE2, _LOGUTXTREMOVE, _LOGUTXTREMOVE2);
                $error = _LOGUTXTERROR;
                $complete = _LOGUTXTENDSUCCESS;
                $complete_error_start = _LOGUTXTENDERRORSTART;
                $complete_error_end = _LOGUTXTENDERROREND;
            }
            echo '<div style="text-align: center;"><h2>';
            echo $this->data['type'] == 'install' ? _CREATEDB : _UPDATEDB;
            echo '</h2>
                    <div id="log_install" >'._WAITING.'</div>
                    <div id="progress" class="progress-bar-bg">
                    <span class="progress-bar" ></span></div>
                    <script type="text/javascript">
                    var array_text = new Array(\''.implode("','", $array_text).'\');
                        function start_'.$this->data['type'].'(){
                            if(busy == false){
                                busy = true;
                                $("#log_install").text("'.constant('_STARTING'.strtoupper($this->data['type'])).'");
                                $("#log_install").append("<img src=\"images/loading.gif\" alt=\"\" id=\"loading_img\" />");
                                $("#continue_install").removeClass("button");
                                $("#continue_install").addClass("button_disabled");
                                queue_install("'.$this->data['type'].'");
                            }
                        }
                        function ajaxTable(table){
                            $.ajax({
                                async: true,
                                type: "POST",
                                url: "index.php?action=creatingDB",
                                data: "table="+table+"&db_prefix='.$db_prefix.'"
                            }).done(function(txt) {
                                if(txt == "OK"){
                                    writeInfo("'.$this->data['type'].'", "'.$db_prefix.'", table, array_text, "OK");
                                    $(".progress-bar").css("width", progress_'.$this->data['type'].'*i+"%");
                                    i++;
                                }
                                else{
                                    writeInfo("'.$this->data['type'].'", "'.$db_prefix.'", table, "'.$error.'", "NO");errors++;
                                    writeError("'._PRINTERROR.'", txt);
                                }                                  
                                $("#log_install").scrollTop(1000);
                                ajaxBusy = false;
                            });
                        }
                        function viewEnd(){
                            if(errors == 0){
                                txt_end = "'.$complete.'";
                                $("#continue_install").text("' . _CONTINUE . '");
                                install = true;
                            }
                            else{
                                txt_end = "'.$complete_error_start.'"+errors+"'.$complete_error_end.'";
                                $("#continue_install").text("' . _RETRY . '");
                            }
                            writeComplete(txt_end);
                            $("#continue_install").removeClass("button_disabled");
                            $("#continue_install").addClass("button");
                        }
                    </script>';
            echo '<a href="#" class="button" id="continue_install" onclick="submit(\''.$this->data['type'].'\')" >' . _START . '</a>
                    </div>';
        }
                
        private function creatingDB(){
            $table = $_REQUEST['table'];
            $db_prefix = $_REQUEST['db_prefix'];
            if($this->bddConnect($this->data['host'], $this->data['user'], $this->data['pass'], $this->data['db_name']) == 'OK'){
                if($this->data['type'] == 'install'){
                    include('install.inc');
                }
                elseif($this->data['type'] == 'update'){
                    include('update.inc');
                }
            }
            else{
                echo 'pass = '.$this->data['pass'].'<br/>';
                echo $this->bddConnect($this->data['host'], $this->data['user'], $this->data['pass'], $this->data['db_name']);
            }
        }
        
        private function checkUserAdmin(){
            $_SESSION['user_admin'] = 'INPROGRESS';
            if(isset($_REQUEST['send'])){
                if (!isset($_REQUEST['pseudo']) || !isset($_REQUEST['pass']) || !isset($_REQUEST['pass2']) || !isset($_REQUEST['mail'])
                    || strlen($_REQUEST['pseudo']) < 3 || $_REQUEST['pass'] != $_REQUEST['pass2'] || preg_match("`[\$\^\(\)'\"?%#<>,;:]`", $_REQUEST['pseudo'])
                    || !preg_match("/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/", $_REQUEST['mail']) ){
                    echo '<div style="text-align: center;margin:30px auto;">
                            <h2>'._CHECKUSERADMIN.'</h2>
                            <p>'._ERRORFIELDS.'</p>
                            <a href="index.php?action=checkUserAdmin" class="button" >' . _BACK . '</a>
                            </div>';
                }
                else{
                    if($this->bddConnect($this->data['host'], $this->data['user'], $this->data['pass'], $this->data['db_name']) == 'OK'){
                        include('user.inc');
                        $save_config = saveConfig('install');
                        if($save_config == 1){
                            $_SESSION['user_admin'] = 'FINISH';
                            self::redirect("index.php?action=checkInstallSuccess", 0);
                        }
                        else{
                            self::redirect("index.php?action=checkInstallFailure&error=".$save_config, 0);
                        }
                    }
                }
            }
            else{
                echo '<div style="text-align: center;margin:30px auto;">
                        <h2>'._CHECKUSERADMIN.'</h2>';
            }
            if(!isset($_REQUEST['send'])){
                echo '<form method="post" action="index.php?action=checkUserAdmin" id="form_user_admin">
                                <div id="config" >';
                                $array_fields = array('pseudo', 'pass', 'pass2', 'mail');
                                foreach($array_fields as $k){
                                    echo '<label>
                                                <strong>'.constant('_'.strtoupper($k)).'</strong>
                                                <input type="';
                                    echo (($k == 'pass') || ($k == 'pass2')) ? 'password' : 'text';
                                    echo '" name="'.$k.'" value="" onblur="checkInputAdmin($(this));" />
                                                </label>';
                                }
                echo '</div>
                                <input type="hidden" name="send" value="ok" />
                                <div id="infos" style="text-align: center;margin:30px auto;color:#FF4040;"></div>
                                <div style="text-align: center;margin:30px auto;">
                                    <a href="#" class="button" onclick="verifFormAdmin(\'form_user_admin\',  \''.addslashes(_WAIT).'\', \''.addslashes(_ERROR_PSEUDO).'\', \''.addslashes(_ERROR_PASS).'\', \''.addslashes(_ERROR_PASS2).'\',  \''.addslashes(_ERROR_MAIL).'\');"  >' . _SUBMIT . '</a>
                                </div>
                            </form>
                        </div>';
            }
        }
        
        private function updateConfig(){
            include('user.inc');
            $save_config = saveConfig('update');
            if($save_config == 1){
                self::redirect("index.php?action=checkInstallSuccess", 0);
            }
            else{
                self::redirect("index.php?action=checkInstallFailure&error=".$save_config, 0);
            }
        }
        
        private function checkInstallFailure(){
            $_SESSION['user_admin'] = 'FINISH';
            $error = isset($_REQUEST['error']) ? $_REQUEST['error'] : '';
            echo '<div style="text-align: center;margin:30px auto;">
                        <h2>'._ERROR.'</h2>
                        <p>'.constant('_'.$error).'</p>';
            if($error == 'CONF.INC' || $error == 'COPY'){                
                echo '<div id="log_install">';
                if(isset($_SESSION['content_web'])){
                    echo $_SESSION['content_web'];
                    echo '</div><p>'.constant('_'.$error.'2').'</p>';
                    
                }
                else{
                    echo _ERRORGENERATECONFINC;
                    echo '</div>';
                }                
            }
            if(isset($_SESSION['content_web']) && $error != 'CHMOD'){
                echo '<a href="index.php?action=printConfig" class="button" >'._DOWNLOAD.'</a>&nbsp;';
            }
            else{
                echo '<a href="index.php?action=checkUserAdmin" class="button" >'._BACK.'</a>&nbsp;';
            }
            if(isset($_SESSION['content_web'])){
                echo '<a href="index.php?action=checkInstallSuccess" class="button" >' . _CONTINUE . '</a>
                        </div>';
            }
        }
        
        private function checkInstallSuccess(){
            echo '<div style="text-align: center;margin:30px auto;">
                        <h2>'._INSTALLSUCCESS.'</h2>
                        <p>'._INFOPARTNERS.'</p>
                        <div id="partners" ><img src="images/loading.gif" alt="" /><br/>'._WAIT.'</div>';
            echo '<script type="text/javascript" >
                            function ajaxPartners(){
                                $.ajax({
                                    async: true,
                                    type: "POST",
                                    url: "index.php?action=getPartners",
                                }).done(function(txt) {
                                    $("#partners").css("display", "none");
                                    $("#partners").html(txt);
                                    $("#partners").fadeIn("slow");
                                });
                            }
                            $(document).ready(ajaxPartners());
                        </script>';
                        
            echo '<a href="index.php?action=deleteSession" class="button" >' . _ACCESS_SITE . '</a>
                    </div>';
        }
        
        private function getPartners(){
            $content = @file_get_contents('http://www.nuked-klan.org/extra/partners.php?key=iS5scBmNTNyE6M07Jna3');
            $content = @unserialize($content);
            $content = !is_array($content) ? array() : $content;
            $i = 0;
            foreach($content as $k => $v){
                echo '<a href="'.$v[2].'" ><img src="'.$v[1].'" alt="'.$v[0].'" /></a>';
                $i++;
            }
            if($i == 0){
                echo _NOPARTNERS;
            }
        }
        
        private function resetSession(){
            unset($_SESSION);
            session_destroy();
            self::redirect('index.php', 0);
        }

        private function deleteSession(){
            unset($_SESSION);
            session_destroy();
            self::redirect('../index.php', 0);
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Méthodes de services, appelées pour effectuer une tâche précise
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        private function importLang(){
            if(isset($this->data['lang_install']) && in_array($this->data['lang_install'], $this->array_lang)){
                include('lang/'.$this->data['lang_install'].'.lang.php');
            }
            else{
                $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'fr' ? 'french' : 'english';
                include('lang/'.$lang.'.lang.php');
            }
        }
        
        private function bddConnect($host, $user, $pass, $db_name){
            $db = @mysql_connect($host, $user, $pass);
             if(mysql_error() != ''){
                return mysql_error();
             }
            $connect = @mysql_select_db($db_name, $db);
             if(mysql_error() != ''){
                return mysql_error();
             }
            @mysql_query('SET NAMES "latin1"');
            return 'OK';
        }
        
        private function testBddConnect(){
            $connect = $this->bddConnect($_REQUEST['db_host'], $_REQUEST['db_user'], utf8_decode($_REQUEST['db_pass']), $_REQUEST['db_name']);
            if(preg_match('#Unknown MySQL server host#', $connect)){
                echo 'error_host';
            }
            else if(preg_match('#Access denied for user#', $connect)){
                echo 'error_login';
            }
            else if(preg_match('#Unknown database#', $connect)){
                echo 'error_db';
            }
            else if($connect == 'OK'){
                if(isset($_REQUEST['type'])){
                    if($_REQUEST['type'] == 'update'){
                        $result = mysql_query('SELECT name, value FROM '.$_REQUEST['db_prefix'].'_config');
                        if ($result == false) {
                            echo 'error_prefix';
                        } else {
                            echo 'OK';
                        }

                    }
                    else{
                        echo 'OK';
                    }
                }
                else{
                    echo 'OK';
                }
            }

            else{
                echo $connect;
            }
        }

        private function requirements(){
            $array_requirements = array();
            $array_requirements['_PHPVERSION'] = version_compare(phpversion() > 0, '5.1') ? 1 : 3;
            $array_requirements['_MYSQLEXT'] = extension_loaded('mysql') ? 1 : 3;
            $array_requirements['_SESSIONSEXT'] = extension_loaded('session') ? 1 : 3;
            $array_requirements['_ZIPEXT'] = extension_loaded('zip') ? 1 : 3;
            $array_requirements['_FILEINFOEXT'] = extension_loaded('fileinfo') ? 1 : 2;
            $array_requirements['_HASHEXT'] = function_exists('hash') ? 1 : 3;
            $array_requirements['_GDEXT'] = extension_loaded('gd') ? 1 : 3;
            $array_requirements['_TESTCHMOD'] = is_writable(dirname(dirname(__FILE__)).'/') ? 1 : 3;
            return $array_requirements;
        }
        
        private function createBackupBdd(){
            header("Content-disposition:filename=save".time().".sql"); 
            header("Content-type:application/octetstream");
            
            include('../conf.inc.php');
            $this->bddConnect($global['db_host'], $global['db_user'], $global['db_pass'], $global['db_name']);
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
            
            echo $return;
        }
        private function checkVersion($version){
            $array_version['print'] = $version;
            $version = strtoupper(str_replace(' ', '', $version));
            $array_version['RC'] = preg_match('/RC/', $version) ? substr(strstr($version, 'RC'), 2) : null;
            $version = preg_match('/RC/', $version) ? substr($version, 0, -(strlen($array_version['RC']) +2)) : $version;
            $tmp = explode('.', $version);
            $array_version['main'] = isset($tmp[0]) ? $tmp[0]: null;
            $array_version['sub'] = isset($tmp[1]) ? $tmp[1]: null;
            $array_version['rev'] = isset($tmp[2]) ? $tmp[2]: null;
            return $array_version;
        }
        
        private function validVersion($version){
            if($version['sub'] == '7' && ($version['rev'] == '7' || $version['rev'] == '8' || $version['rev'] == '9')){
                if(isset($version['RC'])){
                    if($version['RC'] == '5.3' || $version['RC'] == '6'){
                        return true;                        
                    }
                    else
                        return false;
                }
                return true;
            }
            else
                return false;
        }
        
        private function printConfig(){
            header("Content-disposition:filename=conf.inc.php"); 
            header("Content-type:application/octetstream");
            if(isset($_SESSION['content'])){
                echo $_SESSION['content'];
            }
        }
        
        private function showError($text){
            echo '<div id="error_div" >'.$text.'</div>';
        }
        
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Méthodes d'affichage, appelées pour effectuer la mise en page
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        static function viewTop(){
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
                        <head>
                            <title>Installation de Nuked-klan</title>
                            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
                            <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
                            <script type="text/javascript" src="jquery-1.7-min.js" ></script>
                            <script type="text/javascript" src="script.js" ></script>
                        </head>
                        <body>
                            <div id="content" class="greyscale">
                                <div id="sidebar" >
                                     <a href="http://www.nuked-klan.org">
                                         <img id="logo" src="../modules/Admin/images/logo.png" alt="Nuked-Klan" />
                                     </a>';
        }
        
        private function navigation(){
            echo '<div id="navigation" >';
            $array_menu = array('lang_install' => _SELECTLANG, 'type' => _SELECTTYPE, 'stats' => _SELECTSTATS, 'db_save' => _SELECTSAVE, 'assist' => _CHECKTYPEINSTALL, 'user_admin' => _CHECKUSERADMIN);
            if(isset($this->data)){
                $i = 0;
                foreach($this->data as $k => $v){
                    $a = isset($array_menu[$k]) ? $array_menu[$k] : null;
                    if($a !== null){
                        if($i > 0)  echo '<hr style="margin:0 auto;width:80%;" />';
                        echo '<p style="margin:5px auto;"><span class="link_nav">'.$a.'</span><br/><span>'.constant('_'.strtoupper($this->data[$k])).'<span></p>';
                        $i++;
                    }
                }
                if(isset($_REQUEST['action'])){
                    if($_REQUEST['action'] != 'checkInstallSuccess'){
                        echo '<a href="index.php?action=resetSession" id="reset" class="button" >' . _RESETSESSION . '</a>';
                    }
                }
            }
            echo '</div></div>';
        }
        
        static function viewBottom(){
            echo '</div></body></html>';
        }
        
        static function viewInfos(){
            $step = rand(1,4); // A modifier en cas d'ajout d'infos
            echo '<hr style="margin-top:30px;margin-bottom:15px;width:90%;" />
                    <div style="width:580px;overflow:hidden;margin:auto;">';                    
                        switch($step){
                            case'1':
                            $a = '_DISCOVERY';
                            break;
                            case'2':
                            $a = '_NEWSADMIN';
                            break;
                            case'3':
                            $a = '_INSTALL_AND_UPDATE';
                            break;
                            case'4':
                            $a = '_COMMUNAUTY_NK';
                            break;
                        }                        
                        echo '<div id="slide'.$step.'" style="display:block;width:580px;">
                                    <h2>'.constant($a).'</h2>
                                    <p>
                                        <img src="images/img_slide_0'.$step.'.png" alt="" style=" float:right;" width="200" height="194" />
                                        '.constant($a.'_DESCR').'
                                    </p>
                                </div>                        
                    </div>';
        }
        
        static function redirect($url, $tps){
            $temps = $tps * 1000;
            echo '<script type="text/javascript">function redirect(){window.location=\'' . $url . '\'}setTimeout(\'redirect()\',\'' . $temps .'\');</script>';
        }
        
        private function routeUser(){
            $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
            $array_page_nude = array('creatingDB', 'printConfig', 'testBddConnect', 'getPartners', 'createBackupBdd');
            
            if(in_array($action, $array_page_nude)){
                $this->{$action}();
            }
            else{ 
                self::viewTop();
                $this->navigation();          
                if(method_exists($this, $action) && (isset($_SESSION['lang_install']) || isset($_REQUEST['lang_install']))){
                    $this->{$action}();
                }
                else{
                    $this->checkLang();
                }
                self::viewInfos();
                self::viewBottom();
            }
        }
    }
    
    $install = new install();
?>