<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Field: Select
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
class CSFramework_Option_select extends CSFramework_Options {

  public function __construct( $field, $value = '', $unique = '' ) {
    parent::__construct( $field, $value, $unique );
  }

  public function output() {

    echo $this->element_before();

    if( isset( $this->field['options'] ) ) {

      $options    = $this->field['options'];
      $class      = $this->element_class();
      $options    = ( is_array( $options ) ) ? $options : array_filter( $this->element_data( $options ) );
      $extra_name = ( isset( $this->field['attributes']['multiple'] ) ) ? '[]' : '';
      $chosen_rtl = ( is_rtl() && strpos( $class, 'chosen' ) ) ? 'chosen-rtl' : '';

      if (is_array($this->element_value())) {
        $oldValue = implode(',', $this->element_value());
      } else {
        $oldValue = $this->element_value();
      }

      echo '<select data-valueselected=\''. $oldValue .'\' name="'. $this->element_name( $extra_name ) .'"'. $this->element_class( $chosen_rtl ) . $this->element_attributes() .'>';

      echo ( isset( $this->field['default_option'] ) ) ? '<option value="">'.$this->field['default_option'].'</option>' : '';

      if( !empty( $options ) ){
        foreach ( $options as $key => $value ) {
          echo '<option value="'. $key .'" '. $this->checked( $this->element_value(), $key, 'selected' ) .'>'. $value .'</option>';
        }
      }

      echo '</select>';

      if (strpos( $class, 'chosen' )) {
          $event1 = "ActionSelectAll(event)";
          $event2 = "ActionUnSelectAll(event)";
          echo '<button onclick="'. $event1 .'" style="margin-top: 5px;" type="button" class="button button button-small hide-if-no-js">Select all</button>';
          echo '<button onclick="'. $event2 .'" style="margin-top: 5px;" type="button" class="button button button-small hide-if-no-js">Remove all</button>';
      }
    }

    echo $this->element_after();

  }

}
