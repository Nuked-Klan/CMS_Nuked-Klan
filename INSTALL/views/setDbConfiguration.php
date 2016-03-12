<?php
    $type = ($assist == 'yes') ? 'ASSIST' : 'SPEED'
?>
                <h1><?php echo ($process == 'install') ? $i18n['INSTALL_'. $type] : $i18n['UPDATE_'. $type] ?></h1>
                <form method="post" action="index.php?action=saveDbConfiguration" id="dbConfigurationForm" class="form">
                    <h2><?php echo $i18n['CONFIG'] ?></h2>
                    <div id="dbTypeBox">
                        <label for="dbType"><?php echo $i18n['DB_TYPE'] ?></label> :&nbsp;
                        <select id="dbType" name="db_type">
<?php
    foreach ($databaseTypeList as $dbTypeName) :
?>
                            <option value="<?php echo $dbTypeName ?>"<?php selected($dbTypeName, $dbType) ?>><?php echo $dbTypeName ?></option>
<?php
    endforeach
?>
                        </select>
                    </div>
<?php
    if ($assist == 'yes') :
?>
                    <p id="dbTypeInfo"><img class="infoLogo" src="media/images/info.png" alt="" /><?php echo $i18n['INSTALL_DB_TYPE'] ?></p>
<?php
    endif
?>
                    <div id="dbHostBox">
                        <label for="dbHost"><?php printf($i18n['DB_HOST'], 'MySQL') ?></label>
                        <input type="text" name="db_host" id="dbHost" value="<?php echo $dbHost ?>"<?php disabled($process, 'update') ?> />
                    </div>
<?php
    if ($assist == 'yes') :
?>
                    <p id="dbHostInfo"><img class="infoLogo" src="media/images/info.png" alt="" /><span><?php printf($i18n['INSTALL_DB_HOST'], 'MySQL') ?></span></p>
<?php
    endif
?>
                    <div id="dbUserBox">
                        <label for="dbUser"><?php echo $i18n['DB_USER'] ?></label>
                        <input type="text" name="db_user" id="dbUser" value="<?php echo $dbUser ?>"<?php disabled($process, 'update') ?> />
                    </div>
<?php
    if ($assist == 'yes') :
?>
                        <p id="dbUserInfo"><img class="infoLogo" src="media/images/info.png" alt="" /><span><?php printf($i18n['INSTALL_DB_USER'], 'MySQL') ?></span></p>
<?php
    endif
?>
                    <div id="dbPasswordBox">
                        <label for="dbPassword"><?php echo $i18n['DB_PASSWORD'] ?></label>
                        <input type="password" name="db_pass" id="dbPassword" value="" />
                    </div>
<?php
    if ($assist == 'yes') :
?>
                    <p id="dbPasswordInfo"><img class="infoLogo" src="media/images/info.png" alt="" /><span><?php printf($i18n['INSTALL_DB_PASSWORD'], 'MySQL') ?></span></p>
<?php
    endif
?>
                    <div id="dbPrefixBox">
                        <label for="dbPrefix"><?php echo $i18n['DB_PREFIX'] ?></label>
                        <input type="text" name="db_prefix" id="dbPrefix" value="<?php echo $dbPrefix ?>"<?php disabled($process, 'update') ?> />
                    </div>
<?php
    if ($assist == 'yes') :
?>
                    <p id="dbPrefixInfo"><img class="infoLogo" src="media/images/info.png" alt="" /><span><?php printf($i18n['INSTALL_DB_PREFIX'], 'MySQL') ?></span></p>
<?php
    endif
?>
                    <div id="dbNameBox">
                        <label for="dbName"><?php echo $i18n['DB_NAME'] ?></label>
                        <input type="text" name="db_name" id="dbName" value="<?php echo $dbName ?>"<?php disabled($process, 'update') ?> />
                    </div>
<?php
    if ($assist == 'yes') :
?>
                    <p id="dbNameInfo"><img class="infoLogo" src="media/images/info.png" alt="" /><span><?php printf($i18n['INSTALL_DB_NAME'], 'MySQL') ?></span></p>
<?php
    endif

/*
?>
                    <p><input type="checkbox" id="advanced" name="advanced" />&nbsp;<label id="advancedLabel" for="advanced"><?php echo $i18n['ADVANCED_PARAMETERS'] ?></label></p>
                    <div id="advancedBox">
                        <div id="dbPortBox">
                            <label for="dbPort"><?php echo $i18n['DB_PORT'] ?></label>
                            <input type="text" name="db_port" id="dbPort" maxlength="5" value="<?php echo $dbPort ?>"<?php disabled($process, 'update') ?> />
                        </div>
<?php
    if ($assist == 'yes') :
?>
                        <p id="dbPortInfo"><img class="infoLogo" src="media/images/info.png" alt="" /><span><?php printf($i18n['INSTALL_DB_PORT'], 'MySQL') ?></span></p>
<?php
    endif
?>
                        <div id="dbPersistentBox">
                            <input type="checkbox" id="dbPersistent" name="db_persistent"<?php checked($dbPersistent) ?> value="true" />&nbsp;<label id="dbPersistentLabel" for="dbPersistent"><?php echo $i18n['DB_PERSISTENT'] ?></label>
                        </div>
<?php
    if ($assist == 'yes') :
?>
                        <p id="dbPersistentInfo"><img class="infoLogo" src="media/images/info.png" alt="" /><span><?php printf($i18n['INSTALL_DB_PERSISTENT'], 'MySQL') ?></span></p>
<?php
    endif
?>
                    </div>
*/ ?>

                    <div id="notification"></div>
                    <div id="links">
                        <input type="submit" name="submit" value="<?php echo $i18n['SUBMIT'] ?>" />
                        <a href="index.php?action=<?php echo ($assist == 'no') ? 'selectProcessType' : 'changelog' ?>"><?php echo $i18n['BACK'] ?></a>
                    </div>
                </form>
