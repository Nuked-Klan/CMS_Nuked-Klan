                <h1><?php echo $i18n['ERROR'] ?></h1>
                <p><?php echo $error ?></p>
                <div id="links">
<?php
    if ($oldAction != '') :
?>
                    <a href="index.php?action=<?php echo $oldAction ?>"><?php echo $i18n['BACK'] ?></a>
<?php
    endif;

    if ($currentAction != '') :
?>
                    <a href="index.php?action=<?php echo $currentAction ?>"><?php echo $i18n['REFRESH'] ?></a>
<?php
    endif
?>
                </div>
