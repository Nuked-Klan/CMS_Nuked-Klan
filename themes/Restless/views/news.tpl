<article>
    <aside>
        <a href="{{newsCatLink}}" title="{{newsCatName}}" class="RL_bgCover" style="background-image:url({{newsImage}})">
            <img src="{{newsImage}}" alt="" />
        </a>
    </aside>
    <div>
        <header>
            <h2>{{newsTitle}}</h2>
            <a href="{{newsLink}}">{{newsNbComments}}</a>
        </header>
        <div>
            {{newsText}}
        </div>
    </div>
    <footer>
        <div>
            <span>Par <a href="index.php?file=Members&op=detail&autor={{newsAuthor}}">{{newsAuthor}}</a>&nbsp;le&nbsp;{{newsDate}}</span>
        </div>
    </footer>
</article>