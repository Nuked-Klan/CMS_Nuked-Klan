                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['ERROR'] ?></h2>
                    <p><?php echo $error ?></p>
<?php
    if (isset($backLink)) :
?>
                    <a href="<?php echo $backLink ?>" class="button" ><?php echo $i18n['BACK'] ?></a>
<?php
    elseif (isset($refreshLink)) :
?>
                    <a href="<?php echo $refreshLink ?>" class="button" ><?php echo $i18n['REFRESH'] ?></a>
<?php
    endif
?>
                </div>