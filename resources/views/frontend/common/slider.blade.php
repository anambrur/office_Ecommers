@if (isset($sliders) && count($sliders) > 0)
    <?php
    $slider_change_autoplay = (int) SM::smGetThemeOption('slider_change_autoplay', 4);
    $slider_change_autoplay *= 3000;
    ?>

    <div id="home-slider">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 header-top-right">
                    <div class="homeslider">
                        <ul id="contenhomeslider">
                            @forelse($sliders as $slider)
                                <li>
                                    <img src="{{ SM::sm_get_the_src($slider->image, 1903, 901) }}"
                                        alt="{!! $slider->title !!}" title="{!! $slider->title !!}">

                                </li>
                            @empty
                                <img alt="slider Style" src="{{ asset('frontend/') }}/images/slider/slider2psd.jpg"
                                    title="slider style" />
                            @endforelse

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
