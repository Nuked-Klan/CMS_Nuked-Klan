<footer id="RL_footer">
    <div id="RL_separatorTopFooter"><!-- No content --></div>
    @include(infosFooter)
    <section id="RL_footerContentLeft">
        @include(whois)

        @include(footerNav)
    </section><!--
    --><section id="RL_footerContentRight">
        @include(sponsors)
    </section>
    <div id="RL_separatorBottomFooter"><!-- No Content --></div>
    <div class="RL_infosFooter" id="RL_bottomFooter">
        @include(copyright)
        <div>
            @include(permalinks)

            @include(copyleft)
        </div>
    </div>
</footer>