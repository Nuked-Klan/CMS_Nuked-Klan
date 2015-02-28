<section id="RL_blockUnikCenter">
    <?php foreach($this->blockContent as $item): ?>
    <figure>
        <img src="<?php echo $item['image']; ?>" alt="" />
        <figcaption>
            <h3><?php echo $item['title']; ?></h3>
            <p><?php echo $item['postedBy']; ?></p>
        </figcation>
        <a href="<?php echo $item['link']; ?>">+</a>
    </figure>
    <?php endforeach; ?>
</section>
