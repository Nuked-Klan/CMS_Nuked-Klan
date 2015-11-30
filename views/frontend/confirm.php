<form method="post" action="<?php echo $url ?>">
    <div id="nkAlertWarning" class="nkAlert">
        <strong><?php echo $message ?></strong><br />
<?php
    foreach ($fields as $name => $value) :
?>
        <input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>" />
<?php
    endforeach
?>
        <input type="submit" name="confirm" value="<?php echo _YES ?>" class="nkButton" />
        <input type="submit" name="confirm" value="<?php echo _NO ?>" class="nkButton" />
    </div>
</form>