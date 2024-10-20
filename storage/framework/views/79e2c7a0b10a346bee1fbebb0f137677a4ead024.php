<style>
    /*----------------home page category by color-----*/
    .option5 .category-featured.<?php echo e($title); ?> .navbar-brand {
        background: <?php echo e($color); ?>;
    }

    .option5 .category-featured.<?php echo e($title); ?> .nav-menu .nav > li:hover a,
    .option5 .category-featured.<?php echo e($title); ?> .nav-menu .nav > li.active a {
        color: <?php echo e($color); ?>;
    }

    .option5 .category-featured.<?php echo e($title); ?> .nav-menu .navbar-collapse {
        background: #fff;
        border-bottom: 2px solid<?php echo e($color); ?>;
    }

    .option5 .category-featured.<?php echo e($title); ?> .nav-menu .nav > li > a:before {
        background: <?php echo e($color); ?>;
    }

    .option5 .category-featured.<?php echo e($title); ?> .nav-menu .nav > li:hover a,
    .option5 .category-featured.<?php echo e($title); ?> .nav-menu .nav > li.active a {
        color: <?php echo e($color); ?>;
    }

    .option5 .category-featured.<?php echo e($title); ?> .nav-menu .nav > li:hover a:after,
    .option5 .category-featured.<?php echo e($title); ?> .nav-menu .nav > li.active a:after {
        color: <?php echo e($color); ?>;
    }

    .option5 .category-featured.<?php echo e($title); ?> .product-list li .add-to-cart {
        /*background-color: rgba(102, 153, 0, 0.7);*/
        background: <?php echo e($color); ?>;
        /*color: rgba(102, 153, 0, 0.7);*/
    }

    .option5 .category-featured.<?php echo e($title); ?> .product-list li .add-to-cart:hover {
        background-color: <?php echo e($color); ?>;
    }
</style>
