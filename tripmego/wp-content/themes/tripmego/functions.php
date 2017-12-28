<?PHP
ob_start();
/*session_start();
set_time_limit(0);*/



//metabox
//require_once( get_template_directory() . '/inc/metabox.php');
//widget
//require_once( get_template_directory() . '/inc/widgets.php');
//settings.php
//require_once( get_template_directory() . '/inc/settings.php');
//options.php

add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );

//remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images',20);

add_action( 'init', 'wpcodex_add_excerpt_support_for_pages' );

function wpcodex_add_excerpt_support_for_pages() {
	add_post_type_support( 'post','page', 'excerpt' );

}



//This for fix JQMIGRATE: Migrate is installed
//add_action( 'wp_default_scripts', function( $scripts ) {
 //  if ( ! empty( $scripts->registered['jquery'] ) ) {
   //     $scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
    //}
//} );
//-------------------------End Fix
// jetpack stat to view display
function get_page_views($post_id) {

  if (function_exists('stats_get_csv')) {
  
    $args = array('days'=>-1, 'limit'=>-1, 'post_id'=>$post_id);
    $result = stats_get_csv('postviews', $args);
    $views = $result[0]['views'];

  } else {

    $views = 0;

  }
  return number_format_i18n($views);
}
//-------------------------------------

function tripmego_styles(){ 

	wp_enqueue_style( 'tripmego-style1', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'tripmego-style2', get_template_directory_uri() . '/normalize.css' );
	wp_enqueue_style( 'tripmego-style3', get_template_directory_uri() . '/tesheed.css' );
	wp_enqueue_style( 'tripmego-style4', get_template_directory_uri() . '/css/font-awesome.min.css' );

}
add_action('wp_enqueue_scripts' , 'tripmego_styles',100);



function tripmego_scripts(){

	wp_enqueue_script( 'googleapis', '//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js', array());

}

add_action('wp_enqueue_scripts' , 'tripmego_scripts');

function tripmego_setup(){
		//register a custom primary navigation menu
	register_nav_menus( array(

		'primary' 	=> 'Primary Menu',
		'footer'  	=> 'Footer Menu' ,
		'sidemenu1' => 'Side Menu1' ,
		'footer1' 	=> 'Footer Menu1',
		'footer2' 	=> 'Footer Menu2',
		'footer3' 	=> 'Footer Menu3',
    'profileimg'=> 'Profileimg',
	));
	add_theme_support('post-formats', array('aside','image','video','quote','link','gallery','chat','audio','status'));
	add_theme_support( 'html5', array( 'search-form' ) );
	
	add_theme_support( 'wc-product-gallery-zoom' );

add_theme_support( 'wc-product-gallery-lightbox' );

add_theme_support( 'wc-product-gallery-slider' );


//add theme support for document Title tag<title> </title>
//add_theme_support('title-tag');

}
add_action( 'after_setup_theme' , 'tripmego_setup');



/* Custom Post Type*/
/*. Book post type */

add_action( 'init', 'codex_book_init' );
/**
 * Register a book post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function codex_book_init() {
	$labels = array(
		'name'               => _x( 'Books', 'post type general name', 'your-plugin-textdomain' ),
		'singular_name'      => _x( 'Book', 'post type singular name', 'your-plugin-textdomain' ),
		'menu_name'          => _x( 'Books', 'admin menu', 'your-plugin-textdomain' ),
		'name_admin_bar'     => _x( 'Book', 'add new on admin bar', 'your-plugin-textdomain' ),
		'add_new'            => _x( 'Add New', 'book', 'your-plugin-textdomain' ),
		'add_new_item'       => __( 'Add New Book', 'your-plugin-textdomain' ),
		'new_item'           => __( 'New Book', 'your-plugin-textdomain' ),
		'edit_item'          => __( 'Edit Book', 'your-plugin-textdomain' ),
		'view_item'          => __( 'View Book', 'your-plugin-textdomain' ),
		'all_items'          => __( 'All Books', 'your-plugin-textdomain' ),
		'search_items'       => __( 'Search Books', 'your-plugin-textdomain' ),
		'parent_item_colon'  => __( 'Parent Books:', 'your-plugin-textdomain' ),
		'not_found'          => __( 'No books found.', 'your-plugin-textdomain' ),
		'not_found_in_trash' => __( 'No books found in Trash.', 'your-plugin-textdomain' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'your-plugin-textdomain' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'book' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'book', $args );
	/* add category in books */
	register_taxonomy('book_category', 'book', array('hierarchical' => true, 'label' => 'Books Categories', 'singular_name' => 'Category', "rewrite" => true, "query_var" => true));

}


