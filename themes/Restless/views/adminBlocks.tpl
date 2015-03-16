<form id="RL_formBlocks" action="index.php?file=Admin&amp;page=theme&amp;op=saveSettings" method="post">
    <table>
        <thead>
            <th style="width:70%;"><?php echo BLOCK_NAME; ?></th>
            <th style="width:15%;text-align:center;">Activation</th>
            <th style="width:15%;text-align:center;"><?php echo _EDIT; ?></th>
        </thead>
        <?php
        foreach($this->get('arrayBlocks') as $block => $checked): ?>
        <tr>
            <td>
                <strong><?php echo $this->get('cfg')->get('block'.$block.'.title'); ?> : </strong>
            </td>
            <td style="text-align:center;">
                <?php checkboxButton('block'.$block.'Active', 'block'.$block.'Active', $checked, true); ?>
            </td>
            <td style="text-align:center;">
                <?php if($block == 'Article'): ?>
                    <img src="themes/Restless/images/block.png" title="<?php echo NO_EDIT; ?>" alt="<?php echo NO_EDIT; ?>" />
                <?php else: ?>
                    <a class="RL_getRow" id="<?php echo $block; ?>" href="#" >
                        <img src="themes/Restless/images/edit.png" title="<?php echo _EDIT; ?>" alt="<?php echo _EDIT; ?>" />
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr class="RL_alertForm">
            <td colspan="3">
                <?php printNotification(EDIT_NOT_SAVE, null, 'attention', false, false); ?>
            </td>
        </tr>
        <tr class="RL_alertForm">
        </tr>
        <tr>
            <td colspan="3" style="text-align:center;">
                <input class="button" type="submit" value="<?php echo SUBMIT; ?>"/>
            </td>
        </tr>
    </table>
    <?php
        foreach($this->get('arrayBlocks') as $block => $value){
            $this->assign('currentAdminBlock', $block);
            $this->render('adminBlockForm');
        }
    ?>
</form>