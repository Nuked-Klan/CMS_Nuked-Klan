<div id="nkAlertError" class="nkAlert">
    <strong><?php echo __('NO_ENTRANCE') ?></strong>
<?php

if (isset($closeLink)) :

?>
    <a href="#" onclick="javascript:window.close()"><b><?php echo __('CLOSE_WINDOW') ?></b></a>
<?php

else :

?>
    <a href="javascript:history.back()"><span><?php echo __('BACK') ?></span></a>
<?php

endif

?>
</div>