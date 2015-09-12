                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo _WELCOMEINSTALL ?></h2>
                    <p><?php echo _GUIDEINSTALL ?></p>
<?php
    if (isset($process)) :
        if ($process == 'install') :
?>
                    <a href="index.php?action=saveProcess&amp;process=install" class="button"><?php echo _STARTINSTALL ?></a>
<?php
        elseif ($process == 'update') :
?>
                    <h3 style="background:#ECEADB;width:60%;padding:5px;border:1px solid #ddd;margin:20px auto;"><?php echo _DETECTUPDATE ?> <?php echo $version ?> <?php echo _DETECTUPDATEEND ?></h3>
                    <a href="index.php?action=saveProcess&amp;process=update" class="button" ><?php echo _STARTUPDATE ?></a>
<?php
        endif;
    endif;

    if (isset($message)) :
?>
                    <p><?php echo $message ?></p>
                    <a href="index.php?action=deleteSession" class="button"><?php echo _ACCESS_SITE ?></a>
<?php
    endif;

    if (isset($error)) :
?>
                    <p><?php echo $error ?></p>
<?php
    endif
?>
                </div>