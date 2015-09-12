                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo _ERROR ?></h2>
                    <p><?php echo constant('_'. $error) ?></p>

<?php
    if ($error == 'CONF.INC' || $error == 'COPY') :
?>
                        <div id="log_install">

<?php
        if ($content_web != '') :
?>
                            <?php echo $content_web ?>
                        </div>
                        <p><?php echo constant('_'. $error .'2') ?></p>

<?php
        else :
?>
                            <?php echo _ERRORGENERATECONFINC ?>
                        </div>
<?php
        endif;
    endif;

    if ($content_web != '' && $error != 'CHMOD') :
?>
                        <a href="index.php?action=printConfig" class="button" ><?php echo _DOWNLOAD ?></a>&nbsp;
<?php
    else :
?>
                        <a href="index.php?action=setUserAdmin" class="button" ><?php echo _BACK ?></a>&nbsp;
<?php
    endif;

    if ($content_web != '') :
?>
                        <a href="index.php?action=installSuccess" class="button"><?php echo _CONTINUE ?></a>
                    </div>
<?php
    endif
?>