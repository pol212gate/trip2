<?php
function parse_wt_search_func($atts, $content){
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	$location =  isset($atts['location']) ? $atts['location'] :'';
	
	$show_filters =  isset($atts['show_filters']) ? $atts['show_filters'] :'';
	$show_location =  isset($atts['show_location']) ? $atts['show_location'] :'';
	$cats =  isset($atts['cats']) ? $atts['cats'] :'';
	$tags =  isset($atts['tags']) ? $atts['tags'] :'';
	
	$args = array(
		'hide_empty'        => false, 
		'include'           => explode(",",$location)
	); 
	
	$terms = get_terms('wt_location', $args);
	ob_start();
	?>
	<div class="wt-search-form wt-search-shortcode">
        <form role="search" method="get" id="searchform" class="wt-product-search-form" action="<?php echo home_url(); ?>/">
        	<div class="input-group">
            
            <?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){ ?>
              <div class="input-group-btn wt-search-dropdown">
                <button name="wt_location" type="button" class="btn btn-default wt-product-search-dropdown-button wt-showdrd"><span class="button-label"><?php echo esc_html__('Locations','woo-tour'); ?></span> <span class="fa fa-angle-down"></span></button>
                <div class="wt-dropdown-select">
                	<?php if($show_filters=='yes'){ wt_search_filters($cats,$tags='',$location=''); }
					if($show_location!='no'){
						?>
						<div class="row">
						<?php 
						$i = 0;
						$nb_tern = count($terms);
						foreach ( $terms as $term ) {
							$i++;
							$tax_img = '';
							if(function_exists('z_taxonomy_image_url')){ $tax_img = z_taxonomy_image_url($term->term_id);}
							if($tax_img==''){ 
								$img_id = get_option('id_image_' . $term->term_id);
								if($img_id!=''){
									$get_img = wp_get_attachment_image_src($img_id,'wethumb_85x85');
									if(isset($get_img[0])){
										$tax_img = $get_img[0];
									}
								}
							}
							$img ='';
							if($tax_img!=''){
								$img = '<img src="'.esc_url($tax_img).'" alt="'.esc_attr($term->name).'">';
							}
							if ((!function_exists('version_compare')) || version_compare(phpversion(), '5.4', '<')) {
								$tour = $term->count > 1 ? sprintf(esc_html__('%d Tours', 'woo-tour'), $term->count) : sprintf(esc_html__('%d Tour', 'woo-tour'), $term->count);
							}else{
								$tour = get_term_post_count('wt_location',$term->term_id);
								$tour = sprintf(esc_html__('%d Tours', 'woo-tour'), $tour);
							}
							echo '<div class="col-md-4 col-sm-4">
								<a href="'. esc_url( get_term_link( $term ) ) .'" data-value="'. $term->slug .'">';
									
									echo $img!='' ? '<span class="loc-image">'.$img.'</span>' : '';
									echo '
									<span class="loc-details">
										<h3>'. $term->name .'</h3>
										<span>'. $tour .'</span>
									</span>
								</a>
							</div>';
							if($i%3== 0 || $i == $nb_tern){
								echo '</div><div class="row">';
							}
						}?>
						</div>
						<?php 
					}
					?>
                </div>
              </div><!-- /btn-group -->
            <?php } //if have terms ?>
            
              <input type="text" value="<?php the_search_query(); ?>" name="s" id="s" placeholder="<?php echo esc_html__('I want to travel to...','woo-tour'); ?>" class="form-control" />
              <input type="hidden" name="post_type" value="product" />
              <span class="input-group-btn">
              	<button type="submit" id="searchsubmit" class="btn btn-default wt-product-search-submit" ><i class="fa fa-search"></i></button>
              </span>
            </div>
        </form>
    </div>
	<?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;

}

