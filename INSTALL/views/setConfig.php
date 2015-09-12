<?php
    $type = ($assist == 'yes') ? 'ASSIST' : 'SPEED'
?>
                <div style="text-align: center;<?php if ($assist == 'no') echo 'margin:30px auto;' ?>">
                    <h2><?php echo ($process == 'install') ? constant('_INSTALL'. $type) : constant('_UPDATE'. $type) ?></h2>
                    <form method="post" action="index.php?action=saveConfig" id="form_config">
                        <h4><?php echo _CONFIG ?></h4>
                        <div id="config">
                            <label for="form_bdd_host"><strong><?php echo _DBHOST ?></strong></label>
                            <input type="text" name="db_host" id="form_bdd_host" value="<?php echo (isset($host)) ? $host : '' ?>" onblur="checkConfigInput($(this));"<?php echo ($process == 'update') ? ' disabled="disabled"' : '' ?> />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo _INSTALLDBHOST ?></p>
<?php
    endif
?>
                            <label for="form_bdd_user"><strong><?php echo _DBUSER ?></strong></label>
                            <input type="text" name="db_user" id="form_bdd_user" value="<?php echo (isset($user)) ? $user : '' ?>" onblur="checkConfigInput($(this));"<?php echo ($process == 'update') ? ' disabled="disabled"' : '' ?> />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo _INSTALLDBUSER ?></p>
<?php
    endif
?>
                            <label for="form_bdd_pass"><strong><?php echo _DBPASS ?></strong></label>
                            <input type="password" name="db_pass" id="form_bdd_pass" value="" onblur="checkConfigInput($(this));" />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo _INSTALLDBPASS ?></p>
<?php
    endif
?>

<?php /*
                            <label for="form_bdd_database_type"><strong><?php echo _DBTYPE ?></strong></label>
                            <select id="form_bdd_database_type">
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
                            <p><img src="images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo _INSTALLDBTYPE ?></p>
<?php
    endif
?>
*/ ?>

                            <label for="form_bdd_prefix"><strong><?php echo _DBPREFIX ?></strong></label>
                            <input type="text" name="db_prefix" id="form_bdd_prefix" value="<?php echo (isset($prefix)) ? $prefix : 'nuked' ?>" onblur="checkConfigInput($(this));"<?php echo ($process == 'update') ? ' disabled="disabled"' : '' ?> />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo _INSTALLDBPREFIX ?></p>
<?php
    endif
?>
                            <label for="form_bdd_name"><strong><?php echo _DBNAME ?></strong></label>
                            <input type="text" name="db_name" id="form_bdd_name" value="<?php echo (isset($name)) ? $name : '' ?>" onblur="checkConfigInput($(this));"<?php echo ($process == 'update') ? ' disabled="disabled"' : '' ?> />
<?php
    if ($assist == 'yes') :
?>
                            <p><img src="images/info.png" style="float:left;margin-right:5px;" alt="" /><?php echo _INSTALLDBNAME ?></p>
<?php
    endif
?>
                        </div>
                        <div id="infos" style="text-align: center;margin:30px auto;color:#FF4040;"></div>
                        <div style="text-align: center;">
                            <a href="#" id="submit" class="button" ><?php echo _SUBMIT ?></a>
                            <a href="index.php?action=<?php echo ($assist == 'no') ? 'selectProcessType' : 'changelog' ?>" class="button"><?php echo _BACK ?></a>
                        </div>
                    </form>
                    <script type="text/javascript">
                    //<![CDATA[
                    $('#submit').click(function() {
                        return checkConfigForm(
                            '<?php echo $process ?>',
                            'form_config',
                            '<?php echo addslashes(_WAIT) ?>',
                            '<?php echo addslashes(_ERROR_HOST) ?>',
                            '<?php echo addslashes(_ERROR_USER) ?>',
                            '<?php echo addslashes(_ERROR_DB) ?>',
                            '<?php echo addslashes(_ERROR_PREFIX) ?>'
                        );
                    });
                    //]]>
                    </script>
                </div>