
<div style="text-align:center; padding:0 10px">
<?php

if ($GLOBALS['op'] != 'sql') :

?>
    <big><b><?php echo $GLOBALS['nuked']['name'] ?></b></big><br /><br />
    <?php echo __('ERROR_404') ?>
<?php

else :

?>
    <?php echo __('ERROR_404_SQL') ?>
<?php

endif

?>
    <br /><br />
    <form method="post" action="index.php?file=Search&amp;op=mod_search">
        <p><input type="hidden" name="module" value="" /><input type="text" name="main" size="25" /></p>
        <p><input type="submit" class="button" name="submit" value="<?php echo __('SEARCH_FOR') ?>" /></p>
        <p><a href="index.php?file=Search"><b><?php echo __('ADVANCED_SEARCH') ?></b></a> - <a href="javascript:history.back()"><b><?php echo __('BACK') ?></b></a></p>
    </form>
</div>