<?php if(count($this->get('blockForumContent')) > 0 && $this->get('blockForumActive') == true): ?>
<section class="RL_blockRight">
    <header>
        <h3 class="RL_lastsPostTitle">{{blockForumTitle}}</h3>
        <a class="RL_moreButton" href="index.php?file=Forum">Plus</a>
    </header>
    <article class="RL_lastsPost">
        <?php foreach($this->get('blockForumContent') as $post): ?>
        <div class="RL_lastPost">
            <p><a href="<?php echo $post['lien']; ?>" ><?php echo $post['titre']; ?></a></p>
            <p><?php echo BY.$post['auteur']; ?> <?php echo THE.$post['date']; ?></p>
            <a href="<?php echo $post['lien']; ?>"><div></div><span>Lire</span><div></div></a>
        </div>
        <?php endforeach; ?>
    </article>
</section>
<?php endif; ?>