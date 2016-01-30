                <h1><?php printf($i18n['WELCOME_INSTALL'], $processVersion) ?></h1>
                <p><?php echo $i18n['GUIDE_INSTALL'] ?></p>
<?php
    if (isset($process)) :
        if ($process == 'install') :
?>
                <div id="links">
                    <a href="index.php?action=saveProcess&amp;process=install"><?php echo $i18n['START_INSTALL'] ?></a>
                </div>
<?php
        elseif ($process == 'update') :
?>
                <p class="warningNotification"><?php printf($i18n['DETECT_UPDATE'], $currentVersion) ?></p>
                <div id="links">
                    <a href="index.php?action=saveProcess&amp;process=update"><?php echo $i18n['START_UPDATE'] ?></a>
                </div>
<?php
        endif;
    endif;

    if (isset($message)) :
?>
                <p><?php echo $message ?></p>
<?php
        if (isset($alreadyUpdated)) :
?>
                <div id="links">
                    <a href="index.php?action=deleteSession"><?php echo $i18n['ACCESS_SITE'] ?></a>
                </div>
<?php
        endif;
    endif;
?>