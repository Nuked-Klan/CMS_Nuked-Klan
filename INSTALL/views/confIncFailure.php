                <h1><?php echo $i18n['ERROR'] ?></h1>
<?php
    if ($error == 'WEBSITE_DIRECTORY_CHMOD') :
?>
                <p><?php echo $i18n['WEBSITE_DIRECTORY_CHMOD'] ?></p>
                <div id="links">
<?php
    elseif (in_array($error, array('CONF_INC_CHMOD_0666', 'CONF_INC_CHMOD_0644'))) :
?>
                <p><?php echo sprintf($i18n['CONF_INC_CHMOD_ERROR'], substr($error, -4)) ?></p>
                <div id="links">
<?php
    elseif ($error == 'WRITE_CONF_INC_ERROR') :
?>
                <p><?php echo $i18n['WRITE_CONF_INC_ERROR'] ?></p>
                <div id="links">
                    <a href="index.php?action=printConfig"><?php echo $i18n['DOWNLOAD'] ?></a>&nbsp;
<?php
    elseif ($error == 'COPY_CONF_INC_ERROR') :
?>
                <p><?php echo $i18n['COPY_CONF_INC_ERROR'] ?></p>
                <div id="links">
                    <a href="index.php?action=printConfig"><?php echo $i18n['DOWNLOAD'] ?></a>&nbsp;
<?php
    endif;

    if ($oldAction != '') :
?>
                    <a href="index.php?action=<?php echo $oldAction ?>"><?php echo $i18n['BACK'] ?></a>
<?php
    endif;

    if ($currentAction != '') :
?>
                    <a href="index.php?action=<?php echo $currentAction ?>"><?php echo $i18n['RETRY'] ?></a>
<?php
    endif
?>
                </div>