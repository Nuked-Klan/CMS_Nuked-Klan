    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
<?php
    foreach ($menu as $label => $linkData) :
        $class  = '';

        if (isset($linkData['jsConfirmation'])) {
            $link   = 'javascript:'. $linkData['jsConfirmation'] .'();';
        }
        else {
            $link   = 'index.php?admin='. $GLOBALS['file'];

            if (isset($linkData['page'])) {
                $link .= '&amp;page='. $linkData['page'];

                if ($GLOBALS['page'] == $linkData['page']) $class = 'class="nkClassActive"';
            }
            else {
                if ($GLOBALS['page'] == 'index') $class = 'class="nkClassActive"';
            }
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