<?php
$home_service_title = SM::smGetThemeOption("home_service_title", "");
$home_service_subtitle = SM::smGetThemeOption("home_service_subtitle", "");
$services = SM::smGetThemeOption("services", array());
$home_service_video_link = SM::smGetThemeOption("home_service_video_link", "");
?>
<?php if(count($services)>0): ?>
    <div id="content-wrap">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <!-- service 2 -->
                    <div class="services2">
                        <ul>
                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                $title = isset($service["title"]) ? $service["title"] : "";
                                $description = isset($service["description"]) ? $service["description"] : "";
                                $link = isset($service["link"]) ? $service["link"] : "";
                                $service_image = isset($service["service_image"]) ? $service["service_image"] : "";
                                ?>
                                <li class="col-xs-12 col-sm-6 col-md-6 services2-item">
                                    <div class="service-wapper">
                                        <div class="row">
                                            <?php if(empty(!$title)): ?>
                                                <div class="col-sm-6 image">
                                                    <div class="icon">
                                                        <img src="<?php echo SM::sm_get_the_src($service_image, 64, 64); ?>"
                                                             alt="<?php echo e($title); ?>">
                                                    </div>
                                                    <h3 class="title"><a href="<?php echo e($link); ?>"><?php echo e($title); ?></a></h3>
                                                </div>
                                            <?php endif; ?>
                                            <div class="col-sm-6 text">
                                                <?php echo strip_tags($description, "<br><span><i><b>"); ?>

                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>

                    <!-- ./service 2 -->
                </div>
                <?php if(empty(!$title)): ?>
                    <div class="col-md-6">
                        <div class="video-panel">
                            <iframe width="100%" height="360" src="<?php echo $home_service_video_link; ?>"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div> <!-- /.container -->
    </div>
<?php endif; ?>