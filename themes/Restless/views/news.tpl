<article>
    <aside>
        <a href="{{newsCatLink}}" title="{{newsCatName}}" class="RL_bgCover" style="background-image:url({{newsImage}})">
            <img src="{{newsImage}}" alt="" />
        </a>
    </aside>
    <div>
        <header>
            <h2>{{newsTitle}}</h2>
            <a href="{{newsLink}}" title="{{*SEECOMMENT}}">{{newsNbComments}}</a>
        </header>
        <div>
            {{newsText}}
        </div>
    </div>
    <footer>
        <div>
            <span>{{*BY}}<a href="index.php?file=Members&op=detail&autor={{newsAuthor}}">{{newsAuthor}}</a> {{*THE}}{{newsDate}}</span>
        </div>
    </footer>
</article>