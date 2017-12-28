        
<?PHP get_header(); ?>
      <div class="div_tour_detail">
        
          <div class="container_detail w-container">
        <?PHP //echo"single.php" ?>

         <?php if ( have_posts() ) : ?>
          <?php while ( have_posts()) : the_post(); ?>
       
                  <?PHP get_template_part('content',get_post_format() ); ?> <!-- content.php -->

          <?php endwhile; ?>
          <?php endif; ?>

    <!-- Previous and Next link -->
    <div class="nav=post clearfix">
    <!--เช็คเงื่อนไขว่าโพสมีลิงค์ให้คลิกต่อหรือไม่ ถ้าไม่มีให้หยุดแสดง -->
    	<?PHP if(get_previous_post_link()) :?>
        	<div class="nav-previous alignleft">
       	 		<?PHP previous_post_link('%link'); ?>
       		</div>
       <?php endif; ?>

       <?PHP if(get_next_post_link()) :?>
       		<div class="nav-next alignright">
       			<?PHP next_post_link('%link'); ?>
        	</div>
        <?php endif; ?>
    </div>    
        <div class="comment-section">
        	<?PHP comments_template(); ?>

        </div>

        
                      </div>
      
    </div>


               <?php //get_sidebar(); ?>
<?PHP get_footer(); ?>

