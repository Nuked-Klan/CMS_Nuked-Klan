<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo substr($language, 0, 2) ?>">
    <head>
        <title>
<?php
    if (isset($session['process']) && $session['process'] == 'update')
        printf($i18n['UPDATE_TITLE'], $processVersion);
    else
        printf($i18n['INSTALL_TITLE'], $processVersion);
?>
        </title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="stylesheet" href="media/css/style.css" type="text/css" media="screen" />
        <script type="text/javascript" src="media/js/jquery-1.7-min.js" ></script>
        <script type="text/javascript" src="index.php?action=printJsI18nFile" ></script>
<?php
    if (in_array($action, array('setConfig', 'setUserAdmin', 'runProcess'))) :
?>
        <script type="text/javascript" src="media/js/<?php echo $action ?>.js" ></script>
<?php
    endif
?>
    </head>
    <body>
        <div id="content" class="greyscale">
            <div id="sidebar" >
                <a href="http://www.nuked-klan.org">
                    <img id="logo" src="../modules/Admin/images/logo.png" alt="Nuked-Klan" />
                </a>
                <div id="navigation" >
<?php
    $i = 0;

    foreach ($session as $k => $v) :
        $a = isset($navigation[$k]) ? $i18n[$navigation[$k]] : null;

        if ($a !== null) :
            if ($i > 0) :
?>
                    <hr style="margin:0 auto;width:80%;" />
<?php
            endif
?>
                    <p style="margin:5px auto;">
                        <span class="link_nav"><?php echo $a ?></span><br/>
<?php
            if ($k == 'assist') :
?>
                        <span><?php echo ($v == 'yes') ? $i18n['ASSIST'] : $i18n['QUICK'] ?></span>
<?php
            else :
?>
                        <span><?php echo $i18n[strtoupper($session[$k])] ?></span>
<?php
            endif
?>
                    </p>
<?php
            $i++;
        endif;
    endforeach ?>
                <a href="index.php?action=resetSession" id="reset" class="button" ><?php echo $i18n['RESET_SESSION'] ?></a>
                </div>
            </div>
<?php echo $content ?>
        <hr style="margin-top:30px;margin-bottom:15px;width:90%;" />
            <div style="width:580px;overflow:hidden;margin:auto;">
                <div id="slide<?php echo $info['n'] ?>" style="display:block;width:580px;">
                    <h2><?php echo $i18n[$info['name']] ?></h2>
                    <p>
                        <img src="media/images/img_slide_0<?php echo $info['n'] ?>.png" alt="" style="float:right;" width="269" height="175" />
                        <?php echo $i18n[$info['name'] .'_DESCR'] ?>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>