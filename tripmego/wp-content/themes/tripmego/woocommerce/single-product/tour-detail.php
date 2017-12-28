<?PHP

add_action('show_tour_detail','tripmego_tour_detail');

function tripmego_tour_detail() {






		$detail = array(
		'tripcode'             => get_field('trip_code'),
        'tatlicense'        => get_field('tat_license'),
        'countrytour'		=> get_field('country_tour'),

	);	
		echo $detail['tripcode'];
		echo $detail['tatlicense'];
		echo $detail['countrytour'];
	

}

?>