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
        <script type="text/javascript" src="media/js/informationSlide.js" ></script>
<?php
    if (is_file('media/js/'. $action .'.js')) :
?>
        <script type="text/javascript" src="media/js/<?php echo $action ?>.js" ></script>
<?php
    endif
?>
    </head>
    <body>
        <div id="page">
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
                    <hr />
<?php
            endif
?>
                    <p>
                        <span class="stepTitle"><?php echo $a ?></span><br/>
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
                    <a href="index.php?action=resetSession" id="reset"><?php echo $i18n['RESET_SESSION'] ?></a>
                </div>
            </div>
            <div id="<?php echo $action ?>" class="content">
<?php echo $content ?>

            </div>
            <hr />
            <div id="information">
<?php
    foreach($infoList as $n => $info) :
        $n++;
?>
                <div id="slide<?php echo $n ?>">
                    <h1><?php echo $i18n[$info] ?></h1>
                    <p>
                        <img src="media/images/img_slide_0<?php echo $n ?>.png" alt="" />
                        <?php echo $i18n[$info .'_DESCR'] ?>
                    </p>
                </div>
<?php
    endforeach
?>
            </div>
        </div>
    </body>
</html>