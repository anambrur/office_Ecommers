<input type="hidden" name="_token" id="table_csrf_token" value="<?php echo csrf_token(); ?>">

<div id="header" class="header">
    <div class="top-header">
        <div class="container">
            <?php
            $mobile = SM::get_setting_value('mobile');
            $email = SM::get_setting_value('email');
            $address = SM::get_setting_value('address');
            $country = SM::get_setting_value('country');
            if (Auth::check()) {

                $blogAuthor = Auth::user();
                $fname = $blogAuthor->firstname . " " . $blogAuthor->lastname;
                $fname = trim($fname) != '' ? $fname : $blogAuthor->username;
            } else {
                $fname = 'My Account';
                $logonMoadal = 'data-toggle="modal" data-target="#loginModal"';
            }
            ?>
            <div class="top-bar-social top-hotline">
                <a href="#"><i class="fa fa-phone"> </i> Hotline : <?php echo e($mobile); ?> </a>
            </div>
            <div class="top-bar-social">
                <?php if(empty(!SM::smGetThemeOption("social_facebook"))): ?>
                    <a target="_blank" href="<?php echo e(SM::smGetThemeOption("social_facebook")); ?>">
                        <i class="fa fa-facebook"></i>
                    </a>
                <?php endif; ?>
                <?php if(empty(!SM::smGetThemeOption("social_twitter"))): ?>
                    <a href="<?php echo e(SM::smGetThemeOption("social_twitter")); ?>">
                        <i class="fa fa-twitter"></i>
                    </a>
                <?php endif; ?>
                <?php if(empty(!SM::smGetThemeOption("social_google_plus"))): ?>
                    <a target="_blank" href="<?php echo e(SM::smGetThemeOption("social_google_plus")); ?>">
                        <i class="fa fa-google-plus"></i>
                    </a>
                <?php endif; ?>
                <?php if(empty(!SM::smGetThemeOption("social_linkedin"))): ?>
                    <a target="_blank" href="<?php echo e(SM::smGetThemeOption("social_linkedin")); ?>">
                        <i class="fa fa-linkedin"></i>
                    </a>
                <?php endif; ?>
                <?php if(empty(!SM::smGetThemeOption("social_github"))): ?>
                    <a href="<?php echo e(SM::smGetThemeOption("social_github")); ?>">
                        <i class="fa fa-github"></i>
                    </a>
                <?php endif; ?>
                <?php if(empty(!SM::smGetThemeOption("social_pinterest"))): ?>
                    <a href="<?php echo e(SM::smGetThemeOption("social_pinterest")); ?>">
                        <i class="fa fa-pinterest-p"> </i>
                    </a>
                <?php endif; ?>
                <?php if(empty(!SM::smGetThemeOption("social_youtube"))): ?>
                    <a target="_blank" href="<?php echo e(SM::smGetThemeOption("social_youtube")); ?>">
                        <i class="fa fa-youtube-play"></i>
                    </a>
                <?php endif; ?>
                <?php if(empty(!SM::smGetThemeOption("social_instagram"))): ?>
                    <a target="_blank" href="<?php echo e(SM::smGetThemeOption("social_instagram")); ?>">
                        <i style="color: #ffffff;font-size: 16px;" class="fa fa-instagram"></i>
                    </a>
                <?php endif; ?>
            </div>
            <div class="support-link">
                <a href="<?php echo e(url('/about')); ?>">About Us</a>
                <a href="<?php echo e(url('/contact')); ?>">Contact Us</a>
            </div>

            <div id="user-info-top" class="user-info pull-right">
                <div class="dropdown ">
                    <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                       href="<?php echo e('/dashboard'); ?>"><span><?php echo e($fname); ?></span></a>
                    <ul class="dropdown-menu mega_dropdown" role="menu">
                        <?php if(Auth::check()): ?>
                            <li><a href="<?php echo e('/dashboard'); ?>">Profile</a></li>
                            <li><a href="<?php echo e(url('/dashboard/wishlist')); ?>">Wishlists</a></li>
                            <li><a href="<?php echo e('/logout'); ?>">Logout</a></li>
                        <?php else: ?>
                            <li><a href="#" data-toggle="modal" data-target="#loginModal">Login</a></li>
                            <li><a data-toggle="modal" data-target="#loginModal" href="#">Wishlists</a></li>
                            <li><a href="<?php echo e(url('/compare')); ?>">Compare</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--/.top-header -->
    <!-- MAIN HEADER -->
    <div class="container main-header">
        <div class="row">

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 logo">
                <a href="<?php echo e(url('/')); ?>">
                    <img alt="<?php echo e(SM::get_setting_value('site_name')); ?>"
                         src="<?php echo e(SM::sm_get_the_src(SM::sm_get_site_logo(), 300, 63)); ?>"/>
                </a>
            </div>


            <div class="col-xs-4 col-sm-12 col-md-5 col-lg-4 header-search-box">
                <form class="form-inline">
                    <div class="form-group input-serach" id="main_search">
                        <input autocomplete="off" id="search_text" type="text" name="search_text"
                               placeholder="Keyword here...">
                    </div>
                    <button type="submit" class="pull-right btn-search"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 group-button-header">
                <a title="Compare" href="<?php echo e(url('/compare')); ?>" class="btn-compare">compare</a>
                <span class="notify notify-right compare_data"><?php echo e(Cart::instance('compare')->count()); ?></span>
                <?php if(Auth::check()): ?>
                    <a title="My wishlist" href="<?php echo e(url('/dashboard/wishlist')); ?>" class="btn-heart">wishlist</a>
                <?php else: ?>
                    <a data-toggle="modal" data-target="#loginModal" title="My wishlist"
                       href="#" class="btn-heart">wishlist</a>
                <?php endif; ?>
                <div class="btn-cart header_cart_html" id="cart-block">

                    <a title="My cart" href="<?php echo e(url('/cart')); ?>">Cart</a>
                    <span class="notify notify-right cart_count"><?php echo e(Cart::instance('cart')->count()); ?></span>
                    <div class="cart-block">
                        <div class="cart-block-content">
                            <h5 class="cart-title cart_count"><?php echo e(Cart::instance('cart')->count()); ?> Items in my
                                cart</h5>
                            <div class="cart-block-list">
                                <ul>
                                    <?php
                                    $items = Cart::instance('cart')->content();
                                    ?>
                                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <li class="product-info removeCartTrLi">
                                            <div class="p-left">
                                                <a data-product_id="<?php echo e($item->rowId); ?>" class="remove_link removeToCart"
                                                   title="Delete item"
                                                   href="javascript:void(0)"></a>
                                                <a href="<?php echo e(url('product/'.$item->options->slug)); ?>">
                                                    <img class="img-responsive"
                                                         src="<?php echo e(SM::sm_get_the_src($item->options->image, 100, 100)); ?>"
                                                         alt="<?php echo e($item->name); ?>">
                                                </a>
                                            </div>
                                            <div class="p-right">
                                                <p class="p-name"><?php echo e($item->name); ?></p>
                                                <p class="p-rice"><?php echo e(SM::currency_price_value($item->price)); ?></p>
                                                <p>Qty: <?php echo e($item->qty); ?></p>
                                                <?php if($item->options->sizename != ''): ?>
                                                    <p>Size: <?php echo e($item->options->sizename); ?></p>
                                                <?php endif; ?>
                                                <?php if($item->options->colorname != ''): ?>
                                                    <p>Color: <?php echo e($item->options->colorname); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <p>No data found!</p>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="toal-cart">
                                <span>Total</span>
                                <span class="toal-price pull-right">
                                    <?php echo e(SM::currency_price_value(Cart::instance('cart')->subTotal())); ?>

                                </span>
                            </div>
                            <div class="cart-buttons">
                                <a href="<?php echo e(url('/cart')); ?>" class="btn-check-out">Checkout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END MANIN HEADER -->
    <div id="nav-top-menu" class="nav-top-menu">
        <div class="container">
            <div class="row">
                <div class="col-sm-3" id="box-vertical-megamenus">
                    <!--                    <div class="box-vertical-megamenus">-->
                    <!---->
                    <!--                    </div>-->
                </div>
                <div id="main-menu" class="col-sm-9 main-menu">
                    <nav class="navbar navbar-default">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                        data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                                    <i class="fa fa-bars"></i>
                                </button>
                                <a class="navbar-brand" href="#">MENU</a>
                            </div>
                            <div id="navbar" class="navbar-collapse collapse">
                                
                                <?php
                                //                                $menu = array(
                                //                                    'nav_wrapper' => 'ul',
                                //                                    'start_class' => 'nav navbar-nav',
                                //                                    'link_wrapper' => 'li',
                                //                                    'dropdown_class' => 'dropdown-menu container-fluid ',
                                //                                    'has_dropdown_wrapper_class' => 'dropdown',
                                //                                    'show' => TRUE
                                //                                );
                                //                                SM::sm_get_menu($menu);
                                //                                ?>
                                <?php
                                $menu = array(
                                    'nav_wrapper' => 'ul',
                                    'start_class' => 'nav navbar-nav',
                                    'link_wrapper' => 'li',
                                    'dropdown_class' => '',
                                    'subNavUlClass' => 'dropdown-menu mega_dropdown',
                                    'has_dropdown_wrapper_class' => 'dropdown',
                                    'show' => TRUE
                                );
                                SM::sm_get_menu($menu);
                                ?>
                            </div><!--/.nav-collapse -->
                        </div>
                    </nav>
                </div>
            </div>
            <!-- userinfo on top-->
            <div id="form-search-opntop">
            </div>
            <!-- userinfo on top-->
            <div id="user-info-opntop">
            </div>
            <!-- CART ICON ON MMENU -->
            <div class="cart_icon" id="shopping-cart-box-ontop">
                <i class="fa fa-shopping-cart"></i>
                <span class="notify notify-right"><?php echo e(Cart::instance('cart')->count()); ?></span>
                <div class="shopping-cart-box-ontop-content"></div>
            </div>
        </div>
    </div>
</div>
