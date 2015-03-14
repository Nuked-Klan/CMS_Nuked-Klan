<?php if(count($this->get('blockTeamContent')) > 0 && $this->get('blockTeamActive') == true): ?>
<section class="RL_blockRight">
    <header>
        <h3>{{blockTeamTitle}}</h3>
    </header>
    <article>
        <?php foreach($this->get('blockTeamContent') as $equipe): ?>
        <figure class="RL_roster">
            <img src="<?php echo $equipe['image']; ?>" alt="#" />
            <figcaption><?php echo $equipe['title']; ?></figcaption>
        </figure>
        <?php endforeach; ?>
    </article>
</section>
<?php endif; ?>