<?php $__env->startSection('title', ''); ?>
<?php $__env->startSection('content'); ?>
    <!-- Home slideder-->
    <?php echo $__env->make('frontend.common.slider', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <!-- END Home slideder-->
    
    <?php echo $__env->make('frontend.products.latest_deals', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <!-- END latest deals-->
    <!---->
    <div class="content-page">
        <div class="container">
            <?php
            $countC = 0;
            ?>
            <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catKey => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <!-- featured category electronic -->
                <?php
             
                $subcategory_id = \App\Model\Common\Category::where('parent_id', $category->id)->get();
                                    $countProduct = $category->total_products;
                                    foreach ($subcategory_id as $item) {
                                        $countProduct += $item->total_products;
                                    }
                                      if($countProduct>0){
               
                $title = $category->title;
                $color = $category->color_code;
                ?>
                <?php echo $__env->make('frontend.inc.css.homeCss', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <div class="category-featured <?php echo e($title); ?>">
                    <nav class="navbar nav-menu show-brand">
                        <div class="container">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            
                            <div class="navbar-brand"><a href="<?php echo e(url('category/' . $category->slug)); ?>">
                                    <img alt="<?php echo e($title); ?>"
                                        src="<?php echo e(SM::sm_get_the_src($category->fav_icon, 20, 24)); ?>" /><?php echo e($title); ?>

                                </a>
                            </div>
                            <span class="toggle-menu"></span>
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <div class="collapse navbar-collapse">
                                <?php echo e(SM::productCollapse($category->id, $countC)); ?>


                            </div><!-- /.navbar-collapse -->
                        </div><!-- /.container-fluid -->
                        <div id="elevator-<?php echo e($catKey); ?>" class="floor-elevator">
                            <?php
                            if ($catKey == 0) {
                                $elevator_up = '#';
                                $catKey++;
                                $elevator_down = '#elevator-' . $catKey;
                            } else {
                                $catKey1 = $catKey - 1;
                                $catKey++;
                                $elevator_up = '#elevator-' . $catKey1;
                                $elevator_down = '#elevator-' . $catKey;
                                //                                $elevator_down = 'elevator-2' . $category->id;
                            }
                            ?>
                            <a href="<?php echo e($elevator_up); ?>" class="btn-elevator up fa fa-angle-up"></a>
                            <a href="<?php echo e($elevator_down); ?>" class="btn-elevator down fa fa-angle-down"></a>
                        </div>
                    </nav>
                    <div class="product-featured clearfix">
                        <div class="row">
                            <div class="col-sm-2 sub-category-wapper">
                                <ul class="sub-category-list">
                                    
                                    <?php
                                    $subcategories = SM::categoryBySubCategories($category->id);
                                    ?>
                                    <?php $__empty_2 = true; $__currentLoopData = $subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <li>
                                            <a
                                                href="<?php echo e(url('category/' . $subcategory->slug)); ?>"><?php echo e($subcategory->title); ?></a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="col-sm-10 col-right-tab">
                                <div class="product-featured-tab-content">
                                    <div class="tab-container">
                                        <?php
                                        $products = SM::categoryProducts($category->id);
                                        ?>
                                        <?php if(!empty($products)): ?>
                                            <div class="tab-panel active categoryByProduct_<?php echo e($countC); ?>"
                                                id="tab-<?php echo e($category->id); ?>">
                                                <div class="box-left">
                                                    <?php $__currentLoopData = $products->take(1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $first_product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="banner-img">
                                                            <a title="<?php echo e($first_product->title); ?>"
                                                                href="<?php echo e(url('product/' . $first_product->slug)); ?>"><img
                                                                    src="<?php echo e(SM::sm_get_the_src($first_product->image, 430, 450)); ?>"
                                                                    alt="<?php echo e($first_product->title); ?>"></a>
                                                        </div>
                                                        <div class="right-block">
                                                            <h5 class="product-name">
                                                                <a href="<?php echo e(url('product/' . $first_product->slug)); ?>">
                                                                    <?php echo e($first_product->title); ?>

                                                                </a>
                                                            </h5>
                                                            <div class="content_price">
                                                                <span
                                                                    class="price product-price"><?php echo e(SM::currency_price_value($first_product->regular_price)); ?></span>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                                <div class="box-right">
                                                    <ul class="product-list row">
                                                        <?php
                                                        $countP = 0;
                                                        $Products = $products;
                                                        ?>
                                                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php if($loop->first): ?>
                                                                <?php continue; ?>
                                                            <?php endif; ?>
                                                            <?php if($product->product_type == 2): ?>
                                                                <?php
                                                                $att_data = SM::getAttributeByProductId($product->id);
                                                                if (!empty($att_data->attribute_image)) {
                                                                    $attribute_image = $att_data->attribute_image;
                                                                } else {
                                                                    $attribute_image = $product->image;
                                                                }
                                                                ?>
                                                                <li class="col-sm-4">
                                                                    <div class="left-block">
                                                                        <a href="<?php echo e(url('product/' . $product->slug)); ?>">
                                                                            <img title="<?php echo e($product->title); ?>"
                                                                                class="img-responsive"
                                                                                alt="<?php echo e($product->title); ?>"
                                                                                src="<?php echo e(SM::sm_get_the_src($attribute_image, 186, 186)); ?>" />
                                                                        </a>
                                                                        <div class="quick-view">
                                                                            <?php echo SM::quickViewHtml($product->id, $product->slug); ?>
                                                                        </div>
                                                                        <div class="add-to-cart">
                                                                            <?php echo SM::addToCartButton($product->id, $product->regular_price, $product->sale_price); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-block">
                                                                        <h5 class="product-name">
                                                                            <a
                                                                                href="<?php echo e(url('product/' . $product->slug)); ?>">
                                                                                <?php echo e($product->title); ?>

                                                                            </a>
                                                                        </h5>
                                                                        <div class="content_price">
                                                                            <span
                                                                                class="price product-price"><?php echo e(SM::currency_price_value($att_data->attribute_price)); ?></span>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php else: ?>
                                                                <li class="col-sm-4">
                                                                    <div class="left-block">
                                                                        <a href="<?php echo e(url('product/' . $product->slug)); ?>">
                                                                            <img title="<?php echo e($product->title); ?>"
                                                                                class="img-responsive"
                                                                                alt="<?php echo e($product->title); ?>"
                                                                                src="<?php echo e(SM::sm_get_the_src($product->image, 186, 186)); ?>" />
                                                                        </a>
                                                                        <div class="quick-view">
                                                                            <?php echo SM::quickViewHtml($product->id, $product->slug); ?>
                                                                        </div>
                                                                        <div class="add-to-cart">
                                                                            <?php echo SM::addToCartButton($product->id, $product->regular_price, $product->sale_price); ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="right-block">
                                                                        <h5 class="product-name">
                                                                            <a
                                                                                href="<?php echo e(url('product/' . $product->slug)); ?>">
                                                                                <?php echo e($product->title); ?>

                                                                            </a>
                                                                        </h5>
                                                                        <div class="content_price">
                                                                            <?php if($product->sale_price > 0): ?>
                                                                                <span
                                                                                    class="price product-price"><?php echo e(SM::currency_price_value($product->sale_price)); ?></span>
                                                                                <span
                                                                                    class="price old-price"><?php echo e(SM::currency_price_value($product->regular_price)); ?></span>
                                                                            <?php else: ?>
                                                                                <span
                                                                                    class="price product-price"><?php echo e(SM::currency_price_value($product->regular_price)); ?></span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            <?php endif; ?>
                                                            <?php $countP++; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end featured category electronic-->
                <?php 
                $countC++ ;
            
                }
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                No data found
            <?php endif; ?>
        </div>
    </div>
    <?php echo $__env->make('frontend.inc.footer_top', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php $__env->startPush('script'); ?>
        <script type="text/javascript">
            $(document).ready(function() {
                <?php
                $maxC = count($categories);
                for ($i = 0; $i < $maxC; $i++) {
                ?>
                $('.common_selector_<?php echo $i; ?>').click(function() {
                    var category_id = $(this).data("category_id");
                    var type = $(this).data("type");
                    $.ajax({
                        type: 'get',
                        url: '<?php echo e(URL::route('categoryType_filter_by_product')); ?>',
                        data: {
                            category_id: category_id,
                            type: type,
                        },
                        success: function(data) {
                            $('.categoryByProduct_<?php echo $i; ?>').empty().html(data);
                        }
                    });
                });
                <?php } ?>
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>