<?php
function requireDir($paths) {
    if( ! empty( $paths ) ) {
        foreach ( $paths as $path ) {
            require_once $path;
        }
    }
}

require_once plugin_dir_path( __FILE__ ) . 'cs-framework/cs-framework.php';

requireDir(glob(__DIR__ . '/functions/*.php'));
requireDir(glob(__DIR__ . '/ajax/*.php'));
requireDir(glob(__DIR__ . '/shortcodes/*.php'));
requireDir(glob(__DIR__ . '/action_filters/*.php'));
requireDir(glob(__DIR__ . '/api/*.php'));

