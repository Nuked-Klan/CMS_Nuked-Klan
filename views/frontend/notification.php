<!--
        <div class="nkBgColor1 nkBorderColor3 nkdefaultNotification">
            <?php echo $data ?>
        </div>
-->
<div id="nkAlert<?php echo ucfirst($type) ?>" class="nkAlert">
    <strong><?php echo $message ?></strong>
<?php
    if (is_string($backLinkUrl) && $backLinkUrl != '') :
?>
    <a href="javascript:history.back()"><span><?php echo _BACK ?></span></a>
<?php
    endif
?>
</div>