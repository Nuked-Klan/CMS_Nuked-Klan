        <div id="nkForumViewMainPoll" class="nkBorderColor1">
            <div class="nkForumViewPollBg"></div><!-- @whitespace
            --><div class="nkForumViewPoll">
                <div class="nkForumPollTitle">
                    <h3><?php echo _POSTSURVEY ?></h3>
                </div>
                <form method="post" action="<?php echo $action ?>">
                    <div class="nkForumPollIniTable">
                        <div class="nkForumPollOptionsIni">
                            <div><strong><?php echo _QUESTION ?></strong></div>
                            <div><input type="text" name="titre" size="40" value="<?php echo $title ?>"/></div>
                        </div>
<?php
    $r = 0;
    foreach ($pollOptions as $options) :
        $r++;
?>
                        <div class="nkForumPollOptionsIni">
                            <div><span><?php echo _OPTION ?>&nbsp;<?php echo $r ?>&nbsp;:&nbsp;</span></div>
                            <div><input type="text" name="option[<?php echo $r ?>]" size="40" value="<?php echo $options['option_text'] ?>" /></div>
                        </div>
<?php
    endforeach;

    $r++;
?>
                        <div class="nkForumPollOptionsIni">
                            <div><span><?php echo _OPTION ?>&nbsp;<?php echo $r ?>&nbsp;:&nbsp;</span></div>
                            <div><input type="text" name="newoption" size="40" /></div>
                        </div>
                    </div>
<?php
    if (isset($poll_id)) :
?>
                    <input type="hidden" name="poll_id" value="<?php echo $poll_id ?>" />
<?php
    endif;

    if (isset($max_option)) :
?>
                    <input type="hidden" name="max_option" value="<?php echo $max_option ?>" />
<?php
    endif;
?>
                    <input type="hidden" name="thread_id" value="<?php echo $thread_id ?>" />
                    <input type="hidden" name="forum_id" value="<?php echo $forum_id ?>" />
                    <div id="nkForumPollActionLinks">
                        <input type="submit" value="<?php echo _MODIFTHISPOLL ?>" class="nkButton"  />
                    </div>
                </form>
            </div>
        </div>