function add_my_post_types_to_query( $query ) {
  if ( is_home() && $query->is_main_query() )
    $query->set( 'post_type', array( 'post', 'page', 'book' , 'product' ) );
  return $query;
}
add_action( 'pre_get_posts', 'add_my_post_types_to_query' );



function new_excerpt_length($length) {
return 100;
}
add_filter('excerpt_length', 'new_excerpt_length');





//------------------------------register sidebar admin panel
function chapter_widgets(){
		register_sidebar( array(
				'name' => 'sidebar',  //sidbar 1 call to use  dynamic_sidebar('sidebar');
				'id' => 'sidebar'
		));
		register_sidebar(array(
				'name' 				=> 'single_sidebar', //sidbar 2 call to use  dynamic_sidebar('single_sidebar');
				'id' 				=> 'single_sidebar',
				'before_widget' 	=> '<li id="foo_widget-4" class="foo widget widget_foo_widget">',
				'after_widget' 		=> '</li>'

		));
		register_sidebar(array(
				'name' => 'page_sidebar', //sidbar 3 call to use  dynamic_sidebar('page_sidebar');
				'id' => 'page_sidebar'
		));
		register_sidebar(array(
				'name' => 'footer_sidebar', //sidbar 3 call to use  dynamic_sidebar('page_sidebar');
				'id' => 'footer_sidebar'
		));
}
add_action('widgets_init','chapter_widgets');
//------------------------------end register sidebar admin panel



//custom Post type
function create_post_type() {
  register_post_type( 'acme_product',
    array(
      'labels' => array(
        'name' => __( 'Wholsale' ),
        'singular_name' => __( 'Wholesale' ),
     
        'add_new_item' => __('Add New Wholesale')
      ), 
      'public' => true,
      'has_archive' => true,
    )
  );
}
add_action( 'init', 'create_post_type' );

//[foobar]




//theme-customizer fix position HERE Not Move
/*
require_once( get_template_directory() . '/inc/theme-customizer.php');


function caption_shortcode( $atts, $content = null ) {
	return '<span class="caption">' . $content . '</span>';
}
add_shortcode( 'caption', 'caption_shortcode' );*/

//----------------------------------------Custom Fields Woocommerce
 //Display Custom Fields
/*add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );

// Save Custom Fields
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

function woo_add_custom_general_fields() {

  global $woocommerce, $post;
  
  echo '<div class="options_group">';
  
  // Custom fields will be created here...

  // Text Field
woocommerce_wp_text_input( 
	array( 
		'id'          => '_text_field', 
		'label'       => __( 'My Text Field', 'woocommerce' ), 
		'placeholder' => 'http://',
		'desc_tip'    => 'true',
		'description' => __( 'Enter the custom value here.', 'woocommerce' ) 
	// ----------------------------------------------
	)
);
  
  echo '</div>';
	
}


function woo_add_custom_general_fields_save( $post_id ){
	
	// Text Field
	$woocommerce_text_field = $_POST['_text_field'];
	
		update_post_meta( $post_id, '_text_field', esc_attr( $woocommerce_text_field ) );
		
    $number_field = $_POST['_number_field'];

        update_post_meta($post_id, '_number_field', esc_attr($number_field));
    
	
}
*/

/* add a custom tab to show user pages */
/*
add_filter('um_profile_tabs', 'pages_tab', 1000 );
function pages_tab( $tabs ) {
  $tabs['pages'] = array(
    'name' => 'Pages',
    'icon' => 'um-faicon-pencil',
    'custom' => true
  );  
  return $tabs;
}



/* Tell the tab what to display */
/*
add_action('um_profile_content_pages_default', 'um_profile_content_pages_default');
function um_profile_content_pages_default( $args ) {
    global $ultimatemember;
    $loop = $ultimatemember->query->make('post_type=page&posts_per_page=10&offset=0&author=' . um_profile_id() );
    while ($loop->have_posts()) { $loop->the_post(); $post_id = get_the_ID();
    ?>

        <div class="um-item">
            <div class="um-item-link"><i class="um-icon-ios-paper"></i><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
        </div>

    <?php
    }
}
*/

//-------------------------Start Fuction wholesale------------------------------------//


// First Register the Tab by hooking into the 'woocommerce_product_data_tabs' filter
add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab' );
function add_my_custom_product_data_tab( $product_data_tabs ) {
    $product_data_tabs['my-custom-tab'] = array(
        'label' => __( 'wholesale', 'woocommerce' ),
        'target' => 'my_custom_product_data',
        
    );
    return $product_data_tabs;
}



/** CSS To Add Custom tab Icon */
function wcpp_custom_style() {?>
<style>
#woocommerce-product-data ul.wc-tabs li.my-custom-tab_options a:before { font-family: WooCommerce; content: '\e006'; }
</style>
<?php 
}
add_action( 'admin_head', 'wcpp_custom_style' );

