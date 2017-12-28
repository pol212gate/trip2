<?php

if (!function_exists('escape_array_sql')) {
    function escape_array_sql($arr){
        global $wpdb;
        $escaped = array();
        foreach($arr as $k => $v){
            if(is_numeric($v))
                $escaped[] = $wpdb->prepare('%d', $v);
            else
                $escaped[] = $wpdb->prepare('%s', $v);
        }
        return implode(',', $escaped);
    }
}

if (!function_exists('asfb_check_folder')) {
    function asfb_check_folder()
    {
        global $ASFB_config;
        if (
            !is_writable($ASFB_config['cache']['dir']) ||
            !is_writable($ASFB_config['cache']['dir'] . 'css') ||
            !is_writable($ASFB_config['cache']['dir'] . 'template')
        ) {

            add_action( 'admin_notices', function(){
                ?>
                <div class="notice notice-error">
                    <p><?php _e( '[ASFB] Please fix permistion folder cache! And re-active plugin.', 'advanced_search_form_builder' ); ?></p>
                </div>
                <?php
            } );
        }
    }
}

if (!function_exists('asfb_check_permision')) {
    function asfb_check_permision()
    {
        $exited = new WP_Query(array(
            'post_type' => 'asfb_post',
            'post_status' => 'any',
            'posts_per_page' => 1
        ));
        if ($exited->have_posts()) {
            global $asfbFlat;

            $asfbFlat['version'] = 'mp';
            add_action( 'admin_notices', function(){
                global $ASFB_config;
                ?>

                <div class="notice notice-error">
                    <p><?php _e( urldecode('Free%20version%20only%201%20form')  .' <a href="'. $ASFB_config['plugin']['url_pro'] .'">'. urldecode('Upgrade%20now').'!</a>', 'advanced_search_form_builder' ); ?></p>
                </div>

                <?php
            });
        }
    }
}

if (!function_exists('asfb_check_error_session')) {
    function asfb_check_error_session()
    {
        if(isset($_SESSION['asfb_error'])) {
            foreach ($_SESSION['asfb_error'] as $key => $item) {
                add_action( 'admin_notices', function() use ($item){
                    ?>
                    <div class="notice notice-error">
                        <p><?php echo $item; ?></p>
                    </div>
                    <?php
                } );

                unset($_SESSION['asfb_error']);
            }
        }

        return true;
    }
}