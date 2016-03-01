    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
<?php
    foreach ($menu as $label => $linkData) :
        $class = '';

        if (isset($linkData['jsConfirmation'])) {
            $link = 'javascript:'. $linkData['jsConfirmation'] .'();';
        }
        else {
            $link = 'admin='. $GLOBALS['file'];

            if (isset($linkData['uri'])) {
                foreach ($linkData['uri'] as $k => $v)
                    $link .= '&amp;'. $k.'='. $v;
            }

            if (str_replace('&amp;', '&', $link) == $_SERVER['QUERY_STRING'])
                $class = 'class="nkClassActive"';
        }

?>
            <li <?php echo $class ?>>
                <a class="shortcut-button" href="index.php?<?php echo $link ?>">
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