// functions you can call to output text boxes, select boxes, etc.
add_action('woocommerce_product_data_panels', 'woocom_custom_product_data_fields');

function woocom_custom_product_data_fields() {
    global $post;

    // Note the 'id' attribute needs to match the 'target' parameter set above
    ?> <div id = 'my_custom_product_data'
    class = 'panel woocommerce_options_panel' > <?php
        ?> <div class = 'options_group' > <?php
              // Text Field




  // Number Field
  woocommerce_wp_text_input(
    array(
      'id' => '_number_field',
      'label' => __( 'Group Size', 'woocommerce' ),
      'placeholder' => '',
      'description' => __( 'Enter the custom value here.', 'woocommerce' ),
      'type' => 'number',
      'custom_attributes' => array(
         'step' => 'any',
         'min' => '15'
      )
    )
  );
    woocommerce_wp_text_input(
    array(
      'id' => '_text_field',
      'label' => __( 'Code Tour', 'woocommerce' ),
      //'wrapper_class' => 'show_if_simple', //show_if_simple or show_if_variable
      'placeholder' => 'Custom text field',
      'desc_tip' => 'true',
      'description' => __( 'Enter the custom value here.', 'woocommerce' )
    )
  );

  // Checkbox
  woocommerce_wp_checkbox(
    array(
      'id' => '_checkbox',
      'label' => __('Custom Checkbox Field', 'woocommerce' ),
      'description' => __( 'Check me!', 'woocommerce' )
    )
  );

  // Select
  woocommerce_wp_select(
    array(
      'id' => '_select',
      'label' => __( 'Wholesale Name', 'woocommerce' ),
      'options' => array(
         'one' => __( 'Go Holiday', 'woocommerce' ),
         'two' => __( 'World Vacation', 'woocommerce' ),
        'three' => __( 'Zego', 'woocommerce' )
      )
    )
  );

  // Textarea
  woocommerce_wp_textarea_input(
     array(
       'id' => '_textarea',
       'label' => __( 'Custom Textarea', 'woocommerce' ),
       'placeholder' => '',
       'description' => __( 'Enter the value here.', 'woocommerce' )
     )
 );

    // Number Field
  woocommerce_wp_text_input(
    array(
      'id' => '_number_field2',
      'label' => __( 'Commission Tour', 'woocommerce' ),
      'placeholder' => '',
      'description' => __( 'Enter the custom value here.', 'woocommerce' ),
      'type' => 'number',
      'custom_attributes' => array(
         'step' => 'any',
         'min' => '15'
      )
    )
  );
        ?> </div>

    </div><?php
}


add_action( 'woocommerce_process_product_meta', 'woocom_save_proddata_custom_fields'  );
/** Hook callback function to save custom fields information */


function woocom_save_proddata_custom_fields($post_id) {
    // Save Text Field
    $text_field = $_POST['_text_field'];
    if (!empty($text_field)) {
        update_post_meta($post_id, '_text_field', esc_attr($text_field));
    }

    // Save Number Field
    $number_field = $_POST['_number_field'];
    if (!empty($number_field)) {
        update_post_meta($post_id, '_number_field', esc_attr($number_field));
    }
    // Save Number Field
    $number_field2 = $_POST['_number_field2'];
    if (!empty($number_field2)) {
        update_post_meta($post_id, '_number_field2', esc_attr($number_field2));
    }
    // Save Textarea
    $textarea = $_POST['_textarea'];
    if (!empty($textarea)) {
        update_post_meta($post_id, '_textarea', esc_html($textarea));
    }

    // Save Select
    $select = $_POST['_select'];
    if (!empty($select)) {
        update_post_meta($post_id, '_select', esc_attr($select));
    }

    // Save Checkbox
    $checkbox = isset($_POST['_checkbox']) ? 'yes' : 'no';
    update_post_meta($post_id, '_checkbox', $checkbox);

    // Save Hidden field
    $hidden = $_POST['_hidden_field'];
    if (!empty($hidden)) {
        update_post_meta($post_id, '_hidden_field', esc_attr($hidden));
    }
}



// You can uncomment the following line if you wish to use those fields for "Variable Product Type"
//add_action( 'woocommerce_process_product_meta_variable', 'woocom_save_proddata_custom_fields'  );

//--------------------------end wholesale--------------//






//---------------------Tour Detail---------------------//



// Next provide the corresponding tab content by hooking into the 'woocommerce_product_data_panels' action hook
// See https://github.com/woothemes/woocommerce/blob/master/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
// for more examples of tab content
// See https://github.com/woothemes/woocommerce/blob/master/includes/admin/wc-meta-box-functions.php for other built-in
// functions you can call to output text boxes, select boxes, etc.


