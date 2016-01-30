                <h1><?php echo $i18n['DEPRECATED_FILES'] ?></h1>
                <p><?php echo $i18n['CLEANING_FILES'] ?></p>
                <div>
                    <ul>
<?php
    foreach ($deprecatedFiles as $file) :
?>
                        <li><?php echo $file ?></li>
<?php
    endforeach
?>
                    </ul>
                </div>
                <div id="links">
                    <a href="index.php?action=cleaningFiles"><?php echo $i18n['RETRY'] ?></a>
                </div>