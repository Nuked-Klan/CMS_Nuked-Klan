<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
        <title>
<?php
    if (isset($this->data['process']) && $this->data['process'] == 'update')
        printf($i18n['UPDATE_TITLE'], $processVersion);
    else
        printf($i18n['INSTALL_TITLE'], $processVersion);
?>
        </title>
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
                </a>
                <div id="navigation" >
<?php
    if (isset($data)) :
        $i = 0;

        foreach ($data as $k => $v) :
            $a = isset($navigation[$k]) ? $i18n[$navigation[$k]] : null;

            if ($a !== null) :
                if ($i > 0) :
?>
                    <hr style="margin:0 auto;width:80%;" />
<?php
                endif
?>
                    <p style="margin:5px auto;"><span class="link_nav"><?php echo $a ?></span><br/><span><?php echo $i18n[strtoupper($data[$k])] ?></span></p>
<?php
                $i++;
            endif;
        endforeach;

        if ($action != 'checkInstallSuccess') : ?>
                <a href="index.php?action=resetSession" id="reset" class="button" ><?php echo $i18n['RESET_SESSION'] ?></a>
<?php
        endif;
    endif ?>
                </div>
            </div>
<?php echo $content ?>
        <hr style="margin-top:30px;margin-bottom:15px;width:90%;" />
            <div style="width:580px;overflow:hidden;margin:auto;">
                <div id="slide<?php echo $info['n'] ?>" style="display:block;width:580px;">
                    <h2><?php echo $i18n[$info['name']] ?></h2>
                    <p>
                        <img src="images/img_slide_0<?php echo $info['n'] ?>.png" alt="" style="float:right;" width="200" height="194" />
                        <?php echo $i18n[$info['name'] .'_DESCR'] ?>
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>