<?php if($this->get('makeForm') === true): ?>
<div class="RL_form{{currentAdminBlock}} RL_formContent">
    <h3>
        <?php echo EDIT_BLOCK; ?> "{{blockTitle}}"
    </h3>

    <div>
        <label><?php echo BLOCK_TITLE; ?> :</label>
        <input class="RL_input" type="text" name="block{{currentAdminBlock}}Title" value="{{blockTitle}}" data-check="{{blockTitle}}"/>
    </div>
    <?php if($this->get('makeSelect') === true): ?>
    <div>
        <label><?php echo NB_ELEMENTS_BLOCK; ?> :</label>
        <select class="RL_select" name="block{{currentAdminBlock}}NbItems" data-check="{{blockNbItems}}">
            <?php foreach($this->get('selectBlock') as $key => $selected): ?>
            <option value="<?php echo $key; ?>"
            <?php echo $selected; ?>><?php echo $key; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
        endif;
        if($this->get('makeSelectCat') === true):
    ?>
    <div>
        <label><?php echo SELECT_CAT; ?> :</label>
        <select name="block{{currentAdminBlock}}Cat" data-check="{{selectedCat}}">
            <?php
                foreach($this->get('selectCat') as $id => $name):
                    $selected = null;
                    if($id == $this->get('selectedCat')){
                        $selected = 'selected="selected"';
                    }
            ?>
            <option value="<?php echo $id; ?>"
            <?php echo $selected; ?>><?php echo $name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
        endif;
        if($this->get('makeCheckbox') === true):
    ?>
    <div>
        <label class="RL_inline"><?php echo ACTIVE_LIGHTBOX; ?> :</label>
        <?php checkboxButton($this->get('lightboxInputName'), $this->get('lightboxInputName'), $this->get('lightboxChecked'), true); ?>
    </div>
    <?php
        endif;
        if($this->get('makeInputSocial') === true):
            foreach($this->get('arrayInputSocial') as $name => $value):
    ?>
            <div>
                <label class="RL_socialLabel RL_<?php echo $name; ?>"><?php echo LINK.' '.$name; ?> :</label>
                <input class="RL_input RL_socialInput" type="text" name="social<?php echo $name; ?>" value="<?php echo $value; ?>" data-check="<?php echo $value; ?>"/>
            </div>
    <?php
            endforeach;
        endif;
    ?>
    <p style="text-align:center;">
        <a id="RL_close" class="button" href="#"><?php echo CLOSE; ?></a>
    </p>
</div>
<?php endif; ?>