<li>
    <a href="<?php echo $this->get('data')['link']; ?>" <?php echo ($this->get('data')['blank']  ? 'target="_blank"' : null); ?> >
        <span><?php echo $this->get('data')['title']; ?></span>
    </a>
    <?php if(array_key_exists('subnav', $this->get('data'))) : ?>
    <ul class="RL_subMenu">
        <?php foreach($this->get('data')['subnav'] as $sub):
            $this->render('navigation-item', $sub);
        endforeach; ?>
    </ul>
    <?php endif; ?>
</li>