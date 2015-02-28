<?php if(count($this->blockForumContent) > 0): ?>
<section class="RL_blockRight">
    <header>
        <h3 class="RL_lastsPostTitle"><?php echo $this->blockForumTitle; ?></h3>
        <a class="RL_moreButton" href="index.php?file=Forum">Plus</a>
    </header>
    <article class="RL_lastsPost">
        <?php foreach($this->blockForumContent as $post): ?>
        <div class="RL_lastPost">
            <p><a href="<?php echo $post['lien']; ?>" ><?php echo $post['titre']; ?></a></p>
            <p>par <?php echo $post['auteur']; ?> <?php echo $post['date']; ?></p>
            <a href="<?php echo $post['lien']; ?>"><div></div><span>Lire</span><div></div></a>
        </div>
        <?php endforeach; ?>
    </article>
</section>
<?php endif; ?>