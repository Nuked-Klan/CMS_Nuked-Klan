<br />
<div style="text-align: center;">
<?php
    foreach ($links as $label => $url) :
        if ($label == 'closeLink') :
?>
    <a class="buttonLink" href="#" onclick="javascript:window.close()"><?php echo __('CLOSE_WINDOW') ?></a>
<?php
        else :
?>
    <a class="buttonLink" href="<?php echo $url ?>"><?php echo $label ?></a>
<?php
        endif;
    endforeach;
?>
</div>