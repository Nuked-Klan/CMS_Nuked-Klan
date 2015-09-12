                <div style="text-align: center;margin:30px auto;">
                    <h3 style="margin-bottom:30px;" ><?php echo _CHECKTYPEINSTALL ?></h3>
                        <a href="index.php?action=saveProcessType&amp;assist=no" class="button" ><?php echo ($process == 'install') ? _INSTALLSPEED : _UPDATESPEED ?></a>
                        <a href="index.php?action=saveProcessType&amp;assist=yes" class="button" ><?php echo ($process == 'install') ? _INSTALLASSIST : _UPDATEASSIST ?></a>
                </div>