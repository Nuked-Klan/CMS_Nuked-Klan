<aside id="RL_blockTopMatch">
    <h2><?php echo $this->topMatchContent['title']; ?></h2>
    <figure>
        <figcaption><?php echo $this->topMatchContent['teamName']; ?></figcaption>
        <img src="<?php echo $this->topMatchContent['teamLogo']; ?>" alt="" />
    </figure>
    <div>
        <p class="RL_<?php echo $this->topMatchContent['scoreClass']; ?>" ><?php echo $this->topMatchContent['score']; ?></p>
    </div>
    <figure>
        <figcaption><?php echo $this->topMatchContent['opponentName']; ?></figcaption>
        <img src="<?php echo $this->topMatchContent['opponentLogo']; ?>" alt="" />
    </figure>
    <p>
        <strong>Date : </strong><?php echo $this->topMatchContent['date']; ?><br/>
        <strong>Maps : </strong><?php echo $this->topMatchContent['maps']; ?>
    </p>
    <a href="#" class="RL_button">D&eacute;tails</a>
</aside>
