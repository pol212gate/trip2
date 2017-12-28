<?php

	/***
	***	@global profile redirection
	***/
	add_action('template_redirect', 'um_bbpress_profile_redirect');
	function um_bbpress_profile_redirect(){
		$bbp_user_id = get_query_var( 'bbp_user_id' );
		if ( $bbp_user_id > 0 && bbp_is_single_user() ) {
			um_fetch_user($bbp_user_id);
			$redirect = um_user_profile_url();
			exit( wp_redirect( $redirect ) );
		}
	}

	add_action('template_redirect','um_bbpress_bbp_single_forum');
    function um_bbpress_bbp_single_forum(){
    	global $post;

    	
		if( isset(  $post->ID ) && is_bbpress() ){

    		$accessible = get_post_meta(  $post->ID,'_um_accessible', true);
    		$roles = get_post_meta(  $post->ID, '_um_access_roles', true);
    		$parent_roles = get_post_meta(  $post->post_parent, '_um_access_roles', true);
    		$custom_access_settings = get_post_meta(  $post->ID,'_um_custom_access_settings', true );
    		$_um_bbpress_can_topic = get_post_meta(  $post->ID, '_um_bbpress_can_topic', true );
    		$_um_bbpress_can_reply = get_post_meta(  $post->ID, '_um_bbpress_can_reply', true );
    		$_um_access_redirect = get_post_meta(  $post->ID, '_um_access_redirect', true );
    		$_um_access_parent_redirect = get_post_meta(  $post->post_parent, '_um_access_redirect', true );

    		if( $roles ){
	    		if( ! empty( $_um_access_redirect ) && ! in_array( um_user('role') , $roles )  ){
	    			exit( wp_redirect( $_um_access_redirect ) );
	    		}
	    	}

	    	if( $parent_roles ){
	    		if( ! empty( $_um_access_parent_redirect  ) && ! in_array( um_user('role') , $parent_roles )  ){
	    			exit( wp_redirect( $_um_access_parent_redirect  ) );
	    		}
	    	}

	    	if( is_array( $_um_bbpress_can_topic ) ){
	    		if( ! in_array( um_user('role'), $_um_bbpress_can_topic ) && ! empty( $_um_bbpress_can_topic ) ){
		    		add_filter('bbp_current_user_can_access_create_topic_form','um_bbpress_bbp_current_user_can_access_create_topic_form');
				}
			}

			if( is_array(  $_um_bbpress_can_reply ) ){
				if( ! in_array( um_user('role'), $_um_bbpress_can_reply )  && ! empty( $_um_bbpress_can_topic )  ){
		    		add_filter('bbp_current_user_can_access_create_reply_form','um_bbpress_bbp_current_user_can_access_create_reply_form');
				}
			}
    	}
    }