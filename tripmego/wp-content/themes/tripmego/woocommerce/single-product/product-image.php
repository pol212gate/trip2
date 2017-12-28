<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;
$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$thumbnail_size    = apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' );
$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . $placeholder,
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
) );
?>

			<div class="item2">
			<div class="div_box_right">
           <!-- <div class="div_block_slide w-clearfix">
              <div class="slider_show_image_travel w-slider" data-animation="slide" data-duration="500" data-infinite="1">
                <div class="w-slider-mask">-->

								<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?> w-slide" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
									<div class="woocommerce-product-gallery__wrapper w-slide">
							<?php
							$attributes = array(
								'title'                   => get_post_field( 'post_title', $post_thumbnail_id ),
								'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
								'data-src'                => $full_size_image[0],
								'data-large_image'        => $full_size_image[0],
								'data-large_image_width'  => $full_size_image[1],
								'data-large_image_height' => $full_size_image[2],
							);

							if ( has_post_thumbnail() ) {
								$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'shop_thumbnail' ) . '" class="woocommerce-product-gallery__image"><div><a href="' . esc_url( $full_size_image[0] ) . '">';
								$html .= get_the_post_thumbnail( $post->ID, 'shop_single', $attributes );
								$html .= '</a></div></div>';
							} else {
								$html  = '<div class="woocommerce-product-gallery__image--placeholder w-slide">';
								$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image image_slider_from_database" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
								$html .= '</div>';
							}

							echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );

							do_action( 'woocommerce_product_thumbnails' );
							?>
									</div>
								</div>
							<!--</div>
						</div>
					</div>-->

                     <div class="div_promotion w-clearfix"><i class="fa fa-tag image-37" aria-hidden="true"></i>
            <p class="p_promotion">if you confirm booking 2 people up Special Discount 15% Before until 11 Apr 2017</p>
          </div>

</div>
</div>
  

