<li>
    <a href="<?php echo $this->data['link']; ?>" <?php echo ($this->data['blank']  ? 'target="_blank"' : null); ?> >
        <span><?php echo $this->data['title']; ?></span>
    </a>
    <?php if(array_key_exists('subnav', $this->data)) : ?>
    <ul class="RL_subMenu">
        <?php foreach($this->data['subnav'] as $sub):
            new viewTpl('navigation-item', $sub);
        endforeach; ?>
    </ul>
    <?php endif; ?>
</li>