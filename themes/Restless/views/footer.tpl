<footer id="RL_footer">
    <div id="RL_separatorTopFooter"><!-- No content --></div>
    <?php new viewTpl('infosFooter'); ?>
    <section id="RL_footerContentLeft">
        <?php
            new viewTpl('whois');

            new viewTpl('footerNav');
        ?>
    </section><!--
    --><section id="RL_footerContentRight">
        <?php new viewTpl('sponsors'); ?>
    </section>
    <div id="RL_separatorBottomFooter"><!-- No Content --></div>
    <div class="RL_infosFooter" id="RL_bottomFooter">
        <?php new viewTpl('copyright'); ?>
        <div>
            <?php
                new viewTpl('permalinks');

                new viewTpl('copyleft');
            ?>
        </div>
    </div>
</footer>