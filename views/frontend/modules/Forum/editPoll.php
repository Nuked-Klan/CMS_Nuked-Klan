        <div id="nkForumViewMainPoll" class="nkBorderColor1">
            <div class="nkForumViewPollBg"></div><!-- @whitespace
            --><div class="nkForumViewPoll">
                <div class="nkForumPollTitle">
                    <h3><?php echo _POSTSURVEY ?></h3>
                </div>
                <form method="post" action="index.php?file=Forum&amp;op=savePoll">
                    <div class="nkForumPollIniTable">
                        <div class="nkForumPollOptionsIni">
                            <div><label for="forumPollTitle"><strong><?php echo _QUESTION ?>&nbsp;:&nbsp;</strong></label></div>
                            <div><input id="forumPollTitle" type="text" name="title" size="40" value="<?php echo $title ?>"/></div>
                        </div>
<?php
    $r = 1;

    foreach ($pollOptions as $option) :
?>
                        <div class="nkForumPollOptionsIni">
                            <div><label for="forumPollOption<?php echo $r ?>"><?php echo _OPTION ?>&nbsp;<?php echo $r ?>&nbsp;:&nbsp;</label></div>
                            <div><input id="forumPollOption<?php echo $r ?>" type="text" name="option[<?php echo $r ?>]" size="40" value="<?php echo $option['option_text'] ?>" /></div>
                        </div>
<?php
        $r++;
    endforeach;

    if ($newOption) :
?>
                        <div class="nkForumPollOptionsIni">
                            <div><label for="forumPollOption<?php echo $r ?>"><?php echo _OPTION ?>&nbsp;<?php echo $r ?>&nbsp;:&nbsp;</label></div>
                            <div><input id="forumPollOption<?php echo $r ?>" type="text" name="newOption" size="40" /></div>
                        </div>
<?php
    endif
?>
                    </div>
<?php
    if ($pollId > 0) :
?>
                    <input type="hidden" name="poll_id" value="<?php echo $pollId ?>" />
<?php
    endif;

    if (isset($maxOptions)) :
?>
                    <input type="hidden" name="maxOption" value="<?php echo $maxOptions ?>" />
<?php
    endif;
?>
                    <input type="hidden" name="thread_id" value="<?php echo $threadId ?>" />
                    <input type="hidden" name="forum_id" value="<?php echo $forumId ?>" />
                    <div id="nkForumPollActionLinks">
                        <input type="submit" value="<?php echo ($pollId > 0) ? _MODIFTHISPOLL : _ADDTHISPOLL ?>" class="nkButton"  />
                    </div>
                </form>
            </div>
        </div>