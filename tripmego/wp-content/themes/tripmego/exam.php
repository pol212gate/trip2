<?php if(is_front_page()):?>

<?PHP echo"<script src='https://code.jquery.com/jquery-1.10.2.js'></script>"; ?>  <!--For vdo -->
<?PHP elseif(is_page( 'test-create' )) :  ?>
<?PHP else : ?>
<?PHP echo"<script src='https://code.jquery.com/jquery-1.10.2.js'></script>"; ?>  <!--For vdo -->
<?PHP endif ; ?>




        <?php if(is_front_page()):?>

          <?PHP echo"<script src=";?><?PHP bloginfo( 'template_directory' );?><?PHP echo"/js/webflow.js' type='text/javascript'></script>"; ?>

<?PHP elseif(is_page( 'test-create' )) :  ?>
<?PHP else : ?>

<?PHP endif ; ?>




        
<?PHP get_header(); ?>
    
  <!-- Condition 1-->
    <?PHP if(is_page( 'confirm-payment' )) :  ?>
   <div class="div_tour_detail2">
      <div class="container_detail w-container">
                 <?php if ( have_posts() ) : ?>
          <?php while ( have_posts()) : the_post(); ?>
        

             <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">' , esc_url( get_permalink() ) ), '</a></h2>' ); ?>  
             <?php /* the_title(); */ ?> 
           
               <?PHP the_content(); ?>
     
          <?php endwhile; ?>
          <?php endif; ?>

              <?PHP echo"page.php" ?>
            
      </div>
    </div>
 <!-- Condition 2-->
   <?PHP elseif(is_page( 'test-create' )) :  ?>
   <div class="div_tour_detail2">
      <div class="container_detail w-container">

         <?php if ( have_posts() ) : ?>
          <?php while ( have_posts()) : the_post(); ?>
        

            <h2 class="head-title"> <?php// the_title(); ?></h2>
             <?php /* the_title(); */ ?> 
           
               <?PHP the_content(); ?>
     
          <?php endwhile; ?>
          <?php endif; ?>

              <?PHP echo"page.php" ?>
            
      </div>
    </div>
 <!-- Condition 3-->
    <?PHP else :?>
    <div class="div_tour_detail">
      <div class="container_detail w-container">

         <?php if ( have_posts() ) : ?>
          <?php while ( have_posts()) : the_post(); ?>
        

             <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">' , esc_url( get_permalink() ) ), '</a></h2>' ); ?>  
             <?php /* the_title(); */ ?> 
           
               <?PHP the_content(); ?>
     
          <?php endwhile; ?>
          <?php endif; ?>

              <?PHP echo"page.php" ?>
            
      </div>
    </div>

   <?php endif; ?> 

             
<?PHP get_footer(); ?>

