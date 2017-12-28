<?php
class ASFB_search_enpoint
{
    private $name_space;
    private $version;

    function __construct()
    {
        $this->name_space = 'asfb';
        $this->version = 'v1';

        register_rest_route( $this->name_space, $this->version . '/search', array(
            array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array( $this, 'search' )
            ),
        ) );

        register_rest_route( $this->name_space, $this->version . '/suggestion', array(
            array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array( $this, 'suggestion' )
            ),
        ) );
    }

    public function search()
    {
        $time_start = microtime(true);

        global $ASFB_config;
        global $post;
        $after = '';
        $before = '';

        $form_id = ASFB_request::getQuery('form_id', '');
        $dataForm = get_post_meta( $form_id, $ASFB_config['default_setting']['key_post_meta_form'], true);

        $searchResultPost = array();
        if ($dataForm['include_live_result'] == 1) {
            $args = self::buildQuery($form_id);
            $args['posts_per_page'] = $dataForm['live_result_limit'];

            $cacheName = md5(serialize($args));
            $cacheData = ASFB_cache::get($cacheName);

            if (empty($cacheData)) {
                $searchResult = new WP_Query( $args );
                $searchResultPost = array(
                    'total' => $searchResult->found_posts,
                    'result' => $searchResult->get_posts()
                );

                ASFB_cache::save($cacheName, $searchResultPost);
            } else {
                $searchResultPost = json_decode(file_get_contents($cacheData['path_file']), true);
            }

            $results = array();

            foreach ($searchResultPost['result'] as $key => $item) {
                $item = (array)$item;
                $item['html'] = '';

                $post = get_post($item['ID']);
                setup_postdata($post);

                ob_start();

                ?>
                <div class="asfbItemAjax">
                    <a href="<?php the_permalink() ?>">
                        <span class="asfbWrapImage">
                            <?php the_post_thumbnail() ?>
                        </span>
                        <span class="asfbTitle">
                            <?php the_title() ?>
                        </span>
                    </a>
                </div>
                <?php

                $item['html'] .= trim(ob_get_clean());

                $results[] = $item;
            }
            wp_reset_postdata();

            $searchResultPost['result'] = $results;

            if (
                class_exists( 'WooCommerce' )
                && isset($dataForm['styling_result_woo'])
                && $dataForm['styling_result_woo'] == 1
            ) {
                $after = trim(woocommerce_product_loop_end(false));
                $before = trim(woocommerce_product_loop_start(false));
            }
        }

        $resultsFinal = array(
            'result' => $searchResultPost['result'],
            'number_post' => count($searchResultPost['result']),
            'total' => $searchResultPost['total'],
            'after' => $after,
            'before' => $before,
        );

        do_action('asfb_before_return_search_result', $resultsFinal);

        $time_end = microtime(true);

        return array(
            'result' => $searchResultPost['result'],
            'number_post' => count($searchResultPost['result']),
            'total' => $searchResultPost['total'],
            'after' => $after,
            'before' => $before,
            'time' => $time_end - $time_start
        );
    }

    public function suggestion()
    {
        $time_start = microtime(true); 

        global $wpdb;
        global $ASFB_config;
        global $post;

        $q = ASFB_request::getQuery('q', '');
        $post_type = ASFB_request::getQuery('post_type', array());
        $taxonomies = ASFB_request::getQuery('taxonomies', array());
        $form_id = ASFB_request::getQuery('form_id', '');

        $dataForm = get_post_meta( $form_id, $ASFB_config['default_setting']['key_post_meta_form'], true);

        if ( empty($post_type) || empty($taxonomies) ) {
            return array(
                'status' => 400,
                'message' => 'Error params'
            );
        }

        if ($dataForm['autocom_post_title'] == 1) {
            $query = $this->buildQuerySuggestionTitlePost($post_type, $q, 6);
            $posts = $wpdb->get_col( $query );
        } else {
            $posts = array();
        }

        if ( is_array($dataForm['autocom_tax_title']) && count($dataForm['autocom_tax_title']) > 0) {
            $queryTax = $this->buildQuerySuggestionTitleTaxonomy($taxonomies, $post_type, $q, 6);
            $terms = $wpdb->get_results($queryTax);
        } else {
            $terms = array();
        }

        $after = '';
        $before = '';
        $searchResultPost = array(
            'result' => array(),
            'total' => 0
        );

        $time_end = microtime(true); 

        return array(
            'suggestion' => array(
                'post' => $posts,
                'terms' => $terms,
            ),
            'result' => $searchResultPost['result'],
            'number_post' => count($searchResultPost['result']),
            'total' => $searchResultPost['total'],
            'after' => $after,
            'before' => $before,
            'time' => $time_end - $time_start
        );
    }

    static function buildQuery($form_id)
    {
        if ($form_id == '') return '';

        global $ASFB_config;
        $formBuilder = get_post_meta($form_id, $ASFB_config['default_setting']['key_post_meta_form'], true);

        $args =array();

        $query = ASFB_request::getQuery('q');

        $args['post_type'] = $formBuilder['filter_post_type_source'];
        $args['posts_per_page'] = $formBuilder['posts_per_page'];
        $args['paged'] = max(ASFB_request::getQuery('s_page'), 1);
        $args['s'] = $query;

        if ( isset($formBuilder['pf_content']) && $formBuilder['pf_content'] != 1) {
            $args['ASFB_deny_content'] = true;
        }
        if ( isset($formBuilder['pf_title']) && $formBuilder['pf_title'] != 1) {
            $args['ASFB_deny_title'] = true;
        }
        if ( isset($formBuilder['pf_excerpt']) && $formBuilder['pf_excerpt'] != 1) {
            $args['ASFB_deny_excerpt'] = true;
        }
        $args['ASFB_main_query'] = 1;

        //==================================================================
        $taxonomy = ASFB_request::getQuery('taxonomy');
        $taxQuery = array();
        if ( is_array($taxonomy) && count($taxonomy)) {
            foreach ($taxonomy as $key => $value) {

                if (empty($value))
                    continue;

                if (!is_array($value) && $value != '') {
                    $value = array($value);
                }

                $value = array_filter($value);

                if ( !empty($value) && count($value) > 0) {
                    $taxQuery[] = array(
                        'taxonomy' => $key,
                        'field' => 'term_id',
                        'terms' => $value,
                        'operator' => 'IN'
                    );
                }
            }
            if (count($taxQuery) > 0) {
                $taxQuery['relation'] = $formBuilder['tax_relation'];
                $args['tax_query'] = $taxQuery;
            }
        }

        //==================================================================
        $cf = ASFB_request::getQuery('custom_field');
        $cfQuery = array();

        $compare = array();
        if (isset($formBuilder['cf'])) {
            foreach ($formBuilder['cf'] as $k => $v) {
                $compare[trim($v['cf_key_name'])] = $v['cf_compare'];
            }
        }

        if (is_array($cf) && count($cf) > 0) {
            foreach ($cf as $key => $value) {
                $key = trim($key);
                if (is_array($value)) {
                    if ( isset($value['min']) || isset($value['max'])) {
                        if ( !isset($value['min']) ) {
                            $cfQuery[] = array(
                                'key'     => $key,
                                'value'   => $value['max'],
                                'compare' => '<=',
                                'type' => 'numeric'
                            );
                        } elseif ( !isset($value['max']) ) {
                            $cfQuery[] = array(
                                'key'     => $key,
                                'value'   => $value['min'],
                                'compare' => '>=',
                                'type' => 'numeric'
                            );
                        } else {
                            $cfQuery[] = array(
                                'relation' => 'AND',
                                array(
                                    'key'     => $key,
                                    'value'   => $value['min'],
                                    'compare' => '>=',
                                    'type' => 'numeric'
                                ),
                                array(
                                    'key'     => $key,
                                    'value'   => $value['max'],
                                    'compare' => '<=',
                                    'type' => 'numeric'
                                )
                            );
                        }
                    } else {
                        $orMetaQuery = array();
                        foreach ($value as $_key => $_value) {

                            if (isset($compare[$key])){
                                $orMetaQuery[] = array(
                                    'key'     => $key,
                                    'value'   => $_value,
                                    'compare' => $compare[$key]
                                );
                            }
                        }
                        if (count($orMetaQuery) > 0) {
                            $orMetaQuery['relation'] = 'OR';
                            $cfQuery[] = $orMetaQuery;
                        }
                    }
                } else if (!empty($value)){
                    $cfQuery[] = array(
                        'key'     => $key,
                        'value'   => $value,
                        'compare' => $compare[$key]
                    );
                }
            }
            if (count($cfQuery) > 0) {
                $cfQuery['relation'] = $formBuilder['cf_relation'];
                $args['meta_query']  = $cfQuery;
            }
        }

        //==================================================================

        return apply_filters('asfb_query_search', $args);
    }

    private function buildQuerySuggestionTitlePost($post_type, $q, $limit)
    {
        global $wpdb;
        $sql = $wpdb->prepare( "
            SELECT DISTINCT(p.post_title) FROM {$wpdb->posts} p
            WHERE p.post_status = '%s' 
            AND p.post_title LIKE '%s'
            AND p.post_type IN (". escape_array_sql($post_type) .")
            LIMIT %d
        ", 'publish', $q . '%', $limit);

        return apply_filters('asfb_query_suggestion_title', $sql);
    }

    private function buildQuerySuggestionTitleTaxonomy($taxonomies, $post_type, $q, $limit)
    {
        global $wpdb;
        return $wpdb->prepare( "
            SELECT 
              DISTINCT(t.name), tx.taxonomy, tx.term_id
            FROM {$wpdb->terms} t
            LEFT JOIN {$wpdb->term_relationships} tr ON t.term_id = tr.term_taxonomy_id
            LEFT JOIN {$wpdb->term_taxonomy} tx ON tx.term_id = t.term_id
            LEFT JOIN {$wpdb->posts} p ON tr.object_id = p.ID
            
            WHERE 
              p.post_status = '%s' 
              AND p.post_title LIKE '%s'
              AND p.post_type IN (". escape_array_sql($post_type) .")
              AND tx.taxonomy IN (". escape_array_sql($taxonomies) .")
            
            LIMIT %d
        ", 'publish', '%' . $q . '%', $limit);
    }
}

add_action('rest_api_init', function (){
    new ASFB_search_enpoint();
});