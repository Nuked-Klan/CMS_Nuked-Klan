<?php if($this->get('nbArticles') > 0 && $this->get('blockArticleActive') == true): ?>
<section id="RL_blockUnikCenter">
    <?php foreach($this->get('blockArticleContent') as $item): ?>
    <figure>
        <img src="<?php echo $item['image']; ?>" alt="" />
        <figcaption>
            <h3><?php echo $item['title']; ?></h3>
            <p><?php echo $item['postedBy']; ?></p>
            <a href="<?php echo $item['link']; ?>">+</a>
        </figcaption>
    </figure>
    <?php endforeach; ?>
</section>
<?php endif; ?>