<!--

NOTE : Used by defaultNotification function with nudePage design in index.php file of User module

        <div class="nkBgColor1 nkBorderColor3 nkdefaultNotification">
            <?php echo $data ?>
        </div>
-->

<?php

if ($ajax) :

?>
<div id="nkAlert<?php echo ucfirst($type) ?>" class="nkAlert">
    <strong><?php echo $message ?></strong>
<?php

    if (isset($backLinkUrl) && $backLinkUrl != '') :

?>
    <a href="<?php echo $backLinkUrl ?>"><span><?php echo __('BACK') ?></span></a>
<?php

    else if (isset($closeLink)) :
        $js = (isset($reloadOnClose)) ? ';window.opener.document.location.reload(true);' : '';

?>
    <a href="#" onclick="javascript:window.close()<?php echo $js ?>"><b><?php echo __('CLOSE_WINDOW') ?></b></a>
<?php

    endif

?>
</div>
<?php

else :

?>
<div id="ajaxMessage"><?php echo $message ?></div>
<?php

endif

?>







