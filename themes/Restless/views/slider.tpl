<div id="RL_sliderWrapper">
    @if({{nbSliderImages}} > 0)
        @if({{nbSliderImages}} > 1)
            <div id="RL_sliderNav">
                <a href="#" id="RL_sliderPrev"><span>Prev</span></a>
                <a href="#" id="RL_sliderNext"><span>Next</span></a>
            </div>
        @endif
        <div id="RL_slider" style="width:{{totalWidth}}px" data-left="0">
            @foreach(sliderImages as image)<!--
                --><figure>
                    <figcaption>{{image.title}}</figcaption>
                    <img src="{{image.src}}" alt="" />
                </figure><!--
         -->@endforeach
        </div>
    @endif
</div>
