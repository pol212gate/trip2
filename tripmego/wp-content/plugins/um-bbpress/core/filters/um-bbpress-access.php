<?php
	
	/***
	***	@inherit topic access control from their parent "forums"
	***/
	add_filter('um_access_control_for_parent_posts', 'um_bbpress_access_control_for_topics');
	function um_bbpress_access_control_for_topics( $post_id ) {
		$is_forum = bbp_get_topic_forum_id( $post_id );
		if ( $is_forum )
			return $is_forum;
		return $post_id;
	}
	
	/***
	***	@add a class to help us hide it from forums list
	***/
	add_filter('bbp_get_forum_class', 'um_bbpress_add_class_to_locked_forum_or_topic', 888, 2);
	add_filter('bbp_get_topic_class', 'um_bbpress_add_class_to_locked_forum_or_topic', 888, 2);
	function um_bbpress_add_class_to_locked_forum_or_topic( $classes, $post_id ) {
		global $ultimatemember;
		
		$args = $ultimatemember->access->get_meta( $post_id );
		extract($args);

		if ( !isset( $args['custom_access_settings'] ) || $args['custom_access_settings'] == 0 ) {
			return $classes;
		}

		$restricted = false;

		if ( !isset( $accessible ) ) return $classes;

		switch( $accessible ) {
			
			case 0:	

				break;
			
			case 1:
			
				if ( is_user_logged_in() )
					$restricted = true;

				break;
				
			case 2:
				
				if ( !is_user_logged_in() ){
					$restricted = true;
				}
				
				$role = get_user_meta( get_current_user_id(), 'role', true );
				
				if ( is_user_logged_in() && isset( $access_roles ) && !empty( $access_roles ) ){
					if ( !in_array( $role, unserialize( $access_roles ) ) ) {
						$restricted = true;
					}
				}
				
				break;
				
		}
		
		if ( $restricted ) {
			$classes[] = 'um-bbpress-restricted';
		}
		
		return $classes;
	}

	add_filter('bbp_has_forums_query','um_bbpress_bbp_has_forums_query');
	function um_bbpress_bbp_has_forums_query( $args ){
		global $wpdb;

    	if( current_user_can("manage_options") ){
    		return $args;
    	}
		
		$forums = new WP_Query( $args );
		$array_forum_IDs = array();
		foreach(  $forums->posts as $forum ){
			$accessible = get_post_meta( $forum->ID, '_um_accessible', true);
			$roles = get_post_meta( $forum->ID, '_um_access_roles', true);

			if( ! is_array( $roles ) ){
				$roles = array();
			}

			if( $accessible == 0 ){ // Everyone

			}

			if( $accessible == 1 ){ // Logged out Users

				if( is_user_logged_in() ){
					 $array_forum_IDs[] = $forum->ID;
				}

			}

			if( $accessible == 2 ){ // Logged in Users
				if( ! in_array( um_user('role') ,$roles ) && count( $roles ) > 1 ){
                      $array_forum_IDs[] = $forum->ID;
				}
			}

		}

		$args['post__not_in'] = $array_forum_IDs;

		return $args;
	}



    add_filter('bbp_has_topics_query','um_bbpress_bbp_has_topics_query');
    function um_bbpress_bbp_has_topics_query( $args ){
    	
    	if( current_user_can("manage_options") ){
    		return $args;
    	}

    	$topics = new WP_Query( $args );
    	$array_topic_IDs = array();

    	foreach ( $topics->posts as $topic ) {
    		$accessible = get_post_meta( $topic->post_parent,'_um_accessible', true);
    		$roles = get_post_meta( $topic->post_parent, '_um_access_roles', true);
    		$custom_access_settings = get_post_meta( $topic->post_parent,'_um_custom_access_settings', true );
    		
    		if( $accessible == 0 ){ // Everyone

			}

			if( $accessible == 1 ){ // Logged out Users

				if( is_user_logged_in() ){
					 $array_topic_IDs[] = $topic->ID;
				}

			}

			if( $accessible == 2 ){ // Logged in Users
				if( ! in_array( um_user('role') ,$roles ) && count( $roles ) > 1 ){
                      $array_topic_IDs[] = $topic->ID;
				}
			}

    		
    	}

    	$args['post__not_in'] = $array_topic_IDs;

    	return $args;
    }

    add_filter('bbp_has_topics','um_bbpress_bbp_has_topics_hide_creation', 10 ,2);
    function um_bbpress_bbp_has_topics_hide_creation( $have_posts, $query ){
    	
    	$post_id = $query->query['post_parent'];

    	if( isset(  $post_id ) ){

    		$accessible = get_post_meta( $post_id ,'_um_accessible', true);
    		$roles = get_post_meta( $post_id , '_um_access_roles', true);
    		$custom_access_settings = get_post_meta( $post_id ,'_um_custom_access_settings', true );
    		$_um_bbpress_can_topic = get_post_meta( $post_id , '_um_bbpress_can_topic', true );
    		
    		if( ! is_array(  $_um_bbpress_can_topic  ) ){
    			 $_um_bbpress_can_topic = array();
    		}

    		if( ! in_array( um_user('role'), $_um_bbpress_can_topic ) && ! empty( $_um_bbpress_can_topic ) ){
	    		add_filter('bbp_current_user_can_access_create_topic_form','um_bbpress_bbp_current_user_can_access_create_topic_form');
			}
			

    	}
    	return $have_posts;
    }

    add_filter('bbp_has_replies_query','um_bbpress_bbp_has_replies_query');
    function um_bbpress_bbp_has_replies_query( $args ){

    	
    	if( current_user_can("manage_options") ){
    		return $args;
    	}

    	$replies = new WP_Query( $args );

    	$topics = new WP_Query( array('post_type' => 'topic', 'post__in' => array( $replies->post->ID ) ) );

    	$post_id = $topics->post->post_parent; 
    	
    	$accessible = get_post_meta( $post_id ,'_um_accessible', true);
    	$roles = get_post_meta( $post_id , '_um_access_roles', true);
    	$custom_access_settings = get_post_meta( $post_id ,'_um_custom_access_settings', true );
    	$_um_bbpress_can_reply = get_post_meta( $post_id , '_um_bbpress_can_reply', true );

			if( $accessible == 0 ){ // Everyone

			}

			if( $accessible == 1 ){ // Logged out Users

				if( is_user_logged_in() ){
					$args['post__in'] = array('0');
				}

			}

			if( $accessible == 2 ){ // Logged in Users
				if( ! in_array( um_user('role') ,$roles ) && count( $roles ) > 1 ){
                     $args['post__in'] = array('0');
				}
			}

			if( ! is_array( $_um_bbpress_can_reply ) ){
				$_um_bbpress_can_reply = array();
			}

			if( ! in_array( um_user('role'), $_um_bbpress_can_reply ) && ! empty( $_um_bbpress_can_reply )  ){
	    		add_filter('bbp_current_user_can_access_create_reply_form','um_bbpress_bbp_current_user_can_access_create_reply_form');
			}

    	return $args;
    }
	
	function um_bbpress_bbp_current_user_can_access_create_topic_form( $retval ){
    	return false;
    }

    function um_bbpress_bbp_current_user_can_access_create_reply_form( $retval ){
    	return false;
    }


 