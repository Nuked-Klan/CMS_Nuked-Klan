<nav id="RL_mainNav">
    <ul id="RL_mainMenu">
            @foreach(mainNavContent as row)<!-- Hack Inline block
            -->@include(navigation-item, row)<!-- Hack Inline block
        -->@endforeach
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
        @if(array_key_exists(1, {{GLOBALS.user}}))
            @include(userInfo)
        @else
            @include(login)
        @endif
    </div>
</nav>