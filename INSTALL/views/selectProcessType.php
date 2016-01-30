                <h1><?php echo $i18n['CHECK_TYPE_INSTALL'] ?></h1>
                <div id="links">
                    <a href="index.php?action=saveProcessType&amp;assist=no"><?php echo ($process == 'install') ? $i18n['INSTALL_SPEED'] : $i18n['UPDATE_SPEED'] ?></a>
                    <a href="index.php?action=saveProcessType&amp;assist=yes"><?php echo ($process == 'install') ? $i18n['INSTALL_ASSIST'] : $i18n['UPDATE_ASSIST'] ?></a>
                </div>