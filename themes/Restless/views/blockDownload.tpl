@if({{nbDownloads}} > 0 && {{blockDownloadActive}} == true)
<div>
    <article>
        <header>
            <h1 class="RL_modTitle">{{blockDownloadTitle}}</h1>
            <a class="RL_moreButton" href="index.php?file=Download">Plus</a>
        </header>
        <div id="RL_blockDownload">
            @foreach(blockDownloadContent as item)
            <div>
                <a href="{{item.link}}"><span>Lien</span></a>
                <div>
                    <p>{{item.title}}</p>
                    <p>T&eacute;l&eacute;charg&eacute; {{item.count}} fois</p>
                </div>
            </div>
            @endforeach
        </div>
    </article>
</div><!-- Hack inline block
-->@endif