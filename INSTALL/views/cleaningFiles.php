                <div style="text-align: center;margin:30px auto;">
                    <h2><?php echo $i18n['DEPRECATED_FILES'] ?></h2>
                    <p><?php echo $i18n['CLEANING_FILES'] ?></p>
                    <div style="display: inline-block;text-align:left;">
                        <ul style="margin:0;padding-left:0;">
<?php
    foreach ($deprecatedFiles as $file) :
?>
                            <li style="padding:2px;"><?php echo $file ?></li>
<?php
    endforeach
?>
                        </ul>
                    </div>
                    <p><a href="index.php?action=cleaningFiles" class="button"><?php echo $i18n['RETRY'] ?></a></p>
                </div>