<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>




<?PHP open_div_box_left(); ?> <!-- open tag live in : tab.php -->


  
        <div class="div_title_description w-clearfix"><i class="fa fa-book" aria-hidden="true"></i>
            <h3 class="h3_title_description">Trip description</h3>
    </div>
<div class="div_description w-clearfix">
    <div class="trip_box">
    
            <h6 ><i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp Duration</h6>  
            <?php if(get_field('duration')): ?>            
            <p><?php echo get_field('duration'); ?></p>        
            <?PHP else : ?><p>No Input</p>
    <?php endif; ?>   
    </div>
    <div class="trip_box">

      <h6><i class="fa fa-paper-plane-o" aria-hidden="true"></i>&nbsp Transport</h6> 
      <?php if(get_field('transport')): ?>           
          <?php while(has_sub_field('transport')): ?>        
            <p>
            <?PHP if(get_sub_field('plane')==2): ?>
              <?php echo the_sub_field('transport1'); echo"&nbsp |"; endif; ?> 
            <?PHP if(get_sub_field('bus')==2): ?>
              <?php echo the_sub_field('transport2'); echo"&nbsp |"; endif; ?> 
            <?PHP if(get_sub_field('train')==2): ?>
              <?php echo the_sub_field('transport3'); echo"&nbsp |"; endif; ?>
            <?PHP if(get_sub_field('car')==2): ?>
              <?php echo the_sub_field('transport4'); echo"&nbsp |"; endif; ?>
            <?PHP if(get_sub_field('bike')==2): ?>
              <?php echo the_sub_field('transport5'); echo"&nbsp |"; endif; ?>   
            </p>
          <?php endwhile; ?> 
        <?PHP else : ?><p>No Input</p>
    <?php endif; ?> 
    </div>

    <div class="trip_box">
            <h6><i class="fa fa-info" aria-hidden="true"></i>&nbsp Tour type</h6>
            <?php if(get_field('tour_type')): ?>  
              <p><?php echo get_field('tour_type'); ?></p>
            <?PHP else : ?><p>No Input</p>
            <?php endif; ?>  
    </div> 

    <div class="trip_box">
          
            <h6><i class="fa fa-users" aria-hidden="true"></i>&nbsp Group Size</h6>
            <?php if(get_field('group_size')): ?>  
                  <p><?php echo get_field('group_size'); ?></p>
              <?PHP else : ?><p>No Input</p>
            <?php endif; ?>  

    </div> 
    <div class="trip_box">
         
            <h6><i class="fa fa-map-marker" aria-hidden="true"></i>&nbsp Location</h6>
            <?php if(get_field('location')): ?>  
              <p><?php echo get_field('location'); ?></p>
             <?PHP else : ?><p>No Input</p>
     <?php endif; ?>       
    </div>
    <div class="trip_box">

            <h6><i class="fa fa-cutlery" aria-hidden="true"></i>&nbsp Foods</h6> 
                <?php if(get_field('food_in_tour')): ?>           
          <?php while(has_sub_field('food_in_tour')): ?>     
                  <p><?php echo get_sub_field('food_time'); ?> Times </p>
          <?php endwhile; ?> 
            <?PHP else : ?><p>No Input</p>
           <?php endif; ?> 
    </div>

</div>


    <div class="div_description">
      <div class="p_detail">
				<div class="woocommerce-product-details__short-description">
  			 		 
             <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt , 50); ?>
				 
        </div>
			</div>
    </div>


   <!-- Schedule -->


      <div class="div_title_description w-clearfix"><i class="fa fa-calendar-o" aria-hidden="true"></i>
      <h3 class="h3_title_description">Tour Schedule</h3></div>
    
  <!-- start Timeline-->

    <div class="div_spec w-clearfix">

                              
      <?php if(get_field('schedule')): ?>  
            <?PHP $count = count(get_field("schedule")); ?>
                             
                             
                 
                <?php while(has_sub_field('schedule')): ?> 

                     
                      <!--<div class="day_text1">-->
                        <?PHP echo"<button class='toggler'>";?>Day <?php the_sub_field('select_day'); ?>&nbsp<i class="fa fa-clock-o" aria-hidden="true"></i> </button>
                      <!--</div>-->

                          <?PHP echo"<div class='div_intime'>";?>
                           
                            <div class="container2">
                              <div class="ul">
                                <?php while(has_sub_field('add_time_in_day')): ?>  
                                <span class='number'>
                                      <span><i class="fa fa-bell-o" aria-hidden="true"></i>&nbsp<?php the_sub_field('time_in'); ?></span>
                                    </span>   
                                  <div class="li">
                                    <span></span>    
                                    <div>
                                      <div class='info'><?php the_sub_field('detail_trip'); ?></div> 
                                    </div>
                                     
                                  </div>
                                <?php endwhile; ?> 
                              </div><!--end code for timeline-->  
                            </div>
                          </div>
            
                <?php endwhile; ?>
      <?php endif; ?>
    </div>                  


