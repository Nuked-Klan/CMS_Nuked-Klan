                <div style="text-align: center;margin:30px auto;">
                    <h2><?php printf($i18n['WELCOME_INSTALL'], $processVersion) ?></h2>
                    <p><?php echo $i18n['GUIDE_INSTALL'] ?></p>
<?php
    if (isset($process)) :
        if ($process == 'install') :
?>
                    <a href="index.php?action=saveProcess&amp;process=install" class="button"><?php echo $i18n['START_INSTALL'] ?></a>
<?php
        elseif ($process == 'update') :
?>
                    <h3 style="background:#ECEADB;width:60%;padding:5px;border:1px solid #ddd;margin:20px auto;"><?php printf($i18n['DETECT_UPDATE'], $currentVersion) ?></h3>
                    <a href="index.php?action=saveProcess&amp;process=update" class="button" ><?php echo $i18n['START_UPDATE'] ?></a>
<?php
        endif;
    endif;

    if (isset($message)) :
?>
                    <p><?php echo $message ?></p>
<?php
        if (isset($alreadyUpdated)) :
?>
                    <a href="index.php?action=deleteSession" class="button"><?php echo $i18n['ACCESS_SITE'] ?></a>
<?php
        endif;
    endif;
?>
                </div>