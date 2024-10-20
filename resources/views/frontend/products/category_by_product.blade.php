@extends('frontend.master')
@section("title", $categoryInfo->title)
@section('content')
    @push('style')
        <style>
            #loading {
                text-align: center;
                background: url('loader.gif') no-repeat center;
                height: 150px;
            }
        </style>
    @endpush
    <!-- page wapper-->
    <div class="columns-container">
        <div class="container" id="columns">
            <!-- breadcrumb -->
        @include('frontend.common.breadcrumb')
        <!-- ./breadcrumb -->
            <!-- row -->
            <div class="row">
                <!-- Left colunm -->
            @include('frontend.products.product_sidebar')
            <!-- ./left colunm -->
                <!-- Center colunm-->
                <div class="center_column col-xs-12 col-sm-9" id="center_column">
                    <!-- category-slider -->
                    <div class="category-slider">
                        <img src="{{ SM::sm_get_the_src($categoryInfo->image, 1017, 336) }}"
                             alt="{{ $categoryInfo->title }}">

                    </div>
                    <!-- ./category-slider -->
                    <!-- view-product-list-->
                    <div id="view-product-list" class="view-product-list">
                        <h2 class="page-heading">
                            <span class="">{{ count($categoryInfo->products) }} items found in Category:{{ $categoryInfo->title }}</span>
                        </h2>
                        <ul class="display-product-option" style="width: 63px;!important;">
                            <li class="view-as-grid selected">
                                <span>grid</span>
                            </li>
                            <li class="view-as-list">
                                <span>list</span>
                            </li>
                        </ul>
                        <!-- PRODUCT LIST -->
                        <ul class="row product-list grid " id="ajax_view_product_list">
                        <!--@include('frontend.products.product_list_item', ['productLists'=>$products])-->
                        </ul>
                        <!-- ./PRODUCT LIST -->
                    </div>
                    <!-- ./view-product-list-->
                </div>
                <!-- ./ Center colunm -->
            </div>
            <!-- ./row-->
        </div>
    </div>
    <!-- ./page wapper-->
    @push('script')

    @endpush
@endsection