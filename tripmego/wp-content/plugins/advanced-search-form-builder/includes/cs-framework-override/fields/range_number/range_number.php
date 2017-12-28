<?php
class CSFramework_Option_range_number extends CSFramework_Options {

  public function __construct( $field, $value = '', $unique = '' ) {
    parent::__construct( $field, $value, $unique );
  }

  public function output(){

    echo $this->element_before(); 
    echo '<input type="number" placeholder="'. __('Min', 'advanced_search_form_builder') .'" name="'. $this->element_name() .'[min]" value="'. $this->element_value()['min'] .'"'. $this->element_class() . $this->element_attributes() .'/>';
    echo '<input type="number" placeholder="'. __('Max', 'advanced_search_form_builder') .'" name="'. $this->element_name() .'[max]" value="'. $this->element_value()['max'] .'"'. $this->element_class() . $this->element_attributes() .'/>';
    echo '<input type="number" placeholder="'. __('Step', 'advanced_search_form_builder') .'" name="'. $this->element_name() .'[step]" value="'. $this->element_value()['step'] .'"'. $this->element_class() . $this->element_attributes() .'/>';
    echo $this->element_after();

  }
}