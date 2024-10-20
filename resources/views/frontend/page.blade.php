@extends('frontend.master')
@section("title",$page->page_title)
@section("content")
    <!-- page wapper-->
    <div class="columns-container">
        <div class="container" id="columns">
            <!-- breadcrumb -->
        @include('frontend.common.breadcrumb')
            <!-- ./breadcrumb -->
            <!-- page heading-->
            <h2 class="page-heading">
                <span class="page-heading-title2">{{ $page->page_title }}</span>
            </h2>
            <!-- ../page heading-->
            <div id="contact" class="page-content page-contact">
                <div id="message-box-conact"></div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12" id="contact_form_map">
                        <p>{!! stripslashes( $page->content ) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ./page wapper-->
@endsection
