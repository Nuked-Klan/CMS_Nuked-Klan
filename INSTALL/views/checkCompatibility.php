                <h1><?php echo $i18n['CHECK_COMPATIBILITY_HOSTING'] ?></h1>
                <table>
                    <tr>
                        <th class="component"><?php echo $i18n['COMPONENT'] ?></th>
                        <th class="compatibility"><?php echo $i18n['COMPATIBILITY'] ?></th>
                    </tr>
<?php
    $i = $required = $optional = 0;

    foreach ($requirements as $extensionName => $requirement) :
        if ($extensionName == 'CHMOD_TEST') :
            foreach ($requirement as $dir => $dirRequirement) :
                if ($dirRequirement == 'enabled') :
                    $src = 'media/images/ok.png';
                elseif ($dirRequirement == 'optional-disabled') :
                    $optional++;
                    $src = 'media/images/warning.png';
                else :
                    $required++;
                    $src = 'media/images/nook.png';
                endif;
            endforeach;
        else :
            if ($requirement == 'enabled') :
                $src = 'media/images/ok.png';
            elseif ($requirement == 'optional-disabled') :
                $optional++;
                $src = 'media/images/warning.png';
            else :
                $required++;
                $src = 'media/images/nook.png';
            endif;
        endif;

?>

                    <tr class="<?php echo ($i % 2 == 0) ? 'bgRow1' : 'bgRow2' ?>">
                        <td class="component"><?php echo $i18n[$extensionName] ?></td>
                        <td class="compatibility"><img src="<?php echo $src ?>" alt="" /></td>
<?php
        //endif
?>
                    </tr>

<?php
        if ($extensionName != 'CHMOD_TEST' && in_array($requirement, array('required-disabled', 'optional-disabled'))) :
?>
                    <tr>
                        <td class="componentError <?php echo ($requirement == 'optional-disabled') ? 'warning' : 'error' ?>" colspan="2">
                            <?php echo $i18n[$extensionName .'_ERROR'] ?>
                        </td>
                    </tr>
<?php
        elseif ($extensionName == 'CHMOD_TEST') :
            foreach ($requirement as $dir => $dirRequirement) :
                if (in_array($dirRequirement, array('required-disabled', 'optional-disabled'))) :
?>
                    <tr>
                        <td class="componentError <?php echo ($dirRequirement == 'optional-disabled') ? 'warning' : 'error' ?>" colspan="2">
<?php
                    $fileperms = false;

                    if (is_readable($dir)) :
                        if ($dir == 'WEBSITE_DIRECTORY')
                            $fileperms = fileperms('../');
                        else
                            $fileperms = fileperms('../'. $dir);
                    endif;

                    if ($fileperms !== false) :
?>
                            <?php echo sprintf($i18n['CHMOD_TEST_ERROR'], substr(sprintf('%o', $fileperms), -4));

                    else : ?>
                            <?php echo sprintf($i18n['NO_READABLE_DIRECTORY'], $dir);
                    endif;
                endif;
?>
                        </td>
                    </tr>
<?php
            endforeach;
        endif;

        $i++;
    endforeach;
?>
                </table>
<?php
    if ($required > 0) :
?>
                <p class="warningNotification"><?php echo $i18n['BAD_HOSTING'] ?></p>
<?php
    elseif ($optional > 0) :
?>
                <p class="warningNotification"><?php echo $i18n['BAD_HOSTING'] ?></p>
                <div id="links">
                    <a href="index.php?action=chooseSendStats"><?php echo $i18n['FORCE'] ?></a>
                </div>
<?php
    else :
?>
                <div id="links">
                    <a href="index.php?action=chooseSendStats"><?php echo $i18n['NEXT'] ?></a>
                </div>
<?php
    endif
?>