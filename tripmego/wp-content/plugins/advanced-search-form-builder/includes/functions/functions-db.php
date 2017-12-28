<?php
function ASFB_get_meta_keys( $type = '', $q, $status = 'publish', $args = array()) {
    global $wpdb;

    if ($type == '' || !is_array($type)) return false;

    $limit = 10;
    if (isset($args['limit']) && $args['limit'] > 0) $limit = $args['limit'];

    $query = $wpdb->prepare( "
        SELECT DISTINCT(pm.meta_key) FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE p.post_status = '%s' 
        AND pm.meta_key LIKE '%s'
        AND p.post_type IN (". escape_array_sql($type) .")
        LIMIT %d
    ", $status, '%' . $q . '%', $limit);

    $r = $wpdb->get_col( $query );

    return $r;
}

