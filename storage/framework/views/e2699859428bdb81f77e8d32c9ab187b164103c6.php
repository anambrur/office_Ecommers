<?php
$product_title = SM::smGetThemeOption("product_title", "");
$product_subtitle = SM::smGetThemeOption("product_subtitle", "");
$productsCount = count($latestDeals);
?>
<?php if($productsCount>0): ?>
<div class="page-top">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <h2 class="page-heading">
                    
                    <span class="page-heading-title"><?php echo e($product_title); ?></span>
                </h2>
                <div class="latest-deals-product">
                    <span class="count-down-time2" style="display: none;">
                        <span class="icon-clock"></span>
                        <span>end in</span>
                        <span class="countdown-lastest" data-y="2016" data-m="7" data-d="1" data-h="00" data-i="00"
                              data-s="00"></span>
                    </span>
                    <ul class="product-list owl-carousel" data-dots="false" data-loop="true" data-nav="true"
                        data-margin="10" data-autoplayTimeout="1000" data-autoplayHoverPause="true"
                        data-responsive='{"0":{"items":1},"600":{"items":3},"1000":{"items":5}}'>
                        <?php $__empty_1 = true; $__currentLoopData = $latestDeals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $latestDeal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <?php if($latestDeal->product_type==2): ?>
                        <?php
                        $att_data = SM::getAttributeByProductId($latestDeal->id);
                        if (!empty($att_data->attribute_image)) {
                            $attribute_image = $att_data->attribute_image;
                        } else {
                            $attribute_image = $latestDeal->image;
                        }
                        ?>
                        <li>
                            <div class="left-block">
                                <a href="<?php echo e(url('product/'.$latestDeal->slug)); ?>">
                                    <img class="img-responsive" alt="<?php echo e($latestDeal->title); ?>"
                                         src="<?php echo e(SM::sm_get_the_src($attribute_image, 186, 186)); ?>"/>
                                </a>
                                <div class="quick-view">
                                    <?php echo SM::quickViewHtml($latestDeal->id, $latestDeal->slug); ?>
                                </div>
                                <div class="add-to-cart">
                                    <?php echo SM::addToCartButton($latestDeal->id, $latestDeal->regular_price, $latestDeal->sale_price); ?>
                                </div>
                                <?php if($latestDeal->sale_price>0): ?>
                                <div class="price-percent-reduction2">
                                    <?php echo e(SM::productDiscount($latestDeal->id)); ?>% OFF
                                </div>
                                <?php endif; ?>

                            </div>
                            <div class="right-block">
                                <h5 class="product-name"><a href="<?php echo e(url('product/'.$latestDeal->slug)); ?>"><?php echo e($latestDeal->title); ?></a></h5>
                                <div class="content_price">
                                    <?php
                                    if (!empty($att_data->attribute_price)) {
                                        ?>
                                        <span class="price product-price"><?php echo e(SM::currency_price_value($att_data->attribute_price)); ?></span>
                                    <?php } ?>

                                </div>
                            </div>
                        </li>
                        <?php else: ?>

                        <li>
                            <div class="left-block">
                                <a href="<?php echo e(url('product/'.$latestDeal->slug)); ?>">
                                    <img class="img-responsive" alt="<?php echo e($latestDeal->title); ?>"
                                         src="<?php echo e(SM::sm_get_the_src($latestDeal->image, 186, 186)); ?>"/>
                                </a>
                                <div class="quick-view">
                                    <?php echo SM::quickViewHtml($latestDeal->id, $latestDeal->slug); ?>
                                </div>
                                <div class="add-to-cart">
                                    <?php echo SM::addToCartButton($latestDeal->id, $latestDeal->regular_price, $latestDeal->sale_price); ?>
                                </div>
                                <?php if($latestDeal->sale_price>0): ?>
                                <div class="price-percent-reduction2">
                                    <?php echo e(SM::productDiscount($latestDeal->id)); ?>% OFF
                                </div>
                                <?php endif; ?>

                            </div>
                            <div class="right-block">
                                <h5 class="product-name"><a href="#"><?php echo e($latestDeal->title); ?></a></h5>
                                <div class="content_price">
                                    <?php if($latestDeal->sale_price>0): ?>
                                    <span class="price product-price"><?php echo e(SM::currency_price_value($latestDeal->sale_price)); ?></span>
                                    <span class="price old-price"><?php echo e(SM::currency_price_value($latestDeal->regular_price)); ?></span>
                                    <?php else: ?>
                                    <span class="price product-price"><?php echo e(SM::currency_price_value($latestDeal->regular_price)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        No data found!
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Baner bottom -->
        <?php
        $features = SM::smGetThemeOption("features", array());
        ?>
        <?php if(count($features)>0): ?>
        <div class="row banner-bottom">
            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-sm-6">
                <?php if(isset($feature["feature_image"])): ?>
                <div class="banner-boder-zoom">
                    <a href="<?php echo e($feature["feature_link"]); ?>">
                        <img alt="<?php echo e($feature["feature_title"]); ?>" class="img-responsive ads-style"
                             src="<?php echo SM::sm_get_the_src($feature["feature_image"], 683,163); ?>"/></a>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <!-- end banner bottom -->
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>