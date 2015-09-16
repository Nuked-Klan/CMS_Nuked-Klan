<?php
    $i = 0;

    foreach ($content as $k => $v) :
?>
<a href="<?php echo $v[2] ?>" ><img src="<?php echo $v[1] ?>" alt="<?php echo $v[0] ?>" /></a>
<?php
        $i++;
    endforeach;

    if ($i == 0) echo $i18n['NO_PARTNERS'] ?>