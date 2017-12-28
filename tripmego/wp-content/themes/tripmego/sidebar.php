<?php


//sidebar condition
	if(is_page()) {
		dynamic_sidebar('page_sidebar');

	}elseif(is_single()){
		dynamic_sidebar('single_sidebar');
	}else{
		dynamic_sidebar('sidebar');
	}
// use dynamic function get_sidebar(); not parameter
// use dynamic function dynamic_sidebar(); not parameter
// use dynamic function dynamic_sidebar('ID'); 
// use dynamic function dynamic_sidebar('name'); 

?>