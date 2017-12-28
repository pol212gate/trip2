        
<?PHP get_header(); ?>

    
      <?php if(is_product()) { ?> 
        <div class="div_tour_detail">
          <div class="main_section_detail-trip" id="posts">
            <div class="container_detail w-container">
              
      <?php } else { ?>
        <div class="div_tour_detail">
          <div class="wrapper">

            <!--<div class="title-woo"><h1 class="title-woo-in"><?PHP //woocommerce_page_title(); ?></h1></div>-->
            

          <?php  } ?>
  <!--  woo.php -->


            
        <?PHP woocommerce_content(); ?>
    


      <?php if(is_product()) { ?> 

        </div>

      </div>
    </div>
      <?php } else { ?>
    </div>
      </div>

    <?php } ?>
<?PHP get_footer(); ?>

