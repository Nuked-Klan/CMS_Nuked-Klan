<section class="RL_blockRight">
    <header>
        <h3><?php echo $this->blockEquipeTitle; ?></h3>
    </header>
    <article>
        <?php foreach($this->blockEquipeContent as $equipe): ?>
        <figure class="RL_roster">
            <img src="<?php echo $equipe['image']; ?>" alt="#" />
            <figcaption><?php echo $equipe['title']; ?></figcaption>
        </figure>
        <?php endforeach; ?>
    </article>
</section>