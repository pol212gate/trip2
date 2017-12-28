            
<!--content.php-->
<div class="wrapper-padding">
         <!--<div class="link_pin w-inline-block" href="#">
        <?php//echo do_shortcode('[ti_wishlists_addtowishlist]') ?> <!-- wishlist button add short code-->
    <!--</div>-->

        
        <div class="title-head">
          <!--<i class="fa fa-quote-left" aria-hidden="true"></i>-->
          <h1>
          <?php the_title(); ?> 
        </h1>
          <!--<i class="fa fa-quote-right" aria-hidden="true"></i>-->
        </div>
        <div class="writer">
            <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp<?PHP echo get_the_date(); ?>
            | <i class="fa fa-clock-o" aria-hidden="true">&nbsp</i><?PHP the_time(); ?>
            |&nbsp <i class="fa fa-pencil" aria-hidden="true"></i> &nbsp 
            <a class="writeby" href="<?PHP echo get_author_posts_url(get_the_author_meta('ID'), get_the_author_meta('user_nicename') );?>">
              <?php the_author(); ?>
                                  </a>&nbsp


        </div>
        <div class="share_topic">
       
            <div class="left_share">
                <i class="fa fa-share-alt share_icon" aria-hidden="true"></i>
                  <?php echo do_shortcode('[Sassy_Social_Share ]')  ?>
            </div> <!-- social share // Saasy social share // Shortcode -->
        <!--start view post -->
            <div class="entry-meta"><!--link with get_page_views($post_id) function.php -->
                    <h6 class="post_view">
                       <i class="fa fa-eye share_eye" aria-hidden="true"></i>
                          <?php 
                          // adding page views after Meta Information
                          $post_id = get_the_ID(); 
                          $pageviews = get_page_views($post_id); 
                          echo "$pageviews Views."; 
                          // end of views
                          ?>
                    </h6>
                
            </div><!-- .entry-meta -->
            <!--end view post -->
            
            
        </div>

        <?php //get_sidebar("single_sidebar"); ?>


       
        <!--<div class="content-excerpt"><?PHP //the_excerpt(); ?></div>  -->
        <div class="image-title"><?PHP the_post_thumbnail(); ?></div>    

          

               		<?php echo"<div class='content-content'>"; the_content();  echo"</div>"; ?>

          <div class="share_topic">
            <div class="left_share">
                <i class="fa fa-share-alt share_icon" aria-hidden="true"></i> 
                <?php echo do_shortcode('[Sassy_Social_Share ]')  ?>
            </div> <!-- social share // Saasy social share // Shortcode -->
          </div> 
          
          <div class="next_post">
              <?php 
                // แสดงรายการเขียน 5 รายการ
                $args = array(
                        'author'         => get_the_author_meta('ID'),
                        'posts_per_page' => 1
                );
                $AuthorPosts = new WP_Query( $args );

                if($AuthorPosts -> have_posts()) :
                  while($AuthorPosts ->have_posts() ) : $AuthorPosts -> the_post(); ?>
                  <p>
                    <a class="link_next_post" href="<?PHP the_permalink(); ?>">
                      <i class="fa fa-hand-o-right" aria-hidden="true"></i>&nbsp
                      <?PHP the_title(); ?>
                      
                    </a>
                  </p>

                <?PHP  endwhile;
                else :
                  
                endif;
                wp_reset_postdata();
              
              ?>
          </div>



      <?php if(is_single() ){ ?> 
      <!-- แสดงรายละเอียดคนเขียน-->
        <div class="author clearfix">
          <div class="author-image">
              <div class="cover-image">
                  <?php echo get_avatar( get_the_author_meta('ID'),60); ?>
              </div>
                <div class="profile-author"> <?php echo get_the_author_meta('nickname'); ?>
                  <?php echo get_the_author_meta('description'); ?>
                  <?php echo get_the_author_meta('first_name'); ?>
                  <?php echo get_the_author_meta('last_name'); ?>

                  <?PHP if(count_user_posts(get_the_author_meta('ID')) > 3) { ?> <!-- นับจำนวน Post ของ User -->
                      <!-- ลิงค์ไปที่หน้าของผู้เขียนคนนี้ -->
                    <div class="link-author">
                      <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>" class="btn-a">view all posts <i class="fa fa-book" aria-hidden="true"></i>
                      </a> 
                    </div>
                  <?PHP } ?>

                </div>
          </div>
        </div>

      <?PHP  } ?>


</div>

              			 




























