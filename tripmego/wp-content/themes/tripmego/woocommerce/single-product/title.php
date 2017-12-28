<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
} ?>



<div class="div_block_top w-clearfix">
     
    <div class="link_pin w-inline-block" href="#">
        <?php echo do_shortcode('[ti_wishlists_addtowishlist]') ?> <!-- wishlist button add short code-->
    </div>
    <div class="h1_top_detail">
        <?PHP the_title( '<h1 class="product_title entry-title">', '</h1>' ); ?>
    </div>
    
    <div class="div_box_left1 w-clearfix">
        <div class="div_text_left">
            <div class="collection-list-wrapper-3 w-dyn-list">
                <div class="w-dyn-items">
                    <div class="collection-item w-dyn-item">
                        <h3 class="h3_title_top"><?PHP echo strtoupper($countrybase = get_field('country_base')); ?></h3>
                        &nbsp<i class="fa fa-plane" aria-hidden="true"></i>&nbsp
                        <h3 class="h3_title_top"><?PHP echo strtoupper($countrytour = get_field('country_tour')); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="div_pic_flag_left w-clearfix">
    	   <img class="image-119" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/<?php echo $countrytour; ?>.png" width="39">
        </div>
        <div class="div_text_right">
       
            <h6>
               <?PHP if(get_post_meta( get_the_ID(), '_text_field', true )) : ?> 
                 Code Tour :&nbsp<?PHP echo get_post_meta( get_the_ID(), '_text_field', true ); ?>
                <?PHP else : echo"No Code Tour"; ?>
                    <?php endif; ?>   
            </h6>
        </div>
    </div> 

 
    <div class="div_title_description w-clearfix"><i class="fa fa-calendar" aria-hidden="true"></i>
        <h3 class="h3_title_description">Travel Bookings</h3>
    </div>  
        <!--<div class="div_text_right">  -->
        <div class='div_text_left'>
            <?php
                if( have_rows('date_period') ):

                    // loop through the rows of data
                    while ( have_rows('date_period') ) : the_row();
                   
                    echo"<div class='block_period'>";
                        echo"<h6 class='date_start'><i class='fa fa-plane' aria-hidden='true'></i> &nbsp วันที่เดินทาง :&nbsp  ";  
                    // display a sub field value
                        the_sub_field('travel_dates_start');
                        echo"<i class='fa fa-arrows-h' aria-hidden='true'></i>";
                        the_sub_field('travel_dates_stop');
                        echo"</h6>";

                        echo"<h6 class='booking_before'><i class='fa fa-calendar-check-o' aria-hidden='true'></i> &nbsp";
                        echo"จองก่อนวันที่ : ";
                        the_sub_field('expire_dates');
                        echo"</h6>";
                    echo"</div>";

                    endwhile;
                else :
                    echo"No start date";
                    // no rows found
                endif;
                ?>

               <!-- <table>
                    <tr>
                        <th>Travel Period</th>
                        <th>Adult</th>
                        <th>Child with bed</th>
                        <th>Child no bed</th>
                        <th>Room</th>

                    </tr>-->
                    
                            <?php
                if( have_rows('date_period') ):

                    // loop through the rows of data
                    while ( have_rows('date_period') ) : the_row(); ?>
                   

                    <!-- display a sub field value -->
                       <tr> <th> <?PHP //the_sub_field('travel_dates_start'); ?></th>
                            <th> <?PHP //the_sub_field('adult_price'); ?></th>
                            <th> <?PHP //the_sub_field('child_with_bed'); ?></th>
                            <th> <?PHP //the_sub_field('child_no_bed'); ?></th>
                            <th> <?PHP //the_sub_field('single_room'); ?></th>
                       </tr>
                     
                    
                 

    
                       
<?PHP

                    endwhile;
                else :
                    echo"No start date";
                    // no rows found
                endif;
                ?>
            
            </table>

        </div>    
</div>








