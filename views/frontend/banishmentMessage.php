
        <div class="nkBgColor1 nkBorderColor3" style="margin: 200px auto; padding: 20px; width: 800px; text-align: center">
            <big><b><?php echo $GLOBALS['nuked']['name'] ?> - <?php echo $GLOBALS['nuked']['slogan'] ?></b><br /><br /><?php echo __('IP_BANNED') ?></big>
<?php
    if (! empty($reason)) :
?>
            <br /><p><hr class="nkColor3" style="height: 1px; width: 95%" />
            <big><b><?php echo __('REASON') ?> :</b><br /><?php echo nkHtmlEntityDecode($reason) ?></big></p>
<?php
    endif
?>
            <hr class="nkColor3" style="height: 1px; width: 95%" /><br />
            <?php echo __('DURING') ?> <?php echo strtolower($duration) ?><br />
            <?php echo __('CONTACT_WEBMASTER') ?> : <a href="mailto:<?php echo $GLOBALS['nuked']['mail'] ?>"><?php echo $GLOBALS['nuked']['mail'] ?></a>
        </div>
