<br />
<div style="text-align: center;">
<?php
    foreach ($links as $label => $url) :
?>
    <a class="buttonLink" href="<?php echo $url ?>"><?php echo $label ?></a>
<?php
    endforeach
?>
</div>