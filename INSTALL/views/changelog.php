                <div style="text-align:center;">
                    <img src="images/nk.png" alt="" />
                    <h2><b><?php echo _NEWNKNEWRELEASE ?></b></h2>
                </div>
                <div style="width:90%;margin: 20px auto;">
<?php
    foreach ($changelog as $k) :
?>
                    <p>
                        <b><?php echo constant($k) ?>:</b>
                        <br />
                        <?php echo constant($k .'1') ?>
                        <br />
                    </p>
<?php
    endforeach
?>
                </div>
                <div style="text-align: center;">
                    <a href="index.php?action=setConfig&amp;assist=yes" class="button" ><?php echo _CONTINUE ?></a>
                </div>