//----------------------------end custom fields---------------------------------------------------

//----------------------------------BUG-----------------------------------------------

/*add_filter('woocommerce_product_tabs' , 'tripmego_new_product_tab_content');

function tripmego_new_product_tab( $tabs ){

$tabs['test_tab'] = array(
	'title' => __('New Product Tab' , 'woocommerce'),
	'priority' => 5,
	'callback' => 'tripmego_new_product_tab_content'
		);

	return $tabs;

}*/

//---------------------------------------------------------------------------------

/*
function tripmego_new_product_tab_content(){

global $post;
		
		$custom_tab_options = array(
			'title' => get_post_meta($post->ID, 'custom_tab_title', true),
			'content' => get_post_meta($post->ID, 'custom_tab_content', true),
		);
		?>

		<h2><?PHP echo $custom_tab_options['title']; ?></h2>
		<h4><?PHP echo $custom_tab_options['content']; ?></h4>
<?PHP
}
*/

/**
 * Custom Tabs for Product display
 * 
 * Outputs an extra tab to the default set of info tabs on the single product page.
 */
 /*
function custom_tab_options_tab() {
?>
	<li class="custom_tab"><a href="#custom_tab_data"><?php _e('Custom Tab', 'woothemes'); ?></a></li>
<?php
}
add_action('woocommerce_product_write_panel_tabs', 'custom_tab_options_tab'); 

*/
/**
 * Custom Tab Options
 * 
 * Provides the input fields and add/remove buttons for custom tabs on the single product page.
 */
/*
function custom_tab_options() {
	global $post;
	
	$custom_tab_options = array(
		'title' => get_post_meta($post->ID, 'custom_tab_title', true),
		'content' => get_post_meta($post->ID, 'custom_tab_content', true),
	);
	
?>
	<div id="custom_tab_data" class="panel woocommerce_options_panel">
		<div class="options_group">
			<p class="form-field">
				<?php woocommerce_wp_checkbox( array( 'id' => 'custom_tab_enabled', 'label' => __('Enable Custom Tab?', 'woothemes'), 'description' => __('Enable this option to enable the custom tab on the frontend.', 'woothemes') ) ); ?>
			</p>
		</div>
		
		<div class="options_group custom_tab_options">                								
			<p class="form-field">
				<label><?php _e('Custom Tab Title:', 'woothemes'); ?></label>
				<input type="text" size="5" name="custom_tab_title" value="<?php echo @$custom_tab_options['title']; ?>" placeholder="<?php _e('Enter your custom tab title', 'woothemes'); ?>" />
			</p>
			
			<p class="form-field">
				<?php _e('Custom Tab Content:', 'woothemes'); ?>
           	</p>
			
			<table class="form-table">
				<tr>
					<td>
						<textarea class="theEditor" rows="10" cols="40" name="custom_tab_content" placeholder="<?php _e('Enter your custom tab content', 'woothemes'); ?>"><?php echo @$custom_tab_options['content']; ?></textarea>
					</td>
				</tr>   
			</table>
        </div>	
	</div>
<?php
}
add_action('woocommerce_product_write_panels', 'custom_tab_options');
*/

/**
 * Process meta
 * 
 * Processes the custom tab options when a post is saved
 */
/*function process_product_meta_custom_tab( $post_id ) {
	update_post_meta( $post_id, 'custom_tab_enabled', ( isset($_POST['custom_tab_enabled']) && $_POST['custom_tab_enabled'] ) ? 'yes' : 'no' );
	update_post_meta( $post_id, 'custom_tab_title', $_POST['custom_tab_title']);
	update_post_meta( $post_id, 'custom_tab_content', $_POST['custom_tab_content']);
}
add_action('woocommerce_process_product_meta', 'process_product_meta_custom_tab');


/*
add_action('wp_head' , 'price_remove');
function price_remove(){
remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10);

}
add_action( 'woocommerce_single_product_summary' , 'woocommerce_template_single_price' , 25 );
*/
/*
add_action('wp_head' , 'rating_remove');
function rating_remove(){
remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating',5);

}*/

/*add_action('wp_head' , 'addcart_remove');
function addcart_remove(){
remove_action('woocommerce_after_shop_loop_item','woocommerce_template_loop_add_to_cart',10);

}
add_action('wp_head' , 'related_remove');
function related_remove(){
remove_action('woocommerce_after_single_product_summary','woocommerce_output_related_products',20);

}
/*
add_action('wp_head' , 'btncart_remove');
function btncart_remove(){
remove_action('woocommerce_single_product_summary','woocommerce_template_single_add_to_cart',30);

}*/
function open_div_id_main(){
	echo"<div id='main'>"; //open main
}
function end_div_id_main(){
	echo"</div>"; // close main
}

