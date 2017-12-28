


 archive.php

<?PHP get_header(); ?>

      <!--<div class="div_tour_detail">-->
        
         
 
    

       <!--<div class="container_detail w-container">-->
       
                    <h2>
                <?php if(is_category() ){
                            single_cat_title();
                            //echo"category";
                        }elseif(is_author() ){
                            the_post();//--------------loop
                                echo "Author archives :" .get_the_author(); //work with in loop
                            rewind_posts(); //--------------loop
                        }elseif(is_tag() ){
                            single_tag_title();
                            //echo"Tags";
                        }elseif(is_day() ){
                            echo"Daily Archives" . get_the_date();
                        }elseif(is_month() ){
                            echo"Monthly Archives" .get_the_date('F Y');
                        }elseif(is_year() ){
                            echo"Yearly Archive" .get_the_date('Y');
                        }else{
                            echo"others";
                }?>     
            </h2>
        <div class="wrapper">
             <div class="masonry6">

                <?php if ( have_posts() ) : ?>
                    <?php while ( have_posts()) : the_post(); ?>
                        
                        <div class="products">
                            <div class="item">
              <a href="<?PHP the_permalink(); ?>">
                        <?php //the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">' , esc_url( get_permalink() ) ), '</a></h2>' ); ?>  
                            <div class="frameimg">
                              <?PHP the_post_thumbnail(); ?>            
                            </div>
                            <div class="box_title">
                                <h2><?php the_title(); ?></h2> 
                                 <p class="date_content"><?PHP echo get_the_date(); ?><?PHP the_time(); ?></p>
                            </div> 
                              <div class="p_div_detail">
                                  <a href="<?PHP echo get_author_posts_url(get_the_author_meta('ID'), get_the_author_meta('user_nicename') );?>"></a>&nbsp
                                <?PHP echo get_the_excerpt(); ?> 
                              </div>
                             </a>
                             </div> 
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>

        </div><!--masonry5-->
</div><!--wrapper5-->


    <!-- Pagination -->
    <?php //the_posts_pagination( array( 'mid_size' => 2 ) ); ?>

   <!-- Archive Navigation -->  
   <div class="nav-post clearfix"> 
    <!--เช็คเงื่อนไขว่าโพสมีลิงค์ให้คลิกต่อหรือไม่ ถ้าไม่มีให้หยุดแสดง -->
        <?PHP if(get_previous_posts_link()) :?>
            <div class="nav-previous alignleft">
                <i class="fa fa-hand-o-left" aria-hidden="true"></i>
                <?PHP previous_posts_link('Previous Page'); ?>
            </div>
       <?php endif; ?>

       <?PHP if(get_next_posts_link()) :?>
            <div class="nav-next alignright">
                <?PHP next_posts_link('Next Page'); ?>
                <i class="fa fa-hand-o-right" aria-hidden="true"></i>
            </div>
        <?php endif; ?>
    </div>  


          <!--</div><!--div container_detail -->
  <!--</div><!--div_tour_detail -->

<?PHP get_footer(); ?>
