<?php if(count($this->get('blockSocialContent')) > 0 && $this->get('blockSocialActive') == true): ?>
<article>
    <header>
        <h1 class="RL_modTitle">{{blockSocialTitle}}</h1>
    </header>
    <div id="RL_followContent">
        <?php foreach($this->get('blockSocialContent') as $social => $socialLink): ?>
        <a href="<?php echo $socialLink; ?>" class="RL_follow<?php echo $social; ?>" target="_blank">
            <span><?php echo $social; ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</article>
<?php endif; ?>