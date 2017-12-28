<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

?>

<?PHP if(is_front_page()) { ?>

<?php if ( $product->is_on_sale() ) : ?>
	
	<?php echo apply_filters( 'woocommerce_sale_flash', '<div class="onsale3"><span >' . esc_html__( 'Special!', 'woocommerce' ) . '<i class="fa fa-tags " aria-hidden="true"></i></span></div>', $post, $product ); ?>

<?php endif; ?>

<?PHP } else { ?>

<?php if ( $product->is_on_sale() ) : ?>
	
	<?php echo apply_filters( 'woocommerce_sale_flash', '<div class="onsale2"><span >' . esc_html__( 'Sale!', 'woocommerce' ) . '<br><i class="fa fa-tags fa-2x" aria-hidden="true"></i></span></div>', $post, $product ); ?>

<?php endif; ?>

<!-- Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. -->
<?PHP }  ?>