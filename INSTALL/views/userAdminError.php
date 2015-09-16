                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['CREATE_USER_ADMIN'] ?></h2>
<?php
    if ($error == 'fields') :
?>
                    <p><?php echo $i18n['ERROR_FIELDS'] ?></p>
<?php
    else
?>
                    <p><?php echo $error ?></p>
<?php
    endif
?>
                    <a href="index.php?action=setUserAdmin" class="button"><?php echo $i18n['BACK'] ?></a>
                </div>