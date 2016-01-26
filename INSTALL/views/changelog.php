                <div style="text-align:center;">
                    <img src="media/images/nk.png" alt="" />
                    <h2><b><?php printf($i18n['NEW_FEATURES_NK'], $processVersion) ?></b></h2>
                </div>
                <div style="width:90%;margin: 20px auto;">
<?php
    foreach ($changelog as $k) :
?>
                    <p>
                        <b><?php echo $i18n[$k] ?>:</b>
                        <br />
                        <?php echo $i18n[$k .'_DETAIL'] ?>
                        <br />
                    </p>
<?php
    endforeach
?>
                </div>
                <div style="text-align: center;">
                    <a href="index.php?action=setConfig&amp;assist=yes" class="button" ><?php echo $i18n['NEXT'] ?></a>
                </div>