/*-------------------------- res-navmenu --------------------------*/
function tripmego_resmenu(){ ?>

<div id="resize" class="resizebg">
	<a href="javascript:void(0)" class="closebtn2" onclick="closeMenu()"><i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i> </a>
                <?PHP  
                  $menuParameters = array(
                    'theme_location' => 'primary',
                    'container'       => false,
                    'echo'            => false,
                    //'items_wrap'      => '%3$s',
                    'menu_id'         => 'menu',
                    'depth'           => 0,
                    );

                   // $output = strip_tags(wp_nav_menu($menuParameters),'<a>');
                    //$output = preg_replace('/<a/', '<a class="nav_menu w-nav-link"', $output);
                      echo wp_nav_menu($menuParameters);  

                ?>
</div>

			<?PHP }
add_action('wp_head','tripmego_resmenu');
/* --------------------------side navmenu --------------------------*/

/*-------------------------- side-navmenu --------------------------*/
function tripmego_profile(){ ?>
			<div id="mySidenav2" class="sidenav2">


			  <a href="javascript:void(0)" class="closebtn2" onclick="closeNav2()">&times; </a>
			<?php if(is_user_logged_in()) { ?>
			<?PHP //dynamic_sidebar('sidebar'); ?>
			<?php } else { ?>
			<div class="box_reg_log">
			<button id="btn_register" class="btn_reg_log ">
				</i>&nbsp REGISTER
			</button>

			<button id="btn_login" class="btn_reg_log ">
				</i>&nbsp LOGIN
			</button>
		</div>

			<?php } ?>
			  <?php
				$menu2 =  array( 
			    'theme_location'  => 'sidemenu1', 
			    'container' 	  => false , 
			    'menu' 			  => 'test',
				'link_before'     => '<h4 class="h4_menu_profile">',
			 	'link_after'      => '</h4>',
				'depth'           => 0
			            );
			echo wp_nav_menu($menu2);  

			?>
			</div>
			<?php if(is_user_logged_in()) { ?>

			<?php } else { ?>
			<!-- Part register-->
			<div id="myModal_register" class="modalbg"><!-- The Modal -->
				<div class="modal-content-register"><!-- Modal content -->
    				<span class="close_register">&times;</span>
    					<p></p>
             
						<?php echo do_shortcode('[ultimatemember form_id=7227]') ?>
						
  				</div>
			</div>
								<script>
				// Get the modal
				var modal2 = document.getElementById('myModal_register');

				// Get the button that opens the modal
				var btn2 = document.getElementById("btn_register");

				// Get the <span> element that closes the modal
				var span2 = document.getElementsByClassName("close_register")[0];

				// When the user clicks on the button, open the modal 
				btn2.onclick = function(log) {
				    modal2.style.display = "block";
				}

				// When the user clicks on <span> (x), close the modal
				span2.onclick = function(log2) {
				    modal2.style.display = "none";
				}
				
				// When the user clicks anywhere outside of the modal, close it
				modal2.onclick = function(event2) {
				    if (event2.target == modal2) {
				        modal2.style.display = "none";
				    }
				}
				</script>
			<!-- Part Login-->
			<div id="myModal_login" class="modalbg_login"><!-- The Modal -->
				<div class="modal-content-login"><!-- Modal content -->
    				<span class="close_login">&times;</span>
    					<p> </p>

						<?php echo do_shortcode('[ultimatemember form_id=7208]') ?>
   
						
  				</div>
			</div>
								<script>
				// Get the modal
				var modal3 = document.getElementById('myModal_login');

				// Get the button that opens the modal
				var btn3 = document.getElementById("btn_login");

				// Get the <span> element that closes the modal
				var span3 = document.getElementsByClassName("close_login")[0];

				// When the user clicks on the button, open the modal 
				btn3.onclick = function(reg) {
				    modal3.style.display = "block";
				}

				// When the user clicks on <span> (x), close the modal
				span3.onclick = function(reg2) {
				    modal3.style.display = "none";
				}
				
				// When the user clicks anywhere outside of the modal, close it
				modal3.onclick = function(event3) {
				    if (event3.target == modal3) {
				        modal3.style.display = "none";
				    }
				}
				</script>
		
<?php } ?>



			<?PHP }
add_action('wp_head','tripmego_profile');
/* --------------------------side navmenu --------------------------*/

