            
<?php //the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">' , esc_url( get_permalink() ) ), '</a></h2>' ); ?>  
   <?php /* the_title(); */ ?> 
       <?PHP echo get_the_date(); ?>
           <!-- Time -->
           <?PHP the_time(); ?>
            <!-- Author -->
            by 	<a href="<?PHP echo get_author_posts_url(get_the_author_meta('ID'), get_the_author_meta('user_nicename') );?>">
             <?php the_author(); ?>
               </a>&nbsp
             <!-- Catgory  -->
               <?PHP 
               	$categories = get_the_category();
               	$separator =',';
               	$output = '';

               if($categories){

               	foreach($categories as $category)
               			 		{
               			 			$output .= '<a href="'. get_category_link($category->term_id) .'">' . $category->cat_name . '</a>';
               			 			$output .= $separator;
               			 		}
               			 	
               			 	}
               			 		echo trim($output , $separator);
               			 ?>

               			 <?php if(is_archive() || is_search() ){ //---------------archive page search page
               			 			the_excerpt();
               			 }elseif(is_single() ){ //------------- single page
               			 	
               			 			the_post_thumbnail();
               			 			the_content();
               			 }else { ?>

							<?php  if($post->post_excerpt) {?>
               				<?PHP the_excerpt(); ?>
               					<a href="<?PHP the_permalink(); ?>"> Read more &raquo;</a>
              					 <?php 
              				} else { the_content(); }

               			 }?>

               			

              			 




























