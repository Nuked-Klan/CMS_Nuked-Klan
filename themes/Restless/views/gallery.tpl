<section id="RL_gallery">
    <header>
        <h1 class="RL_modTitle"><?php echo $this->galleryTitle; ?></h1>
        <a class="RL_moreButton" href="index.php?file=Gallery">Plus</a>
    </header>
    <div>
        <?php foreach($this->galleryContent as $image): ?>
        <figure>
            <a href="<?php echo $image['link']; ?>"><img src="<?php echo $image['src']; ?>" alt="<?php echo $image['title']; ?>" /></a>
        </figure>
        <?php endforeach; ?>
    </div>
</section>