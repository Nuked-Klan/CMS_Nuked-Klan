@if({{nbComments}} > 0 && {{blockGuestbookActive}} == true)
<article>
    <header>
        <h1 class="RL_modTitle">{{blockGuestbookTitle}}</h1>
    </header>
    <div id="RL_commentsContent">
        @foreach(blockGuestbookContent as comment)
        <div class="commentsItem">
            <div>{{comment.text}}</div>
            <p><a href="#">{{comment.author}}</a>&nbsp;{{*THE}} {{comment.date}}</p>
        </div>
        @endforeach
    </div>
</article>
@endif