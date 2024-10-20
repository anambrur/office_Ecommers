<?php if(isset($sliders) && count($sliders) > 0): ?>
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
                            <?php $__empty_1 = true; $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <li>
                                    <img src="<?php echo e(SM::sm_get_the_src($slider->image, 1903, 901)); ?>"
                                        alt="<?php echo $slider->title; ?>" title="<?php echo $slider->title; ?>">

                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <img alt="slider Style" src="<?php echo e(asset('frontend/')); ?>/images/slider/slider2psd.jpg"
                                    title="slider style" />
                            <?php endif; ?>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
