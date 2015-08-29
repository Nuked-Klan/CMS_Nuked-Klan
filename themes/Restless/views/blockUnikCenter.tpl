@if({{nbArticles}} > 0 && {{blockArticleActive}} == true)
<section id="RL_blockUnikCenter" {{classUnikCenter}}>
    @foreach(blockArticleContent as item)
    <figure>
        <div class="RL_bgCover" style="background-image:url({{item.image}})"><!-- No Content --></div>
        <img src="{{item.image}}" alt="" />
        <figcaption>
            <h3>{{item.title}}</h3>
            <p>{{item.postedBy}}</p>
            <a href="{{item.link}}">+</a>
        </figcaption>
    </figure>
    @endforeach
</section>
@endif