<!-- end schedule-->
       <!-- hook part airline detail -->
      
              <div class="div_title_description w-clearfix">
                   <i class="fa fa-plane " aria-hidden="true" ></i>
                     <h3 class="h3_title_description">Airline Detail</h3>
                </div>
                <div class="div_spec w-clearfix">

               
                    <div class="trip_box">
                        <h6></h6>
                        <p>
                        <?PHP if(get_field('airline_detail')==1) : ?>
                         <img class="image-61" src="<?PHP bloginfo( 'template_directory' ); ?>/images/airline-logo/<?PHP echo get_field('airline_detail'); ?>.png">
                        </p>
                        <?PHP  else : echo "NO logo";  ?>
                        <?php  endif; ?>
                    </div>
                

               
                    <div class="trip_box">
                        <h6><i class="fa fa-plane" aria-hidden="true"></i></i>&nbsp Airline :</h6>
                        <?PHP if(get_field('airline_detail')==1): ?> <p>Air Asia X </p>
                        <?PHP elseif(get_field('airline_detail')==2): ?> <p>Nok Scoot </p>
                        <?PHP elseif(get_field('airline_detail')==3): echo"<p>Nok Scoot </p>"; ?>
                        <?PHP elseif(get_field('airline_detail')==4): echo"<p>Nok Scoot </p>"; ?>
                        <?PHP elseif(get_field('airline_detail')==5): echo"<p>Nok Scoot </p>"; ?>
                        <?PHP else : echo"<p>No Airline </p>"; ?>
                        <?php endif; ?> 
                    </div>

                    <div class="trip_box">
                        <h6><i class="fa fa-ticket" aria-hidden="true"></i>&nbsp Boarding :</h6>
                          <?php if(get_field('boarding')): ?>
                            <p><?PHP echo get_field('boarding'); ?></p>
                          <?PHP else : echo"<p>No Boarding select </p>"; ?>
                          <?php endif; ?>
                    </div>
                 
                    <div class="trip_box">
                         <h6><i class="fa fa-cutlery" aria-hidden="true"></i>&nbsp Food</h6>
                         <?php if(get_field('food')): ?>
                         <p><?PHP echo get_field('food'); ?></p>
                         <?PHP else : echo"<p>No food select </p>"; ?>
                          <?php endif; ?>
                    </div>

                    <div class="trip_box">
                        <h6><i class="fa fa-suitcase" aria-hidden="true"></i>&nbsp Bag Load :</h6>
                        <?php if(get_field('bag')): ?>
                        <p><?PHP echo get_field('bag'); ?></p>
                        <?PHP else : echo"<p>No Bag select </p>"; ?>
                          <?php endif; ?>
                    </div>
    
                    <div class="trip_box">
                      <h6><i class="fa fa-briefcase" aria-hidden="true"></i>&nbsp Hand Bag :</h6>
                       <?php if(get_field('handbag')): ?>
                       <p><?PHP echo get_field('handbag'); ?></p>
                       <?PHP else : echo"<p>No handBag select </p>"; ?>
                          <?php endif; ?>
                    </div>

              </div>
       
              <div class="div_description">
                  <div class="div_p_other">

                    <?php if(get_field('more_service')): ?>
                      <?php while(has_sub_field('more_service')): ?>
                          <p class="p_other"><?php the_sub_field('other_service'); ?></p>
                      <?php endwhile; ?>
                    <?php endif; ?>

                  </div>
              </div>
              <!-- end hook part airline detail -->
              <!-- Hook hetel detail -->

        <!--<div class="div_title_description w-clearfix"><i class="fa fa-building-o" aria-hidden="true"></i></i>
             <h3 class="h3_title_description">Hotel</h3>
        </div>
        <div class="div_spec w-clearfix">
            <?php if(get_field('hotel')): ?>
                  <?php while(has_sub_field('hotel')): ?>
                    <p class="p_other"><?php the_sub_field('name_hotel'); ?><?php the_sub_field('star_rating'); ?>
            <?PHP $service = get_sub_field('service_hotel'); ?>
                            <?php foreach( $service as $services ): ?>

                                  <?PHP if($services['value']==1): ?>
                                  
                                  <div class="div_icon_detail">
                    <i class="fa fa-briefcase fa-2x" aria-hidden="true"></i>
                    <h4 class="spec3">Hand bag </h4>
                  </div>
                                  <?php endif; ?>

                              <?php endforeach; ?>
                                    <?php endwhile; ?>

                    <?php endif; ?>


        </div>

        <!--<div class="div_description">
          <div class="div_p_other">
            
                      <p class="p_other"><?php the_sub_field('name_hotel'); ?><?php the_sub_field('star_rating'); ?>


                           
                          </p>
                

           </div>
        </div>-->
   <!-- ******************************end Hook hetel detail ******************************-->
                 <!-- Hook place detail -->
        <div class="div_title_description w-clearfix"><i class="fa fa-map-o" aria-hidden="true"></i>
             <h3 class="h3_title_description">Place</h3>
        </div>
              <div class="div_description">
                <div class="div_place">
                    <a class="link_place w-inline-block" href="#">
                        <h4 class="place_in_trip">Fuji sanq</h4>
                    </a>
                    <a class="link_place w-inline-block" href="#">
                        <h4 class="place_in_trip">Shibuya</h4>
                    </a>
                    <a class="link_place w-inline-block" href="#">
                        <h4 class="place_in_trip">Harajuku</h4>
                    </a>
                    <a class="link_place w-inline-block" href="#">
                        <h4 class="place_in_trip">Asakusa</h4>
                    </a>
                    <a class="link_place w-inline-block" href="#">
                        <h4 class="place_in_trip">Shinjuku</h4>
                    </a>
                    <a class="link_place w-inline-block" href="#">
                        <h4 class="place_in_trip">Tokyo disney land</h4>
                    </a>
                    <a class="link_place w-inline-block" href="#">
                        <h4 class="place_in_trip">Buffet Kani</h4>
                    </a>
                </div>
              </div> <!-- <div class="div_description"> -->
   <!-- ******************************end Hook place detail ******************************-->
   <!-- ******************************hook part service include ******************************-->
              <div class="div_title_description w-clearfix">
                   <i class="fa fa-hand-o-right" aria-hidden="true"></i>
                   <h3 class="h3_title_description">Service Include</h3>
              </div>

              <div class="div_description">
                  

                    <button class="toggler2">
                      <i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp
                        ค่าบริการนี้รวมอะไรบ้าง ? &nbsp
                      <i class="fa fa-check-circle-o" aria-hidden="true"></i> 
                    </button>
                    <div class="div_intime">
                    <?php if(get_field('service')): ?>
                      <?php while(has_sub_field('service')): ?>
                          <p class="p_other">-
                            <?php the_sub_field('detail_service'); ?>
                          </p>
                      <?php endwhile; ?>
                    <?php endif; ?>
                  </div>
                  
              </div>
    <!--****************************** end Hook Service include ******************************************-->
    <!-- ***************************** hook part service  not include ******************************-->
              <div class="div_title_description w-clearfix">
                   <i class="fa fa-hand-paper-o" aria-hidden="true"></i>
                   <h3 class="h3_title_description">Service Not Include</h3>
              </div>

              <div class="div_description">
                 

                    <button class='toggler3'>
                      <i class="fa fa-hand-paper-o" aria-hidden="true"></i>
                        ค่าบริการนี้ไม่รวมอะไรบ้าง ? &nbsp
                      <i class="fa fa-clock-o" aria-hidden="true"></i> 
                    </button>
                    <div class="div_intime">
                    <?php if(get_field('service_not_include')): ?>
                      <?php while(has_sub_field('service_not_include')): ?>
                          <p class="p_other"><?php the_sub_field('service_not_include_description'); ?></p>

                      <?php endwhile; ?>
                    <?php endif; ?>
                    </div>
          
              </div>



