    <div style="margin:20px;">
        <div class="notification <?php echo $type ?> png_bg">
            <div>
                <?php echo $message ?>
            </div>
        </div>
    </div>
<?php
    if (is_string($backLinkUrl) && $backLinkUrl != '') :
?>
        <span style="text-align: center;display:block;margin:10px auto;">
            <a class="buttonLink" href="<?php echo $backLinkUrl ?>"><?php echo _BACK ?></a>
        </span>
<?php
    endif
?>