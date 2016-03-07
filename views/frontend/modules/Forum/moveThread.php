        <form action="index.php?file=Forum&amp;op=move&amp;forum_id=<?php echo $forumId ?>&amp;thread_id=<?php echo $threadId ?>" method="post">
            <div id="nkAlertWarning" class="nkAlert">
                <span class="nkAlertSubTitle"><?php echo __('MOVE_TOPIC_TO') ?> : </span>
                <select name="newforum">
<?php
    foreach ($options as $value => $name) :
        if (strpos($value, 'start-optgroup') === 0) :
?>
            <optgroup label="<?php echo $name ?>">

<?php
        elseif (strpos($value, 'end-optgroup') === 0) :
?>
            </optgroup>
<?php
        else :
?>
                    <option value="<?php echo $value ?>"><?php echo $name ?></option>
<?php
        endif;
    endforeach;
?>
                </select><br /><br />
                <input type="submit" name="confirm" value="<?php echo __('YES') ?>" class="nkButton" />&nbsp;<input type="submit" name="confirm" value="<?php echo __('NO') ?>" class="nkButton" />
                <input type="hidden" name="token" value="<?php echo $token ?>" />
            </div>
        </form>
        <br />