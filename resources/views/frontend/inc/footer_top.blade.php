<?php
$home_service_title = SM::smGetThemeOption("home_service_title", "");
$home_service_subtitle = SM::smGetThemeOption("home_service_subtitle", "");
$services = SM::smGetThemeOption("services", array());
$home_service_video_link = SM::smGetThemeOption("home_service_video_link", "");
?>
@if(count($services)>0)
    <div id="content-wrap">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <!-- service 2 -->
                    <div class="services2">
                        <ul>
                            @foreach($services as $service)
                                <?php
                                $title = isset($service["title"]) ? $service["title"] : "";
                                $description = isset($service["description"]) ? $service["description"] : "";
                                $link = isset($service["link"]) ? $service["link"] : "";
                                $service_image = isset($service["service_image"]) ? $service["service_image"] : "";
                                ?>
                                <li class="col-xs-12 col-sm-6 col-md-6 services2-item">
                                    <div class="service-wapper">
                                        <div class="row">
                                            @empty(!$title)
                                                <div class="col-sm-6 image">
                                                    <div class="icon">
                                                        <img src="{!! SM::sm_get_the_src($service_image, 64, 64) !!}"
                                                             alt="{{ $title }}">
                                                    </div>
                                                    <h3 class="title"><a href="{{ $link }}">{{ $title }}</a></h3>
                                                </div>
                                            @endempty
                                            <div class="col-sm-6 text">
                                                {!! strip_tags($description, "<br><span><i><b>") !!}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- ./service 2 -->
                </div>
                @empty(!$title)
                    <div class="col-md-6">
                        <div class="video-panel">
                            <iframe width="100%" height="360" src="{!! $home_service_video_link !!}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    </div>
                @endempty
            </div>
        </div> <!-- /.container -->
    </div>
@endif