<?php if(count($this->get('blockMatchContent')) > 0 && $this->get('blockMatchActive') == true): ?>
<section class="RL_blockRight">
    <header>
        <h3>{{blockMatchTitle}}</h3>
    </header>
    <article>
        <table class="RL_matchesTable" cellspacing="0" cellpadding="0">
            <?php foreach($this->get('blockMatchContent') as $match): ?>
            <tr>
                <td class="RL_matchesIcon">
                    <img src="<?php echo $match['icon']; ?>" alt="" />
                </td>
                <td class="RL_matchesFlag">
                    <img src="<?php echo $match['flag']; ?>" alt="" />
                </td>
                <td class="RL_matchesTeams">
                    <span><a href="<?php echo $match['teamLink']; ?>"><?php echo $match['teamName']; ?></a></span>
                    <span><a href="<?php echo $match['opponentLink']; ?>"><?php echo $match['opponentName']; ?></a></span>
                </td>
                <td class="RL_matchesScore">
                    <?php echo $match['score']; ?>
                </td>
                <td class="RL_matchesIconScore RL_matches<?php echo $match['scoreClass']; ?>">
                    <span><?php echo $match['scoreClass']; ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </article>
</section>
<?php endif; ?>