/* --------------------------filter search menu */
function tripmego_filter(){ ?>
			<div id="mySidenav" class="sidenav">
				  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
			      	<div>
			      		<h4>
			      			<i class="fa fa-search" aria-hidden="true"></i>
			      			&nbspSearch your trip
			      		</h4>
			      		<?php //dynamic_sidebar('single_sidebar');// ?>
			      		<?PHP echo do_shortcode( '[searchandfilter id="774"]' ); ?>
			    	</div>

			  <a href="#">Services</a>
			  <a href="#">Clients</a>
			  <a href="#">Contact</a>
			</div>
			<?PHP }
add_action('wp_head','tripmego_filter');
/* --------------------------filter search menu --------------------------*/

/* --------------------------profile pic-----------------------*/



    
      




/*--------------------------end profile pic-------------------------*/

/* --------------------------footer menu1-----------------------*/


function tripmego_footermenu1(){ ?>
		<div class="div_block_footer1">
			<h3 class="h3_menu_footer">Thing to do at destination.</h3>
			  <?php
				$footermenu1 =  array( 
			    'theme_location'  => 'footer1', 
	            'container'       => false,
	            'echo'            => false,
	            'items_wrap'      => '%3$s',
				'depth'           => 0
            	);
			$output1 = strip_tags(wp_nav_menu($footermenu1),'<a>');
            $output1 = preg_replace('/<a/', '<a class="link_menu_footer"', $output1);
              echo $output1;  

			?>
		</div> <?PHP }


/*--------------------------footer menu1--------------------------*/

/*--------------------------footer menu2--------------------------*/
function tripmego_footermenu2(){ ?>
		<div class="div_block_footer-2">
			<h3 class="h3_menu_footer">tripmego</h3>
				<?php
					$footermenu2 =  array( 
					'theme_location'  => 'footer2', 
					'container'       => false,
					'echo'            => false,
					'items_wrap'      => '%3$s',
					'depth'           => 0
				     );
				$output2 = strip_tags(wp_nav_menu($footermenu2),'<a>');
				$output2 = preg_replace('/<a/', '<a class="link_menu_footer"', $output2);
				 echo $output2;  
							?>
				</div>
				<?PHP }


/*--------------------------footer menu2--------------------------*/

/*--------------------------footer menu3--------------------------*/
function tripmego_footermenu3(){ ?>
<div class="div_block_footer3">
	<h3 class="h3_menu_footer">Support</h3>
			  <?php
				$footermenu3 =  array( 
			    'theme_location'  => 'footer3', 
	            'container'       => false,
	            'echo'            => false,
	            'items_wrap'      => '%3$s',
				'depth'           => 0
            	);
			$output3 = strip_tags(wp_nav_menu($footermenu3),'<a>');
            $output3 = preg_replace('/<a/', '<a class="link_menu_footer"', $output3);
              echo $output3;  

			?>
</div>
<?PHP }


function open_div_box_left(){ ?>

<div class="div_box_left3">

<?PHP } ?>
<?PHP function close_div_box_left(){ ?>

</div>

<?PHP } ?>


<?PHP

add_action('wp_head' , 'price_move');
function price_move(){
remove_action('woocommerce_single_product_summary','woocommerce_template_single_price',10);

}
add_action( 'test' , 'woocommerce_template_single_price' , 65 );

?>

<?PHP
/*--------------------------footer menu2--------------------------*/


add_filter( 'woocommerce_product_tabs', 'tripmego_remove_product_tabs', 98 );

function tripmego_remove_product_tabs( $tabs ) {

    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;

}
add_action('wp_head', 'remove_related');
function remove_related(){
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
}





function shortcode_my_orders( $atts ) {

   extract( shortcode_atts( array(

       'order_count' => -1

   ), $atts ) );

 

   ob_start();

   wc_get_template( 'myaccount/my-orders.php', array(

       'current_user' => get_user_by( 'id', get_current_user_id() ),

       'order_count'   => $order_count

   ) );

   return ob_get_clean();

}

add_shortcode('my_orders', 'shortcode_my_orders');
?>

<?php


add_filter( 'woocommerce_subcategory_count_html', 'jk_hide_category_count' );
function jk_hide_category_count() {
	// No count
}

add_action( 'test_price', 'woocommerce_total_product_price', 31 );
function woocommerce_total_product_price() {
    global $woocommerce, $product;
    // let's setup our divs
    echo sprintf('<div id="product_total_price" style="margin-bottom:20px;">%s %s</div>',__('Product Total:','woocommerce'),'<span class="price">'.$product->get_price().'</span>');
    ?>

        <script>
            jQuery(function($){
                var price = <?php echo $product->get_price(); ?>,
                    currency = '<?php echo get_woocommerce_currency_symbol(); ?>';

                $('[name=quantity]').change(function(){
                    if (!(this.value < 1)) {

                        var product_total = parseFloat(price * this.value);

                        $('#product_total_price .price').html( currency + product_total.toFixed(2));

                    }
                });
            });

        </script>
    <?php
}


