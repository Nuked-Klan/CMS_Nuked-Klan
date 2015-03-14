<footer id="RL_footer">
    <div id="RL_separatorTopFooter"><!-- No content --></div>
    <?php $GLOBALS['tpl']->render('infosFooter'); ?>
    <section id="RL_footerContentLeft">
        <?php
            $GLOBALS['tpl']->render('whois');

            $GLOBALS['tpl']->render('footerNav');
        ?>
    </section><!--
    --><section id="RL_footerContentRight">
        <?php $GLOBALS['tpl']->render('sponsors'); ?>
    </section>
    <div id="RL_separatorBottomFooter"><!-- No Content --></div>
    <div class="RL_infosFooter" id="RL_bottomFooter">
        <?php $GLOBALS['tpl']->render('copyright'); ?>
        <div>
            <?php
                $GLOBALS['tpl']->render('permalinks');

                $GLOBALS['tpl']->render('copyleft');
            ?>
        </div>
    </div>
</footer>