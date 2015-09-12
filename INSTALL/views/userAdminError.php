                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo _CHECKUSERADMIN ?></h2>
<?php
    if ($error == 'fields') :
?>
                    <p><?php echo _ERRORFIELDS ?></p>
<?php
    else
?>
                    <p><?php echo $error ?></p>
<?php
    endif
?>
                    <a href="index.php?action=setUserAdmin" class="button"><?php echo _BACK ?></a>
                </div>