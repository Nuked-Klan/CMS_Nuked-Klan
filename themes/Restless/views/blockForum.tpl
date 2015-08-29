@if(count({{blockForumContent}}) > 0 && {{blockForumActive}} == true)
<section class="RL_blockRight">
    <header>
        <h3 class="RL_lastsPostTitle">{{blockForumTitle}}</h3>
        <a class="RL_moreButton" href="index.php?file=Forum">{{*MORE}}</a>
    </header>
    <article class="RL_lastsPost">
        @foreach(blockForumContent as post)
        <div class="RL_lastPost">
            <p><a href="{{post.lien}}" >{{post.titre}}</a></p>
            <p>{{*BY}} {{post.auteur}} {{*THE}} {{post.date}}</p>
            <a href="{{post.lien}}"><div></div><span>Lire</span><div></div></a>
        </div>
        @endforeach
    </article>
</section>
@endif