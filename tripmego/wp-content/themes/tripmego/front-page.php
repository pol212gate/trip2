<?PHP 
if ( 'posts' == get_option( 'show_on_front' ) ) {
    include( get_home_template() );
} else {?>
    <!--include( get_page_template() );-->
<?PHP get_header(); ?>
  <div class="hero-section">
    <div class="background-video w-background-video" data-autoplay="data-autoplay" data-loop="data-loop" data-poster-url="https://daks2k3a4ib2z.cloudfront.net/59814eaae127e80001d7f704/5994032548ee8e0001959252_JUST JAPAN-poster-00001.jpg" data-video-urls="https://daks2k3a4ib2z.cloudfront.net/59814eaae127e80001d7f704/5994032548ee8e0001959252_JUST JAPAN-transcode.webm,https://daks2k3a4ib2z.cloudfront.net/59814eaae127e80001d7f704/5994032548ee8e0001959252_JUST JAPAN-transcode.mp4" data-wf-ignore="data-wf-ignore"></div>
    <div class="hero-content w-container">
      <img src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/whitelogo.png" width="186px">
      <img class="image-49" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/Group-34-Copy.png">
      <div class="hero-subheading" data-ix="hero-text-2">We travel the world in search of stories. Come along for the ride.</div>
      <div class="dropdown_select_country w-dropdown" data-delay="0">
                <?PHP //dynamic_sidebar('single_sidebar'); ?>
                
 <?php //echo do_shortcode('[searchandfilter id="6998"]')  ?> 


      </div>
      <div class="div-block-6">
        <a class="link-10" href="#">Best Destination</a>
      </div>
      <div class="div-block-51">
        <a class="w-inline-block" href="#">
          <img class="image-61" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/TH_flag.png">
        </a>
        <a class="w-inline-block" href="#">
          <img class="image-62" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/Jp_flag.png">
        </a>
        <a class="w-inline-block" href="#">
          <img class="image-63" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/Kr_flag.png">
        </a>
        <a class="w-inline-block" href="#">
          <img class="image-64" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/hk.png">
        </a>
        <a class="w-inline-block" href="#">
          <img class="image-65" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/taiwan.png">
        </a>
        <a class="w-inline-block" href="#">
          <img class="image-66" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/Ch_flag.png">
        </a>
        <a class="w-inline-block" href="#">
          <img class="image-67" src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/Vn_flag.png">
        </a>
      </div>
    </div>
  </div>
  <div class="wrapper1">
    <div class="tab_menu w-container">
      <h2 class="h2_head_title">Popular Trip</h2>
      <div class="div_subtitle"><p>เซ็กซี่ไฮแจ็คคีตปฏิภาณเทค สหัสวรรษกาญจนาภิเษกออสซี่ ออร์เดอร์เที่ยงวันเคสโฟนแตงโม ซิมโฟนีไลท์เลคเชอร์พล็อตโมเดิร์น ช็อค บอกซ์ฮัลโลวีนพงษ์แชมพู คอร์รัปชันอิออนเวิร์ครูบิคเซ็กส์ แฟรี่ สกรัมไมค์ สแตนเลสเย้วเซ็กซี่อะ พ</p></div>
    </div>
     <div class="masonry1">
      <?php echo do_shortcode('[products category="tour" limit="5" order="DESC"]')  ?> 
    </div>
      <div class="tab_menu_viewall">
      <a class="link_view_all w-clearfix w-inline-block" href="all-tours.html">
        <h6 class="h6_viewall">View all Popular Trip &nbsp <i class="fa fa-hand-o-right" aria-hidden="true"></i></h6>

      </a>
    </div>
  </div>
  <div class="wrapper2">
    <div class="tab_menu w-container">
      <h2 class="h2_head_title">Tours</h2>
    </div>
     <div class="masonry2">
    <?php echo do_shortcode('[products category="local experience" limit="4" ]')  ?> 
  </div>
    <div class="tab_menu_viewall">
      <a class="link_view_all w-clearfix w-inline-block" href="all-tours.html">
        <h6 class="h6_viewall">View all Tours &nbsp <i class="fa fa-hand-o-right" aria-hidden="true"></i></h6>

      </a>
    </div>
  </div>
  <div class="wrapper3">
    <div class="tab_menu w-container">
      <h2 class="h2_head_title">Destination</h2>
    </div>
    <div class="masonry3">
    <?php echo do_shortcode('[product_categories category="country" ids="53,54,55,59,58,56" ]')  ?> 
  </div>
      <div class="tab_menu_viewall">
      <a class="link_view_all w-clearfix w-inline-block" href="all-tours.html">
        <h6 class="h6_viewall">View all Destination  &nbsp <i class="fa fa-hand-o-right" aria-hidden="true"></i></h6>

      </a>
    </div>
  </div>

  <div class="wrapper4">
    <div class="tab_menu ">
      <h2 class="h2_head_title">Local Me</h2>
    </div>
    <div class="masonry4">
    <?php echo do_shortcode('[products category="tour" limit="4" ]')  ?> 
    </div>
    <div class="tab_menu_viewall">
      <a class="link_view_all w-clearfix w-inline-block" href="all-tours.html">
        <h6 class="h6_viewall">View all Local me &nbsp<i class="fa fa-hand-o-right" aria-hidden="true"></i></h6>
      </a>
    </div>
  </div>






        <div class="wrapper">
              <div class="tab_menu ">
      <h2 class="h2_head_title">Seasonal &amp; Festival</h2>
    </div>
             <div class="masonry6">
          <?php $args = array('post_type' => 'book',); ?> <!-- command 1 -->
            <?PHP $wp_query = new wp_query($args); ?> <!-- command 2 -->
             <?php if ( $wp_query->have_posts() ) : ?>
          <?php while ( $wp_query->have_posts()) : $wp_query->the_post(); ?>
                        
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

            <div class="tab_menu_viewall">
      <a class="link_view_all w-clearfix w-inline-block" href="all-tours.html">
        <h6 class="h6_viewall">View all Reviews &nbsp<i class="fa fa-hand-o-right" aria-hidden="true"></i></h6>
      </a>
    </div>
</div><!--wrapper5-->

  <div class="section-18">
    <h1 class="heading-42">How it work and Go !</h1>
    <div class="container-44 w-container">
      <p class="paragraph-26">Tripmego have amarket place for travel and tour</p>
      <div class="w-row">
        <div class="w-col w-col-3">
          <div class="div_howto">
            <img src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/f.png" width="39">
            <h4 class="heading-43">Select Tour or destination you want</h4>
          </div>
        </div>
        <div class="w-col w-col-3">
          <div class="div_howto">
            <img src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/bo.png" width="29">
            <h4 class="heading-43">Booking</h4>
          </div>
        </div>
        <div class="w-col w-col-3">
          <div class="div_howto">
            <img src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/d_1.png" width="39">
            <h4 class="heading-43">Payment Cash / Credit</h4>
          </div>
        </div>
        <div class="w-col w-col-3">
          <div class="div_howto">
            <img src="<?PHP bloginfo( 'template_directory' ); ?>/images/icon/go.png" width="39">
            <h4 class="heading-43">Go enjoy it</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="div_private_section">
    <div class="section_private2">
      <h1 class="heading-5">Make your Private trip</h1>
      <h3 class="heading-6">Go anywhere with family or friends and enjoy it.</h3>
      <div class="container-21 w-container">
        <a class="button-5 w-button" href="about.html" target="_blank">Quotation Now !</a>
      </div>
    </div>
  </div>


<?php ///get_sidebar(); ?>
<?PHP get_footer(); ?>


<?PHP }

?>