<div id="RL_sliderWrapper" {{data}} data-width="{{elementWidth}}">
    @if({{nbSliderImages}} > 0)
        @if({{nbSliderImages}} > 1)
            <div id="RL_sliderNav">
                <a href="#" id="RL_sliderPrev"><span>Prev</span></a>
                <a href="#" id="RL_sliderNext"><span>Next</span></a>
            </div>
        @endif
        <div id="RL_slider" style="width:{{totalWidth}}px;left:{{initLeft}}px" data-left="{{initLeft}}">
            @foreach(sliderImages as image)<!--
                --><figure {{image.id}} {{image.current}}>
                    <figcaption>{{image.title}}</figcaption>
                    <a href="{{image.link}}" style="background-image:url({{image.src}})">
                        <img src="{{image.src}}" alt="{{image.title}}" />
                    </a>
                </figure><!--
         -->@endforeach
        </div>
    @endif
</div>
