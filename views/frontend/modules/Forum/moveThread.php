        <form action="index.php?file=Forum&amp;op=move" method="post">
            <div id="nkAlertWarning" class="nkAlert">
                <span class="nkAlertSubTitle"><?php echo _MOVETOPIC ?> : </span>
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
                <input type="submit" name="confirm" value="<?php echo _YES ?>" class="nkButton" />&nbsp;<input type="submit" name="confirm" value="<?php echo _NO ?>" class="nkButton" />
                <input type="hidden" name="forum_id" value="<?php echo $forumId ?>" />
                <input type="hidden" name="thread_id" value="<?php echo $threadId ?>" />
            </div>
        </form>
        <br />