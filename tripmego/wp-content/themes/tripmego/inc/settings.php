<?php 
 // ------------------------------------------------------------------
 // Add all your sections, fields and settings during admin_init
 // ------------------------------------------------------------------
 //
 
 function tripmego_settings_api_init() {
 	// Add the section to reading settings so we can add our
 	// fields to it
 	add_settings_section(
		'tripmego_setting_section',
		'other reading setting',
		'tripmego_setting_section_callback_function',
		'reading'
	);
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field(
		'tripmego_setting_name',
		'Example setting Name',
		'tripmego_setting_callback_function',
		'reading',
		'tripmego_setting_section'
	);
 	
 	// Rtripmegoister our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'reading', 'tripmego_setting_name' );
 	register_setting( 'reading', 'tripmego_text_string' );
 	register_setting( 'reading', 'tripmego_text_string2' );
 } // tripmego_settings_api_init()
 
 add_action( 'admin_init', 'tripmego_settings_api_init' );
 
  
 // ------------------------------------------------------------------
 // Settings section callback function
 // ------------------------------------------------------------------
 //
 // This function is needed if we added a new section. This function 
 // will be run at the start of our section
 //
 
 function tripmego_setting_section_callback_function() {
 	echo '<p>Intro text for our settings section</p>';
 }
 
 // ------------------------------------------------------------------
 // Callback function for our example setting
 // ------------------------------------------------------------------
 //
 // creates a checkbox true/false option. Other types are surely possible
 //
 
 function tripmego_setting_callback_function() {
 	echo '<input name="tripmego_setting_name" id="tripmego_setting_name" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'tripmego_setting_name' ), false ) . ' /> Explanation text';


 	echo'<br/>';
	echo '<input id="tripmego_text_string" name="tripmego_text_string" size="40" type="text" value="'.get_option('tripmego_text_string').'" />';

	 	echo'<br/>';
	echo '<input id="tripmego_text_string2" name="tripmego_text_string2" size="40" type="text" value="'.get_option('tripmego_text_string2').'" />';

 }


 


