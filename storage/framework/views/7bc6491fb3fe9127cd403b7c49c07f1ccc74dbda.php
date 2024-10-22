<!DOCTYPE html>
<html>
<?php echo $__env->make('frontend.inc.css', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<body class="option5">
    <!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      xfbml            : true,
      version          : 'v3.3'
    });
  };

  (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Your customer chat code -->
<div class="fb-customerchat"
  attribution=setup_tool
  page_id="566587163461634">
</div>
<!-- HEADER -->
<?php echo $__env->make('frontend.common.login_modal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('frontend.inc.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<!-- end header -->

<?php if(Session::has("w_message")): ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-warning margin-top-20">
                    <i class="fa fa-warning"></i> <?php echo e(Session::get("w_message")); ?>

                </div>
            </div>
        </div>
    </div>
    <?php
    Session::forget("w_message");
    Session::save();
    ?>
<?php endif; ?>
<?php if(Session::has("s_message")): ?>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-success margin-top-20">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <i class="fa fa-check"></i> <?php echo e(Session::get("s_message")); ?>

                </div>
            </div>
        </div>
    </div>
    <?php
    Session::forget("s_message");
    Session::save();
    ?>
<?php endif; ?>


<div class="search-html">
    <?php echo $__env->yieldContent('content'); ?>
</div>


<?php echo $__env->make('frontend.inc.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php echo $__env->make('frontend.inc.js', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</body>
</html>