add_filter( 'woocommerce_add_to_cart_validation', 'allow_one_item_in_cart', 10);

function allow_one_item_in_cart( $passed ) {

    global $woocommerce;

    $woocommerce->cart->empty_cart();

    return $passed;

}



add_filter( 'woocommerce_gforms_strip_meta_html', 'configure_woocommerce_gforms_strip_meta_html' );
function configure_woocommerce_gforms_strip_meta_html( $strip_html ) {
    $strip_html = false;
    return $strip_html;
}



?>

<?php
/**
* --- STOP! ---
* Get the latest version:
* https://gravitywiz.com/documentation/gravity-forms-ecommerce-fields/
* -------------
*
* Calculation Subtotal Merge Tag
*
* Adds a {subtotal} merge tag which calculates the subtotal of the form. This merge tag can only be used
* within the "Formula" setting of Calculation-enabled fields (i.e. Number, Calculated Product).
*
* @author    David Smith <david@gravitywiz.com>
* @license   GPL-2.0+
* @link      http://gravitywiz.com/subtotal-merge-tag-for-calculations/
* @copyright 2013 Gravity Wiz
*/
class GWCalcSubtotal {

    public static $merge_tag = '{subtotal}';

    function __construct() {

        // front-end
        add_filter( 'gform_pre_render', array( $this, 'maybe_replace_subtotal_merge_tag' ) );
        add_filter( 'gform_pre_validation', array( $this, 'maybe_replace_subtotal_merge_tag_submission' ) );

        // back-end
        add_filter( 'gform_admin_pre_render', array( $this, 'add_merge_tags' ) );

    }

    /**
    * Look for {subtotal} merge tag in form fields 'calculationFormula' property. If found, replace with the
    * aggregated subtotal merge tag string.
    *
    * @param mixed $form
    */
    function maybe_replace_subtotal_merge_tag( $form, $filter_tags = false ) {
        
        foreach( $form['fields'] as &$field ) {
            
            if( current_filter() == 'gform_pre_render' && rgar( $field, 'origCalculationFormula' ) )
                $field['calculationFormula'] = $field['origCalculationFormula'];
            
            if( ! self::has_subtotal_merge_tag( $field ) )
                continue;

            $subtotal_merge_tags = self::get_subtotal_merge_tag_string( $form, $field, $filter_tags );
            $field['origCalculationFormula'] = $field['calculationFormula'];
            $field['calculationFormula'] = str_replace( self::$merge_tag, $subtotal_merge_tags, $field['calculationFormula'] );

        }

        return $form;
    }
    
    function maybe_replace_subtotal_merge_tag_submission( $form ) {
        return $this->maybe_replace_subtotal_merge_tag( $form, true );
    }

    /**
    * Get all the pricing fields on the form, get their corresponding merge tags and aggregate them into a formula that
    * will yeild the form's subtotal.
    *
    * @param mixed $form
    */
    static function get_subtotal_merge_tag_string( $form, $current_field, $filter_tags = false ) {
        
        $pricing_fields = self::get_pricing_fields( $form );
        $product_tag_groups = array();
        
        foreach( $pricing_fields['products'] as $product ) {

            $product_field = rgar( $product, 'product' );
            $option_fields = rgar( $product, 'options' );
            $quantity_field = rgar( $product, 'quantity' );

            // do not include current field in subtotal
            if( $product_field['id'] == $current_field['id'] )
                continue;

            $product_tags = GFCommon::get_field_merge_tags( $product_field );
            $quantity_tag = 1;

            // if a single product type, only get the "price" merge tag
            if( in_array( GFFormsModel::get_input_type( $product_field ), array( 'singleproduct', 'calculation', 'hiddenproduct' ) ) ) {

                // single products provide quantity merge tag
                if( empty( $quantity_field ) && ! rgar( $product_field, 'disableQuantity' ) )
                    $quantity_tag = $product_tags[2]['tag'];

                $product_tags = array( $product_tags[1] );
            }

            // if quantity field is provided for product, get merge tag
            if( ! empty( $quantity_field ) ) {
                $quantity_tag = GFCommon::get_field_merge_tags( $quantity_field );
                $quantity_tag = $quantity_tag[0]['tag'];
            }
            
            if( $filter_tags && ! self::has_valid_quantity( $quantity_tag ) )
                continue;
            
            $product_tags = wp_list_pluck( $product_tags, 'tag' );
            $option_tags = array();
            
            foreach( $option_fields as $option_field ) {

                if( is_array( $option_field['inputs'] ) ) {

                    $choice_number = 1;

                    foreach( $option_field['inputs'] as &$input ) {

                        //hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
                        if( $choice_number % 10 == 0 )
                            $choice_number++;

                        $input['id'] = $option_field['id'] . '.' . $choice_number++;

                    }
                }

                $new_options_tags = GFCommon::get_field_merge_tags( $option_field );
                if( ! is_array( $new_options_tags ) )
                    continue;

                if( GFFormsModel::get_input_type( $option_field ) == 'checkbox' )
                    array_shift( $new_options_tags );

                $option_tags = array_merge( $option_tags, $new_options_tags );
            }

            $option_tags = wp_list_pluck( $option_tags, 'tag' );

            $product_tag_groups[] = '( ( ' . implode( ' + ', array_merge( $product_tags, $option_tags ) ) . ' ) * ' . $quantity_tag . ' )';

        }

        $shipping_tag = 0;
        /* Shipping should not be included in subtotal, correct?
        if( rgar( $pricing_fields, 'shipping' ) ) {
            $shipping_tag = GFCommon::get_field_merge_tags( rgars( $pricing_fields, 'shipping/0' ) );
            $shipping_tag = $shipping_tag[0]['tag'];
        }*/

        $pricing_tag_string = '( ( ' . implode( ' + ', $product_tag_groups ) . ' ) + ' . $shipping_tag . ' )';

        return $pricing_tag_string;
    }
    
