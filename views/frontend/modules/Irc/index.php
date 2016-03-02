
<div id="nkIrcMain" class="nkAlignCenter">
    <h3><?php echo __('CHAN_IRC') ?></h3>
    <p><?php echo __('JOIN_CHAN') ?> #<?php echo $GLOBALS['nuked']['irc_chan'] ?>&nbsp;<?php echo __('ON') ?> irc.<?php echo $GLOBALS['nuked']['irc_serv'] ?> :</p>
    <p><a href="irc://irc.<?php echo $GLOBALS['nuked']['irc_serv'] ?>/<?php echo $GLOBALS['nuked']['irc_chan'] ?>" title="<?php echo __('SOFTWARE_CONNECTION') ?>"><b>mIRC</b></a><!--
    -->&nbsp;|&nbsp;<a href="#" onclick="window.open('http://widget.mibbit.com/?server=<?php echo urlencode($GLOBALS['nuked']['irc_serv']) ?>&channel=%23<?php echo urlencode($GLOBALS['nuked']['irc_chan']) ?>','nom','toolbar=1,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=600,height=400,top=30,left=0');return(false)" title="<?php echo sprintf(__('WEBSITE_CONNECTION'), 'Mibbit') ?>"><b>Mibbit</b></a><br /><br />
    [ <a href="index.php?file=Irc&amp;op=awards"><?php echo __('SEE_AWARDS') ?></a> ]</p>
</div>
