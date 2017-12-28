<?php
/**
 * The template for displaying the index page
 *
 *
 * @package tripmego
 * @since tripmego Theme 1.0
 */

?>
<?PHP get_header(); ?>
      <div class="div_tour_detail">
        
         
 
    <div class="tab_menu w-container"></div>

       <div class="container_detail w-container">


    

        <div class="search">
          <?PHP get_search_form(); ?>
        </div>

   <?PHP echo"index.php"; ?>
         <?php if ( have_posts() ) : ?>
            <?php while ( have_posts()) : the_post(); ?>
        
              <?PHP get_template_part('content',get_post_format() ); ?> <!-- content....php -->
             <!-- get_template_part('content'); original function-->

          <?php endwhile; ?>
          <?php endif; ?>

          <?php get_sidebar('sidebar'); ?>

<!-- query part books -->
          <?php //$args = array('post_type' => 'book',); ?> <!-- command 1 -->
            <?PHP //$wp_query = new wp_query($args); ?> <!-- command 2 -->

             <?php //if ( $wp_query->have_posts() ) : ?>
          <?php //while ( $wp_query->have_posts()) : $wp_query->the_post(); ?>


          <?php //the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">' , esc_url( get_permalink() ) ), '</a></h2>' ); ?>  
             <?php /* the_title(); */ ?> 
              
               <?PHP //the_content(); ?>

          <?php //endwhile; ?>
          <?php //endif; ?>

      </div>
  </div>

<?PHP get_footer(); ?>
