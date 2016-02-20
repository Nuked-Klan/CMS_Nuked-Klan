
<script type="text/javascript">
<!--
function go(img) {
    opener.document.getElementById('game_icon').value = img;
}
// -->
</script>
<div style="text-align: center;"><br /><b><?php echo __('CLICK_ICON') ?></b></div>
<div style="text-align: center;"><br />
<?php
foreach ($fileList as $file) :

    ?> <a href="#" onclick="javascript:go('images/games/<?php echo $file ?>');"><img style="border: 0;" src="images/games/<?php echo $file ?>" alt="" title="<?php echo $file ?>" /></a><?php

endforeach
?>
</div>
<div style="text-align: center;">
    <br /><b><a href="#" onclick="self.close()"><?php echo __('CLOSE_WINDOW') ?></a></b>
</div>
