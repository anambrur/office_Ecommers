@extends('frontend.master')
@section('title', 'Contact')
@section('content')
    <!-- page wapper-->
    <?php
    $contact_form_title = SM::smGetThemeOption('contact_form_title');
    $contact_title = SM::smGetThemeOption('contact_title');
    $contact_subtitle = SM::smGetThemeOption('contact_subtitle');
    $contact_des_title = SM::smGetThemeOption('contact_des_title');
    $contact_description = SM::smGetThemeOption('contact_description');
    $title = SM::smGetThemeOption('contact_banner_title');
    $subtitle = SM::smGetThemeOption('contact_banner_subtitle');
    $bannerImage = SM::smGetThemeOption('contact_banner_image');
    
    $mobile = SM::get_setting_value('mobile');
    $email = SM::get_setting_value('email');
    $address = SM::get_setting_value('address');
    ?>
    <div class="columns-container">
        <div class="container" id="columns">
            <!-- breadcrumb -->
            @include('frontend.common.breadcrumb')
            <!-- ./breadcrumb -->
            <!-- page heading-->
            <h2 class="page-heading">
                <span class="page-heading-title2">{{ $title }}</span>
            </h2>
            <!-- ../page heading-->
            <div id="contact" class="page-content page-contact">
                <div id="message-box-conact"></div>
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="page-subheading">CONTACT FORM</h3>
                        <div class="contact-form-box">
                            {!! Form::open(['method' => 'post', 'action' => 'Front\HomeController@send_mail', 'id' => 'contactMail']) !!}
                            <div class="form-selector">
                                <label>Name</label>
                                <input type="text" class="form-control input-sm" name="fullname" placeholder="Your Name*"
                                    id="fullname" />
                            </div>
                            <div class="form-selector">
                                <label>Email address</label>
                                <input type="email" class="form-control input-sm" name="email"
                                    placeholder="Your E-mail*" id="contact_email" />
                            </div>
                            <div class="form-selector">
                                <label>Subject</label>
                                <input name="subject" class="form-control input-sm" type="text" placeholder="Subject">
                            </div>
                            <div class="form-selector">
                                <label>Message</label>
                                <textarea name="message" id="contact_message" placeholder="Your massage" class="form-control input-sm" rows="10"></textarea>
                            </div>
                            <div class="form-selector">
                                <button type="submit" id="btn-send-contact" class="btn">
                                    <span class="loading" style="display: none;"><i
                                            class="fa fa-refresh fa-spin"></i></span> Submit
                                </button>

                            </div>
                            <ul class="serviceMailErrors mailErrorList concatMailErrors">
                            </ul>
                            {!! Form::close() !!}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6" id="contact_form_map">
                        @empty(!$contact_des_title)
                            <h3 class="page-subheading">{{ $contact_des_title }}</h3>
                        @endempty
                        @empty(!$contact_description)
                            <p>{!! $contact_description !!}</p>
                        @endempty
                        <br />

                        <ul class="store_info">
                            <li><i class="fa fa-home"></i>{{ $address }}
                            </li>
                            <li><i class="fa fa-phone"></i><span>{{ $mobile }}</span></li>
                            <li><i class="fa fa-envelope"></i>Email: <span><a
                                        href="mailto:{{ $email }}">{{ $email }}</a></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ./page wapper-->
@endsection