if(!function_exists('wt_search_filters')){
	function wt_search_filters($cat_include, $tag_include, $location_include){
		$column = 3;
		if($cat_include=='hide'){
			$column = $column -1;
		}
		if($tag_include=='hide'){
			$column = $column -1;
		}
		if($location_include=='hide'){
			$column = $column -1;
		}
		if($column=='3'){ $class = 'col-md-4';}elseif($column=='2'){$class = 'col-md-6';}
		elseif($column=='1'){$class = 'col-md-12';}
		$all_text = esc_html__('All','woo-tour');?>
		<div class="wt-filter-expand <?php echo esc_attr('we-column-'.$column)?> row">
			<?php 
			if($cat_include!='hide'){
				$args = array( 'hide_empty' => false ); 
				if($cat_include!=''){
					$cat_include = explode(",", $cat_include);
					if(is_numeric($cat_include[0])){
						$args['include'] = $cat_include;
					}else{
						$args['slug'] = $cat_include;
					}
				}
				$terms = get_terms('product_cat', $args);
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){ ?>
					<div class="wt-filter-cat <?php echo esc_attr($class);?> col-sm-4">
						<span class=""><?php echo esc_html__('Category','woo-tour');?></span>
                        <select name="product_cat">
                            <option value=""><?php echo esc_html($all_text);?></option>
                            <?php 
                              foreach ( $terms as $term ) {
                                echo '<option value="'. $term->slug .'">'. $term->name .'</option>';
                              }?>
                        </select>
					</div>
			<?php } 
			}
			if($tag_include!='hide'){
				$args = array( 'hide_empty' => false ); 
				if($tag_include!=''){
					$tag_include = explode(",", $tag_include);
					if(is_numeric($tag_include[0])){
						$args['include'] = $tag_include;
					}else{
						$args['slug'] = $tag_include;
					}
				}
				$terms = get_terms('product_tag', $args);
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){ ?>
					<div class="we-filter-tag <?php echo esc_attr($class);?> col-sm-4">
						<span class=""><?php echo esc_html__('Tags','woo-tour');?></span>
                        <select name="product_tag">
                            <option value=""><?php echo esc_html($all_text);?></option>
                            <?php 
                              foreach ( $terms as $term ) {
                                echo '<option value="'. $term->slug .'">'. $term->name .'</option>';
                              }
                              ?>
                        </select>
					</div>
			<?php } 
			} 
			if($location_include!='hide'){
				$args = array( 'hide_empty' => false ); 
				if($tag_include!=''){
					$tag_include = explode(",", $tag_include);
					if(is_numeric($tag_include[0])){
						$args['include'] = $tag_include;
					}else{
						$args['slug'] = $tag_include;
					}
				}
				$terms = get_terms('wt_location', $args);
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){ ?>
					<div class="we-filter-tag <?php echo esc_attr($class);?> col-sm-4">
						<span class=""><?php echo esc_html__('Location','woo-tour');?></span>
                        <select name="wt_location">
                            <option value=""><?php echo esc_html($all_text);?></option>
                            <?php 
                              foreach ( $terms as $term ) {
                                echo '<option value="'. $term->slug .'">'. $term->name .'</option>';
                              }
                              ?>
                        </select>
					</div>
			<?php } 
			}?>
        </div>
	<?php
    }
}
if(!function_exists('get_term_post_count')){
	function get_term_post_count( $taxonomy = 'category', $term = '', $args = [] )
	{
		// Lets first validate and sanitize our parameters, on failure, just return false
		if ( !$term )
			return false;
	
		if ( $term !== 'all' ) {
			if ( !is_array( $term ) ) {
				$term = filter_var(       $term, FILTER_VALIDATE_INT );
			} else {
				$term = filter_var_array( $term, FILTER_VALIDATE_INT );
			}
		}
	
		if ( $taxonomy !== 'category' ) {
			$taxonomy = filter_var( $taxonomy, FILTER_SANITIZE_STRING );
			if ( !taxonomy_exists( $taxonomy ) )
				return false;
		}
	
		if ( $args ) {
			if ( !is_array ) 
				return false;
		}
	
		// Now that we have come this far, lets continue and wrap it up
		// Set our default args
		$defaults = [
			'posts_per_page' => 1,
			'fields'         => 'ids'
		];
	
		if ( $term !== 'all' ) {
			$defaults['tax_query'] = [
				[
					'taxonomy' => $taxonomy,
					'terms'    => $term
				]
			];
		}
		$combined_args = wp_parse_args( $args, $defaults );
		$q = new WP_Query( $combined_args );
	
		// Return the post count
		return $q->found_posts;
	}
}
add_shortcode( 'wt_search', 'parse_wt_search_func' );
add_action( 'after_setup_theme', 'wt_search_reg_vc' );
function wt_search_reg_vc(){
	if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("WooTours - Search", "woo-tour"),
	   "base" => "wt_search",
	   "class" => "",
	   "icon" => "icon-search",
	   "controls" => "full",
	   "category" => esc_html__('Wootours','woo-tour'),
	   "params" => array(
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Included Locations", "woo-tour"),
			"param_name" => "location",
			"value" => "",
			"description" => esc_html__("List of location ID (or slug), separated by a comma, Ex: 13,14", "woo-tour"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show filters", 'woo-tour'),
			 "param_name" => "show_filters",
			 "value" => array(
			 	esc_html__('No', 'woo-tour') => 'no',
				esc_html__('Yes', 'woo-tour') => 'yes',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Included Cats in filter", "woo-tour"),
			"param_name" => "cats",
			"value" => "",
			"description" => esc_html__("List of Cats ID (or slug), separated by a comma, Ex: 13,14", "woo-tour"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Included Tags in filter", "woo-tour"),
			"param_name" => "location",
			"value" => "",
			"description" => esc_html__("List of Tags ID (or slug), separated by a comma, Ex: 13,14", "woo-tour"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show list of locations", 'woo-tour'),
			 "param_name" => "show_location",
			 "value" => array(
			 	esc_html__('Yes', 'woo-tour') => 'yes',
				esc_html__('No', 'woo-tour') => 'no',
			 ),
			 "description" => ''
		  ),
	   )
	));
	}
}