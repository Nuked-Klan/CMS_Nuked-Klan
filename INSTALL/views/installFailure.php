                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['ERROR'] ?></h2>
                    <p><?php echo $i18n[$error] ?></p>

<?php
    if ($error == 'CONF.INC' || $error == 'COPY') :
?>
                        <div id="log_install">

<?php
        if ($content_web != '') :
?>
                            <?php echo $content_web ?>
                        </div>
                        <!--<p><?php //echo constant('_'. $error .'2') ?></p>-->

<?php
        else :
?>
                            <?php echo $i18n['ERROR_GENERATE_CONF_INC'] ?>
                        </div>
<?php
        endif;
    endif;

    if ($content_web != '' && $error != 'CHMOD') :
?>
                        <a href="index.php?action=printConfig" class="button" ><?php echo $i18n['DOWNLOAD'] ?></a>&nbsp;
<?php
    else :
?>
                        <a href="index.php?action=setUserAdmin" class="button" ><?php echo $i18n['BACK'] ?></a>&nbsp;
<?php
    endif;

    if ($content_web != '') :
?>
                        <a href="index.php?action=installSuccess" class="button"><?php echo $i18n['NEXT'] ?></a>
                    </div>
<?php
    endif
?>