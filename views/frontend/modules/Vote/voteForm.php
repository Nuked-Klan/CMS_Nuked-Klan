
<form method="post" action="index.php?file=Vote&amp;op=save">
    <div style="text-align: center;">
        <br /><br />
        <?php echo __('ONE_VOTE_ONLY') ?><br /><br />
        <b><?php echo __('NOTE') ?> : </b><!--
        --><select name="vote">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
            <option>7</option>
            <option>8</option>
            <option>9</option>
            <option>10</option>
        </select><!--
        -->&nbsp;<b>/10</b><br /><br />
        <input type="hidden" name="id" value="<?php echo $id ?>" />
        <input type="hidden" name="module" value="<?php echo $module ?>" />
        <input type="submit" name="submit" value="<?php echo __('TO_VOTE') ?>" />
    </div>
</form>
