<?php
    $type = ($assist == 'yes') ? 'ASSIST' : 'SPEED'
?>
                <div style="text-align: center;<?php if ($assist == 'no') echo 'margin:30px auto;' ?>">
                    <h2><?php echo ($process == 'install') ? $i18n['INSTALL_'. $type] : $i18n['UPDATE_'. $type] ?></h2>
                    <form method="post" action="index.php?action=saveConfig" id="form_config">
                        <h4><?php echo $i18n['CONFIG'] ?></h4>
                        <div id="config">
                            <label for="db_host"><strong><?php echo $i18n['DB_HOST'] ?></strong></label>
                            <input type="text" name="db_host" id="db_host" value="<?php echo (isset($host)) ? $host : '' ?>" onblur="checkConfigInput($(this));"<?php echo ($process == 'update') ? ' disabled="disabled"' : '' ?> />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="media/images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo $i18n['INSTALL_DB_HOST'] ?></p>
<?php
    endif
?>
                            <label for="db_user"><strong><?php echo $i18n['DB_USER'] ?></strong></label>
                            <input type="text" name="db_user" id="db_user" value="<?php echo (isset($user)) ? $user : '' ?>" onblur="checkConfigInput($(this));"<?php echo ($process == 'update') ? ' disabled="disabled"' : '' ?> />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="media/images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo $i18n['INSTALL_DB_USER'] ?></p>
<?php
    endif
?>
                            <label for="db_pass"><strong><?php echo $i18n['DB_PASSWORD'] ?></strong></label>
                            <input type="password" name="db_pass" id="db_pass" value="" onblur="checkConfigInput($(this));" />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="media/images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo $i18n['INSTALL_DB_PASSWORD'] ?></p>
<?php
    endif
?>

<?php /*
                            <label for="db_type"><strong><?php echo $i18n['DB_TYPE'] ?></strong></label>
                            <select id="db_type" name="db_type">
<?php
    foreach ($databaseTypeList as $k => $v) :
?>
                                <option value="<?php echo $k ?>"><?php echo $v ?></option>
<?php
    endforeach
?>
                            </select>
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="media/images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo $i18n['INSTALL_DB_TYPE'] ?></p>
<?php
    endif
?>
*/ ?>

                            <label for="db_prefix"><strong><?php echo $i18n['DB_PREFIX'] ?></strong></label>
                            <input type="text" name="db_prefix" id="db_prefix" value="<?php echo (isset($prefix)) ? $prefix : 'nuked' ?>" onblur="checkConfigInput($(this));"<?php echo ($process == 'update') ? ' disabled="disabled"' : '' ?> />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="media/images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo $i18n['INSTALL_DB_PREFIX'] ?></p>
<?php
    endif
?>
                            <label for="db_name"><strong><?php echo $i18n['DB_NAME'] ?></strong></label>
                            <input type="text" name="db_name" id="db_name" value="<?php echo (isset($name)) ? $name : '' ?>" onblur="checkConfigInput($(this));"<?php echo ($process == 'update') ? ' disabled="disabled"' : '' ?> />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="media/images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo $i18n['INSTALL_DB_NAME'] ?></p>
<?php
    endif
?>
                        </div>
                        <div id="infos" style="text-align: center;margin:30px auto;color:#FF4040;"></div>
                        <div style="text-align: center;">
                            <a href="#" id="submit" class="button" ><?php echo $i18n['SUBMIT'] ?></a>
                            <a href="index.php?action=<?php echo ($assist == 'no') ? 'selectProcessType' : 'changelog' ?>" class="button"><?php echo $i18n['BACK'] ?></a>
                        </div>
                    </form>
                    <script type="text/javascript">
                    //<![CDATA[
                    $('#submit').click(function() {
                        return checkConfigForm();
                    });
                    //]]>
                    </script>
                </div>