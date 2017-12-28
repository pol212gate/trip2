 <?php end_div_id_main(); ?>

	<?php if(is_front_page() ) : ?> <!-- show at index.page -->
    <?php if(get_theme_mod('footer_callout_select') == "Yes" ) : ?>

        	<div class="footer-callout clearfix">
         		<div class="footer-callout-image">
         			<?PHP echo wp_get_attachment_image( get_theme_mod('footer_callout_image') );?>
            </div>
        		<div class="footer-callout-text">
        			<h3>
                  <a href="<?PHP echo get_permalink( get_theme_mod('footer_callout_link') ); ?>"><?php echo get_theme_mod('footer_callout_text', 'Footer Callout'); ?>
                  </a>
              </h3>
        			<p>
                <?php echo wpautop( get_theme_mod('footer_callout_textarea', 'Footer Callout Textarea') ); ?>
              </p>
        		</div> <!-- end footer-callout-text-->
        	</div> <!-- end footer-callout-clearfix-->

    <?php endif; ?>
  <?php endif; ?>
   
        <footer class="site-footer">
          <div class="footer-widgets clearfix">
        		<div class="footer-widget-area"></div>
                <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->


          <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->
              <div class="section_footer">
                  <div class="container-41 w-container">

                        <?PHP tripmego_footermenu1(); ?>
                        <?PHP tripmego_footermenu2(); ?>
                        <?PHP tripmego_footermenu3(); ?>
                        <div class="div_block_footer4 w-clearfix">
                            <div class="div-block-84">
                                <h2 class="heading-23">FOLLOW ME</h2>
                                <div class="div_block_inside_social w-clearfix">
                                  <?php echo do_shortcode('[Sassy_Social_Share ]') ?> <!-- social share // Saasy social share // Shortcode -->
                                </div>
                            </div>

                            <div class="div_box_text_subscribe">
                              <h5 class="heading-40">NEWS LETTER FOR</h5>
                                <div class="text-block-51">Special offer Promotion Special Deal</div>
                            </div>

                            <div class="form_subscribe w-form">
                                <form class="form-14" data-name="Email Form 3" id="email-form-3" name="email-form-3">
                                    <input class="text-field-15 w-input" data-name="Name" id="name" maxlength="256" name="name" placeholder="Enter your email" type="email">
                                      <input class="submit-button-14 w-button" data-wait="Please wait..." type="submit" value="Subscribe Now">
                                </form>
                                  <div class="w-form-done">
                                      <div>Thank you! Your submission has been received!</div>
                                  </div>
                                  <div class="w-form-fail">
                                      <div>Oops! Something went wrong while submitting the form.</div>
                                  </div>
                            </div>
                        </div> <!--END div_block_footer4 w-clearfix -->
                  </div> <!-- END container-41 w-container-->
                  <div class="div-block-81">
                      <h1 class="heading-38">tripmego</h1>
                      <div class="text-block-50">.asia</div>
                  </div>
              </div> <!-- END section_footer-->
          </div><!-- END footer-widgets clearfix -->
        </footer>



        <?PHP //if(is_product()){} else{ ?>
           <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js" type="text/javascript"></script>-->
            <!-- bug for navbar arrow-->
         <?PHP  //} ?>


 <!--this code for short description timeline -->
    <script type="text/javascript">
        
        jQuery(document).ready(function($){
          $('.toggler').click(function(){   
            var $this = $(this);              
            $this.next().fadeToggle(700);
          });
        });
        jQuery(document).ready(function($){
          $('.toggler2').click(function(){   
            var $this = $(this);              
            $this.next().fadeToggle(700);
          });
        });
        jQuery(document).ready(function($){
          $('.toggler3').click(function(){   
            var $this = $(this);              
            $this.next().fadeToggle(700);
          });
        });
    </script>
  <!--  End Script  -->

        <?php wp_footer(); ?>



  </body>
</html>
