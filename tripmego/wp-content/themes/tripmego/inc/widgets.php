<?PHP

/**
 * Adds Foo_Widget widget.
 */
class Foo_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'foo_widget', // Base ID
			esc_html__( 'Widget Title', 'tripmego' ), // Name
			array( 'description' => esc_html__( 'A Foo Widget', 'tripmego' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		//var_dump($args);
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) { ?>

			<?PHP
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];	
		}

		echo esc_html__( 'Hello, World!', 'tripmego' );

		if(! empty($instance['text']) ) { ?>
				<p class="text-foo-widget">
			<?PHP echo $instance['text']; ?>
				</p>
			<?PHP }

		if(! empty($instance['textarea']) ) { ?>
		<p class="textarea-foo-widget"> 
			<?PHP echo $instance['textarea']; ?> 
		</p>
		<?PHP }


		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'tripmego' );
		$text= ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( '', 'tripmego' );
		$textarea = ! empty( $instance['textarea'] ) ? $instance['textarea'] : esc_html__( '', 'tripmego' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'tripmego' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<!-- text and text area -->
		<p>
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:', 'tripmego'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Textarea:', 'tripmego'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('checkbox'); ?>" name="<?php echo $this->get_field_name('checkbox'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox ); ?> />
			<label for="<?php echo $this->get_field_id('checkbox'); ?>"><?php _e('Checkbox', 'wp_widget_plugin'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('select'); ?>"><?php _e('Select', 'wp_widget_plugin'); ?></label>
				<select name="<?php echo $this->get_field_name('select'); ?>" id="<?php echo $this->get_field_id('select'); ?>" class="widefat">
				<?php
					$options = array('lorem', 'ipsum', 'dolorem');
					foreach ($options as $option) 
					{
						echo '<option value="' . $option . '" id="' . $option . '"', $select == $option ? ' selected="selected"' : '', '>', $option, '</option>';
					}
				?>
				</select>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['text'] = ( ! empty( $new_instance['text'] ) ) ? strip_tags( $new_instance['text'] ) : '';
		$instance['textarea'] = ( ! empty( $new_instance['textarea'] ) ) ? strip_tags( $new_instance['textarea'] ) : '';

		return $instance;
	}

} // class Foo_Widget

// register Foo_Widget widget
function register_foo_widget() {
    register_widget( 'Foo_Widget' );
}
add_action( 'widgets_init', 'register_foo_widget' );

?>