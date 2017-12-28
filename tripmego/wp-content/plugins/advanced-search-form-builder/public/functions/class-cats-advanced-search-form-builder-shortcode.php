<?php
$ASFB_shortcode = new Cats_Advanced_Search_Form_Builder_Shortcode();

add_shortcode('form_search', array($ASFB_shortcode, 'formSearch'));
add_shortcode('search_result', array($ASFB_shortcode, 'searchResult'));


class Cats_Advanced_Search_Form_Builder_Shortcode {

    private function includeStyle($dataForm)
    {

		if (isset($dataForm['tax']) && count($dataForm['tax']) > 0) {
			foreach ($dataForm['tax'] as $key => $item) {
			    wp_enqueue_style(
			        $item['input_type'] . getClassByFileName($item['input_type'] . '_styling'),
			        $item[$item['input_type'] . '_styling']
			    );
			}
		}

        if (isset($dataForm['cf']) && count($dataForm['cf']) > 0) {
            foreach ($dataForm['cf'] as $key => $item) {
                wp_enqueue_style(
                    $item['input_type'] . getClassByFileName($item['input_type'] . '_styling'),
                    $item[$item['input_type'] . '_styling']
                );
            }
        }

    	if (isset($dataForm['optionsTextInput'])) {
    		wp_enqueue_style(
    		    'optionsTextInput',
    		    $dataForm['optionsTextInput']
    		);
    	}
    }

	public function formSearch($attr, $data = '')
	{
        global $ASFB_config;

	    $options = shortcode_atts( array(
	        'id' => '0',
	    ), $attr );

	    $formBuilder = get_post($options['id']);
        $dataForm = get_post_meta( $options['id'], $ASFB_config['default_setting']['key_post_meta_form'], true);
        $this->includeStyle($dataForm);

	    if ( is_wp_error($formBuilder) ) return false;

	    ob_start();
			    
	    include plugin_dir_path(__DIR__)  . '/partials/form-search-shortcode.tpl.php';

	    return ob_get_clean();
	}

	public function searchResult($attr, $data = '')
	{
	    $timeStart = microtime(true);

        global $ASFB_config;
        global $wpdb;

	    $form_id = ASFB_request::getQuery('form_id', '');
        $dataForm = get_post_meta( $form_id, $ASFB_config['default_setting']['key_post_meta_form'], true);

        $this->includeStyle($dataForm);

        if ( !$dataForm ) {
            echo __('Form id not available', 'advanced_search_form_builder');
            return false;
        }

	    $args = ASFB_search_enpoint::buildQuery($form_id);
	    $cacheName = md5(serialize($args));
	    $cacheData = ASFB_cache::get($cacheName);
	    $cacheStatus = 'cached';

	    if (empty($cacheData)) {
	        $searchResult = new WP_Query( $args );
	        
	        if (!isset($args['posts_per_page']) || $args['posts_per_page'] < 1) {
	        	$args['posts_per_page'] = 1;
	        }

	        $posts = array(
	            'total_items' => $searchResult->found_posts,
	            'total_pages' => ceil($searchResult->found_posts / $args['posts_per_page']),
	            'result' => $searchResult->get_posts()
	        );
	        $cacheStatus = 'non-cache';

            $posts  = json_decode(json_encode($posts), true);
	        $cacheData = ASFB_cache::save($cacheName, $posts);

	    } else {
	        $posts = json_decode(file_get_contents($cacheData['path_file']), true);
	    }

	    $timeEnd = microtime(true);

	    $searchResult = array(
	        'result' => $posts['result'],
	        'number_post' => count($posts['result']),
	        'total_items' => $posts['total_items'],
	        'total_pages' => $posts['total_pages'],
	        'took' => $timeEnd - $timeStart,
	        'meta' => array(
	            'cachestatus' => $cacheStatus,
	            'cache' => $cacheData,
	            'query' => $args,
	        )
	    );

	    ob_start();

        if ($dataForm['include_form'] == true) {
            echo $this->formSearch(array('id' => $form_id));
        }

        include plugin_dir_path(__DIR__)  . '/partials/form-search-result.tpl.php';

	    return ob_get_clean();
	}
}
