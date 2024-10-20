@if(count($relatedProduct) > 0)
    <div class="page-product-box">
        <h3 class="heading">Related Products</h3>
        <ul class="product-list owl-carousel" data-dots="false" data-loop="true" data-nav="true"
            data-margin="30" data-autoplayTimeout="1000" data-autoplayHoverPause="true"
            data-responsive='{"0":{"items":1},"600":{"items":3},"1000":{"items":3}}'>
            @foreach($relatedProduct as $rProductSingle)
                <li>
                    <div class="product-container">
                        <div class="left-block">
                            <a href="{{ url('product/'.$rProductSingle->slug) }}">
                                <img class="img-responsive" alt="{{ $rProductSingle->title }}"
                                     src="{!! SM::sm_get_the_src( $rProductSingle->image , 297, 297) !!}"/>
                            </a>
                            <div class="quick-view">
                                <?php echo SM::quickViewHtml($rProductSingle->id, $rProductSingle->slug);?>

                            </div>
                            <div class="add-to-cart">
                                <?php echo SM::addToCartButton($rProductSingle->id, $rProductSingle->regular_price, $rProductSingle->sale_price);?>

                            </div>
                            @if($rProductSingle->sale_price>0)
                                <div class="price-percent-reduction2">
                                    {{ SM::productDiscount($rProductSingle->id) }}% OFF
                                </div>
                            @endif
                        </div>
                        <div class="right-block">
                            <h5 class="product-name"><a
                                        href="{{ url('product/'.$rProductSingle->slug) }}">{{ $rProductSingle->title }}</a>
                            </h5>
                            <div class="product-star">
                                <?php echo SM::product_review($rProductSingle->id); ?>
                            </div>
                            <div class="content_price">
                                @if($rProductSingle->sale_price>0)
                                    <span class="price product-price">{{ SM::product_price($rProductSingle->sale_price) }}</span>
                                    <span class="price old-price">{{ SM::product_price($rProductSingle->regular_price) }}</span>
                                @else
                                    <span class="price product-price">{{ SM::product_price($rProductSingle->regular_price) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif