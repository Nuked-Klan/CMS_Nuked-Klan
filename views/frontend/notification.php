<?php

if ($ajax) :

    ?><div id="ajaxMessage"><?php echo $message ?></div><?php

else :

?>
<div id="nkAlert<?php echo ucfirst($type) ?>" class="nkAlert">
    <strong><?php echo $message ?></strong>
<?php

    if (isset($linkTxt, $linkUrl) && $linkTxt != '' && $linkUrl != '') :

?>
    <a href="<?php echo $linkUrl ?>"><span><?php echo $linkTxt ?></span></a>
<?php


    elseif (isset($backLinkUrl) && $backLinkUrl != '') :

?>
    <a href="<?php echo $backLinkUrl ?>"><span><?php echo __('BACK') ?></span></a>
<?php

    elseif (isset($closeLink)) :
        $js = (isset($reloadOnClose)) ? ';window.opener.document.location.reload(true);' : '';

?>
    <a href="#" onclick="javascript:window.close()<?php echo $js ?>"><b><?php echo __('CLOSE_WINDOW') ?></b></a>
<?php

    endif

?>
</div>
<?php

endif

?>
