    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
<?php
    foreach ($menu as $label => $linkData) :
        $link   = 'index.php?file='. $GLOBALS['file'] .'&amp;page=admin';
        $class  = '';

        if (isset($linkData['op'])) {
            $link .= '&amp;op='. $linkData['op'];

            if ($_REQUEST['op'] == $linkData['op']) $class = 'class="nkClassActive"';
        }
        else {
            if ($_REQUEST['op'] == 'index') $class = 'class="nkClassActive"';
        }

?>
            <li <?php echo $class ?>>
                <a class="shortcut-button" href="<?php echo $link ?>">
                    <img src="<?php echo $linkData['img'] ?>" alt="icon" />
                    <span><?php echo $label ?></span>
                </a>
            </li>
<?php
    endforeach
?>
        </ul>
    </div>
    <div class="clear"></div>