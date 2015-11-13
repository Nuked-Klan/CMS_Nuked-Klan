                <div style="text-align: center;margin:30px auto;">
                    <h3 style="margin-bottom:30px;" ><?php echo $i18n['CHECK_TYPE_INSTALL'] ?></h3>
                        <a href="index.php?action=saveProcessType&amp;assist=no" class="button" ><?php echo ($process == 'install') ? $i18n['INSTALL_SPEED'] : $i18n['UPDATE_SPEED'] ?></a>
                        <a href="index.php?action=saveProcessType&amp;assist=yes" class="button" ><?php echo ($process == 'install') ? $i18n['INSTALL_ASSIST'] : $i18n['UPDATE_ASSIST'] ?></a>
                </div>