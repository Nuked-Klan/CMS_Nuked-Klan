                <img src="media/images/nk.png" alt="" />
                <h1><?php printf($i18n['NEW_FEATURES_NK'], $processVersion) ?></h1>
                <div id="changelogDetail">
<?php
    foreach ($changelog as $k) :
?>
                    <h2><?php echo $i18n[$k] ?>:</h2>
                    <p><?php echo $i18n[$k .'_DETAIL'] ?></p>
<?php
    endforeach
?>
                </div>
                <div id="links">
                    <a href="index.php?action=setDbConfiguration&amp;assist=yes"><?php echo $i18n['NEXT'] ?></a>
                </div>