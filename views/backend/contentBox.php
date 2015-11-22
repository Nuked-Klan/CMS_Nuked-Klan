<div class="content-box"><!-- Start Content Box -->
    <div class="content-box-header">
        <h3><?php echo $title ?></h3>
        <div style="text-align:right;">
            <a href="help/<?php echo $GLOBALS['language'] ?>/<?php echo $helpFile ?>.php" rel="modal">
            <img style="border: 0;" src="help/help.gif" alt="" title="<?php echo _HELP ?>" /></a>
        </div>
    </div>
    <div class="tab-content" id="tab2">
<?php echo $content ?>
        <br />
    </div>
</div>