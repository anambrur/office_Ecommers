@extends('frontend.master')
@section('title', '')
@section('content')
    <!-- Home slideder-->
    @include('frontend.common.slider')
    <!-- END Home slideder-->
    {{-- latest deals --}}
    @include('frontend.products.latest_deals')
    <!-- END latest deals-->
    <!---->
    <div class="content-page">
        <div class="container">
            <?php
            $countC = 0;
            ?>
            @forelse($categories as $catKey => $category)
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
                @include('frontend.inc.css.homeCss')
                <div class="category-featured {{ $title }}">
                    <nav class="navbar nav-menu show-brand">
                        <div class="container">
                            <!-- Brand and toggle get grouped for better mobile display -->
                            
                            <div class="navbar-brand"><a href="{{ url('category/' . $category->slug) }}">
                                    <img alt="{{ $title }}"
                                        src="{{ SM::sm_get_the_src($category->fav_icon, 20, 24) }}" />{{ $title }}
                                </a>
                            </div>
                            <span class="toggle-menu"></span>
                            <!-- Collect the nav links, forms, and other content for toggling -->
                            <div class="collapse navbar-collapse">
                                {{ SM::productCollapse($category->id, $countC) }}

                            </div><!-- /.navbar-collapse -->
                        </div><!-- /.container-fluid -->
                        <div id="elevator-{{ $catKey }}" class="floor-elevator">
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
                            <a href="{{ $elevator_up }}" class="btn-elevator up fa fa-angle-up"></a>
                            <a href="{{ $elevator_down }}" class="btn-elevator down fa fa-angle-down"></a>
                        </div>
                    </nav>
                    <div class="product-featured clearfix">
                        <div class="row">
                            <div class="col-sm-2 sub-category-wapper">
                                <ul class="sub-category-list">
                                    {{-- <li style="border-bottom: 2px solid red"><a href="#">{{ $title }}</a></li> --}}
                                    <?php
                                    $subcategories = SM::categoryBySubCategories($category->id);
                                    ?>
                                    @forelse($subcategories as $subcategory)
                                        <li>
                                            <a
                                                href="{{ url('category/' . $subcategory->slug) }}">{{ $subcategory->title }}</a>
                                        </li>
                                    @empty
                                    @endforelse
                                </ul>
                            </div>
                            <div class="col-sm-10 col-right-tab">
                                <div class="product-featured-tab-content">
                                    <div class="tab-container">
                                        <?php
                                        $products = SM::categoryProducts($category->id);
                                        ?>
                                        @if (!empty($products))
                                            <div class="tab-panel active categoryByProduct_{{ $countC }}"
                                                id="tab-{{ $category->id }}">
                                                <div class="box-left">
                                                    @foreach ($products->take(1) as $first_product)
                                                        <div class="banner-img">
                                                            <a title="{{ $first_product->title }}"
                                                                href="{{ url('product/' . $first_product->slug) }}"><img
                                                                    src="{{ SM::sm_get_the_src($first_product->image, 430, 450) }}"
                                                                    alt="{{ $first_product->title }}"></a>
                                                        </div>
                                                        <div class="right-block">
                                                            <h5 class="product-name">
                                                                <a href="{{ url('product/' . $first_product->slug) }}">
                                                                    {{ $first_product->title }}
                                                                </a>
                                                            </h5>
                                                            <div class="content_price">
                                                                <span
                                                                    class="price product-price">{{ SM::currency_price_value($first_product->regular_price) }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="box-right">
                                                    <ul class="product-list row">
                                                        <?php
                                                        $countP = 0;
                                                        $Products = $products;
                                                        ?>
                                                        @foreach ($products as $product)
                                                            @if ($loop->first)
                                                                @continue
                                                            @endif
                                                            @if ($product->product_type == 2)
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
                                                                        <a href="{{ url('product/' . $product->slug) }}">
                                                                            <img title="{{ $product->title }}"
                                                                                class="img-responsive"
                                                                                alt="{{ $product->title }}"
                                                                                src="{{ SM::sm_get_the_src($attribute_image, 186, 186) }}" />
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
                                                                                href="{{ url('product/' . $product->slug) }}">
                                                                                {{ $product->title }}
                                                                            </a>
                                                                        </h5>
                                                                        <div class="content_price">
                                                                            <span
                                                                                class="price product-price">{{ SM::currency_price_value($att_data->attribute_price) }}</span>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @else
                                                                <li class="col-sm-4">
                                                                    <div class="left-block">
                                                                        <a href="{{ url('product/' . $product->slug) }}">
                                                                            <img title="{{ $product->title }}"
                                                                                class="img-responsive"
                                                                                alt="{{ $product->title }}"
                                                                                src="{{ SM::sm_get_the_src($product->image, 186, 186) }}" />
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
                                                                                href="{{ url('product/' . $product->slug) }}">
                                                                                {{ $product->title }}
                                                                            </a>
                                                                        </h5>
                                                                        <div class="content_price">
                                                                            @if ($product->sale_price > 0)
                                                                                <span
                                                                                    class="price product-price">{{ SM::currency_price_value($product->sale_price) }}</span>
                                                                                <span
                                                                                    class="price old-price">{{ SM::currency_price_value($product->regular_price) }}</span>
                                                                            @else
                                                                                <span
                                                                                    class="price product-price">{{ SM::currency_price_value($product->regular_price) }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                            <?php $countP++; ?>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
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
            @empty
                No data found
            @endforelse
        </div>
    </div>
    @include('frontend.inc.footer_top')
    @push('script')
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
                        url: '{{ URL::route('categoryType_filter_by_product') }}',
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
    @endpush
@endsection
