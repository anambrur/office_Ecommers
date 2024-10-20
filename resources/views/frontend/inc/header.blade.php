<input type="hidden" name="_token" id="table_csrf_token" value="{!! csrf_token() !!}">

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
                <a href="#"><i class="fa fa-phone"> </i> Hotline : {{ $mobile }} </a>
            </div>
            <div class="top-bar-social">
                @empty(!SM::smGetThemeOption("social_facebook"))
                    <a target="_blank" href="{{ SM::smGetThemeOption("social_facebook") }}">
                        <i class="fa fa-facebook"></i>
                    </a>
                @endempty
                @empty(!SM::smGetThemeOption("social_twitter"))
                    <a href="{{ SM::smGetThemeOption("social_twitter") }}">
                        <i class="fa fa-twitter"></i>
                    </a>
                @endempty
                @empty(!SM::smGetThemeOption("social_google_plus"))
                    <a target="_blank" href="{{ SM::smGetThemeOption("social_google_plus") }}">
                        <i class="fa fa-google-plus"></i>
                    </a>
                @endempty
                @empty(!SM::smGetThemeOption("social_linkedin"))
                    <a target="_blank" href="{{ SM::smGetThemeOption("social_linkedin") }}">
                        <i class="fa fa-linkedin"></i>
                    </a>
                @endempty
                @empty(!SM::smGetThemeOption("social_github"))
                    <a href="{{ SM::smGetThemeOption("social_github") }}">
                        <i class="fa fa-github"></i>
                    </a>
                @endempty
                @empty(!SM::smGetThemeOption("social_pinterest"))
                    <a href="{{ SM::smGetThemeOption("social_pinterest") }}">
                        <i class="fa fa-pinterest-p"> </i>
                    </a>
                @endempty
                @empty(!SM::smGetThemeOption("social_youtube"))
                    <a target="_blank" href="{{ SM::smGetThemeOption("social_youtube") }}">
                        <i class="fa fa-youtube-play"></i>
                    </a>
                @endempty
                @empty(!SM::smGetThemeOption("social_instagram"))
                    <a target="_blank" href="{{ SM::smGetThemeOption("social_instagram") }}">
                        <i style="color: #ffffff;font-size: 16px;" class="fa fa-instagram"></i>
                    </a>
                @endempty
            </div>
            <div class="support-link">
                <a href="{{ url('/about') }}">About Us</a>
                <a href="{{ url('/contact') }}">Contact Us</a>
            </div>

            <div id="user-info-top" class="user-info pull-right">
                <div class="dropdown ">
                    <a class="current-open" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                       href="{{'/dashboard'}}"><span>{{ $fname }}</span></a>
                    <ul class="dropdown-menu mega_dropdown" role="menu">
                        @if(Auth::check())
                            <li><a href="{{'/dashboard'}}">Profile</a></li>
                            <li><a href="{{ url('/dashboard/wishlist') }}">Wishlists</a></li>
                            <li><a href="{{'/logout'}}">Logout</a></li>
                        @else
                            <li><a href="#" data-toggle="modal" data-target="#loginModal">Login</a></li>
                            <li><a data-toggle="modal" data-target="#loginModal" href="#">Wishlists</a></li>
                            <li><a href="{{ url('/compare') }}">Compare</a></li>
                        @endif
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
                <a href="{{ url('/') }}">
                    <img alt="{{ SM::get_setting_value('site_name') }}"
                         src="{{ SM::sm_get_the_src(SM::sm_get_site_logo(), 300, 63) }}"/>
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
                <a title="Compare" href="{{ url('/compare') }}" class="btn-compare">compare</a>
                <span class="notify notify-right compare_data">{{ Cart::instance('compare')->count() }}</span>
                @if(Auth::check())
                    <a title="My wishlist" href="{{ url('/dashboard/wishlist') }}" class="btn-heart">wishlist</a>
                @else
                    <a data-toggle="modal" data-target="#loginModal" title="My wishlist"
                       href="#" class="btn-heart">wishlist</a>
                @endif
                <div class="btn-cart header_cart_html" id="cart-block">

                    <a title="My cart" href="{{ url('/cart') }}">Cart</a>
                    <span class="notify notify-right cart_count">{{ Cart::instance('cart')->count() }}</span>
                    <div class="cart-block">
                        <div class="cart-block-content">
                            <h5 class="cart-title cart_count">{{ Cart::instance('cart')->count() }} Items in my
                                cart</h5>
                            <div class="cart-block-list">
                                <ul>
                                    <?php
                                    $items = Cart::instance('cart')->content();
                                    ?>
                                    @forelse($items as $id => $item)
                                        <li class="product-info removeCartTrLi">
                                            <div class="p-left">
                                                <a data-product_id="{{ $item->rowId }}" class="remove_link removeToCart"
                                                   title="Delete item"
                                                   href="javascript:void(0)"></a>
                                                <a href="{{ url('product/'.$item->options->slug) }}">
                                                    <img class="img-responsive"
                                                         src="{{ SM::sm_get_the_src($item->options->image, 100, 100) }}"
                                                         alt="{{ $item->name }}">
                                                </a>
                                            </div>
                                            <div class="p-right">
                                                <p class="p-name">{{ $item->name }}</p>
                                                <p class="p-rice">{{ SM::currency_price_value($item->price) }}</p>
                                                <p>Qty: {{ $item->qty }}</p>
                                                @if($item->options->sizename != '')
                                                    <p>Size: {{ $item->options->sizename }}</p>
                                                @endif
                                                @if($item->options->colorname != '')
                                                    <p>Color: {{ $item->options->colorname }}</p>
                                                @endif
                                            </div>
                                        </li>
                                    @empty
                                        <p>No data found!</p>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="toal-cart">
                                <span>Total</span>
                                <span class="toal-price pull-right">
                                    {{ SM::currency_price_value(Cart::instance('cart')->subTotal()) }}
                                </span>
                            </div>
                            <div class="cart-buttons">
                                <a href="{{ url('/cart') }}" class="btn-check-out">Checkout</a>
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
                <span class="notify notify-right">{{ Cart::instance('cart')->count() }}</span>
                <div class="shopping-cart-box-ontop-content"></div>
            </div>
        </div>
    </div>
</div>
