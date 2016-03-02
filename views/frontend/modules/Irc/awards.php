
<div id="nkIrcWrapper">
    <div class="nkIrcAwards">
        <div class="nkIrcAwardsWrapper">
            <div class="nkIrcAwardsHead nkBgColor3">
                <div>
                    <div class="nkIrcAwardsRow"><h3><?php echo __('CHAN_IRC') ?> - <?php echo __('AWARDS') ?></h3></div>
                </div>
            </div>
            <div class="nkIrcAwardsContent nkBgColor2">
<?php
    foreach ($ircAwardsList as $ircAwards) :
?>
                <div>
                    <div class="nkIrcAwardsRow nkBorderColor1">
                        <div><span>&middot;</span>&nbsp;<span><?php echo nkDate($ircAwards['date']) ?></span></div>
                        <?php echo $ircAwards['text'] ?>
                    </div>
                </div>
<?php
    endforeach;

    if ($nbIrcAwards == 0) :
?>
                <div>
                    <div class="nkIrcAwardsRow nkBorderColor1 nkAlignCenter">
                        <?php echo __('NO_AWARD') ?>
                    </div>
                </div>
<?php
    endif
?>
            </div>
        </div>
    </div>
</div>