    /**
    * Get all pricing fields from a given form object grouped by product and shipping with options nested under their
    * respective products.
    *
    * @param mixed $form
    */
    static function get_pricing_fields( $form ) {

        $product_fields = array();

        foreach( $form["fields"] as $field ) {

            if( $field["type"] != 'product' )
                continue;

            $option_fields = GFCommon::get_product_fields_by_type($form, array("option"), $field['id'] );

            // can only have 1 quantity field
            $quantity_field = GFCommon::get_product_fields_by_type( $form, array("quantity"), $field['id'] );
            $quantity_field = rgar( $quantity_field, 0 );

            $product_fields[] = array(
                'product' => $field,
                'options' => $option_fields,
                'quantity' => $quantity_field
                );

        }

        $shipping_field = GFCommon::get_fields_by_type($form, array("shipping"));

        return array( "products" => $product_fields, "shipping" => $shipping_field );
    }
    
    static function has_valid_quantity( $quantity_tag ) {

        if( is_numeric( $quantity_tag ) ) {

            $qty_value = $quantity_tag;

        } else {

            // extract qty input ID from the merge tag
            preg_match_all( '/{[^{]*?:(\d+(\.\d+)?)(:(.*?))?}/mi', $quantity_tag, $matches, PREG_SET_ORDER );
            $qty_input_id = rgars( $matches, '0/1' );
            $qty_value = rgpost( 'input_' . str_replace( '.', '_', $qty_input_id ) );

        }
        
        return floatval( $qty_value ) > 0;
    }
    
    function add_merge_tags( $form ) {

        $label = __('Subtotal', 'gravityforms');

        ?>

        <script type="text/javascript">

            // for the future (not yet supported for calc field)
            gform.addFilter("gform_merge_tags", "gwcs_add_merge_tags");
            function gwcs_add_merge_tags( mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option ) {
                mergeTags["pricing"].tags.push({ tag: '<?php echo self::$merge_tag; ?>', label: '<?php echo $label; ?>' });
                return mergeTags;
            }

            // hacky, but only temporary
            jQuery(document).ready(function($){

                var calcMergeTagSelect = $('#field_calculation_formula_variable_select');
                calcMergeTagSelect.find('optgroup').eq(0).append( '<option value="<?php echo self::$merge_tag; ?>"><?php echo $label; ?></option>' );

            });

        </script>

        <?php
        //return the form object from the php hook
        return $form;
    }

    static function has_subtotal_merge_tag( $field ) {
        
        // check if form is passed
        if( isset( $field['fields'] ) ) {

            $form = $field;
            foreach( $form['fields'] as $field ) {
                if( self::has_subtotal_merge_tag( $field ) )
                    return true;
            }

        } else {

            if( isset( $field['calculationFormula'] ) && strpos( $field['calculationFormula'], self::$merge_tag ) !== false )
                return true;

        }

        return false;
    }

}

new GWCalcSubtotal();


function my_switch_gateways_by_context($available_gateways) {
  global $woocommerce;

  $endpoint = $woocommerce->query->get_current_endpoint();

  if ($endpoint == 'order-pay') {
    unset($available_gateways['cod']);
  } else {
    unset($available_gateways['stripe']);
  }

  return $available_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'my_switch_gateways_by_context');

