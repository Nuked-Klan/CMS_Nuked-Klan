                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['ERROR'] ?></h2>
                    <p><?php echo $error ?></p>
<?php
    if ($oldAction != '') :
?>
                    <a href="index.php?action=<?php echo $oldAction ?>" class="button" ><?php echo $i18n['BACK'] ?></a>
<?php
    endif;

    if ($currentAction != '') :
?>
                    <a href="index.php?action=<?php echo $currentAction ?>" class="button" ><?php echo $i18n['REFRESH'] ?></a>
<?php
    endif
?>
                </div>