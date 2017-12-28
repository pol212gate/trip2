<?PHP
ob_start();
/*session_start();*/
/**
 * Register meta box(es).
 */
function chapter_register_meta_boxes() {
    add_meta_box( 'meta-box-id', __( 'My Meta Box', 'textdomain' ), 'chapter_my_display_callback', 'post' , 'normal' );
}
add_action( 'add_meta_boxes', 'chapter_register_meta_boxes' );
 
/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function chapter_my_display_callback( $post ) {
    // Display code/markup goes here. Don't forget to include nonces!
    
    wp_nonce_field( basename( __FILE__ ), 'tripmego_nonce' );
    $tripmego_stored_meta = get_post_meta( $post->ID );
    ?>
 		<!-- INPUT TEXT -->
    <p>
        <label for="meta-text" class="tripmego-row-title"><?php _e( 'Example Text Input', 'tripmego-textdomain' )?></label>
        <input type="text" name="meta-text" id="meta-text" value="<?php if ( isset ( $tripmego_stored_meta['meta-text'] ) ) echo $tripmego_stored_meta['meta-text'][0]; ?>" />
    </p>
    	<!-- Check Box -->
    <p>
    	<span class="tripmego-row-title"><?php _e( 'Example Checkbox Input', 'tripmego-textdomain' )?></span>
    	<div class="tripmego-row-content">
        	<label for="meta-checkbox">
           	 <input type="checkbox" name="meta-checkbox" id="meta-checkbox" value="yes" <?php if ( isset ( $tripmego_stored_meta['meta-checkbox'] ) ) checked( $tripmego_stored_meta['meta-checkbox'][0], 'yes' ); ?> />
           		 <?php _e( 'Checkbox label', 'tripmego-textdomain' )?>
        	</label>
        	<label for="meta-checkbox-two">
           		 <input type="checkbox" name="meta-checkbox-two" id="meta-checkbox-two" value="yes" <?php if ( isset ( $tripmego_stored_meta['meta-checkbox-two'] ) ) checked( $tripmego_stored_meta['meta-checkbox-two'][0], 'yes' ); ?> />
            	<?php _e( 'Another checkbox', 'tripmego-textdomain' )?>
       		 </label>
    	</div>
	</p>
		<!-- Select LIST -->
	<p>
    	<label for="meta-select" class="tripmego-row-title"><?php _e( 'Example Select Input', 'tripmego-textdomain' )?></label>
    		<select name="meta-select" id="meta-select">
       			 <option value="select-one" <?php if ( isset ( $tripmego_stored_meta['meta-select'] ) ) selected( $tripmego_stored_meta['meta-select'][0], 'select-one' ); ?>><?php _e( 'One', 'tripmego-textdomain' )?></option>';
       			 <option value="select-two" <?php if ( isset ( $tripmego_stored_meta['meta-select'] ) ) selected( $tripmego_stored_meta['meta-select'][0], 'select-two' ); ?>><?php _e( 'Two', 'tripmego-textdomain' )?></option>';
    		</select>
	</p>
 
    <?php

}
 
/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function chapter_save_meta_box( $post_id ) {
    // Save logic goes here. Don't forget to include nonce checks!
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'tripmego_nonce' ] ) && wp_verify_nonce( $_POST[ 'tripmego_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'meta-text' ] ) ) {
        update_post_meta( $post_id, 'meta-text', sanitize_text_field( $_POST[ 'meta-text' ] ) );
    }
    // Checks for input and saves
if( isset( $_POST[ 'meta-checkbox' ] ) ) {
    update_post_meta( $post_id, 'meta-checkbox', 'yes' );
} else {
    update_post_meta( $post_id, 'meta-checkbox', '' );
}
 
// Checks for input and saves
if( isset( $_POST[ 'meta-checkbox-two' ] ) ) {
    update_post_meta( $post_id, 'meta-checkbox-two', 'yes' );
} else {
    update_post_meta( $post_id, 'meta-checkbox-two', '' );
}
// Checks for input and saves if needed
if( isset( $_POST[ 'meta-select' ] ) ) {
    update_post_meta( $post_id, 'meta-select', $_POST[ 'meta-select' ] );
}
}
add_action( 'save_post', 'chapter_save_meta_box' );

ob_end_flush();