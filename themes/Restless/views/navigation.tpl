<nav id="RL_mainNav">
    <ul id="RL_mainMenu">
        <?php foreach($this->mainNavContent as $row):
            new viewTpl('navigation-item', $row);
        endforeach; ?>
   	</ul>
    <form id="RL_navSearch" method="POST" action="index.php?file=Search&op=mod_search">
        <input type="search" placeholder="Recherche..." name="main" />
        <input type="hidden" name="searchtype" value="matchand" />
        <input type="hidden" name="limit" value="50" />
        <input type="submit" value="" />
    </form>
</nav>
<nav id="RL_subNav">
    <div id="RL_login">
        <?php
            if (array_key_exists(1, $GLOBALS['user'])) {
                new viewTpl('userInfo');
            }
            else {
                new viewTpl('login');
            }
        ?>
    </div>
</nav>