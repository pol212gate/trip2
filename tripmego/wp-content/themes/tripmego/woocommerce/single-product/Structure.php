Structure






<div class="div_tour_detail"> <!-- woocommerce .php -->
    <div class="main_section_detail-trip" id="posts"> <!-- woocommerce .php -->
      	<div class="container_detail w-container"><!-- woocommerce .php -->

          <div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="summary entry-summary">
          inside content-single-product.php // woocommerce
          import Hook

      		<div class="div_box_right">
              product-image.php
      		</div>

      		<div class="div_block_top w-clearfix">
              title.php
      		</div>

      		<div class="div_box_left2"> 
            content-tour-meta.php // wootour
      		</div> 
          <div class="div_box_left2"> short-description.php  <?PHP open_div_box_left(); ?>
            short-description.php 
          </div> meta.php <?PHP close_div_box_left(); ?>
          End hook
          
          <div class="div_booking"> content-single-product.php</div>

          <div class="div_block_question">content-single-product.php</div>
          
          <div class="div_share">content-single-product.php</div>

          <div class="div_box_left3"> content-tour-acco.php <---woo tour </div>


          </div><!-- .summary -->
        </div><!-- #product-<?php the_ID(); ?> -->
      	</div><!-- div_tour_detail-->
    </div><!--main_section_detail-trip -->
</div>	<!--container_detail w-container -->