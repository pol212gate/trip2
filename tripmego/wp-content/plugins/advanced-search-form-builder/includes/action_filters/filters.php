<?php 

$ASFB_filters = new ASFB_filters();
add_filter( 'cs_framework_override', array($ASFB_filters, 'changeFolderCs'));
add_filter( 'posts_search', array($ASFB_filters, 'modifySearchQuery'), 600, 2 );
add_filter( 'cs_save_post', array($ASFB_filters, 'cs_save_post'), 600, 2 );
add_filter( 'cs_validate_save', array($ASFB_filters, 'cs_validate_save'), 600, 2 );


class ASFB_filters {

    public function changeFolderCs($path) {
        return ASFB_FOLDER_PLUGIN . '/includes/' . $path; 
    }

    public function modifySearchQuery($search, &$wp_query) {
        global $wpdb;
        if ( empty( $search ) )
            return $search; // skip processing - no search term in query
        $q = $wp_query->query_vars;
        $n = ! empty( $q['exact'] ) ? '' : '%';
        $searchand = '';

        $query = array();

        if (!isset($q['s'])) {
            $q['s'] = '';
        }

        $term = esc_sql( $wpdb->esc_like( $q['s'] ) );
        $query['title'] = "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $query['content'] = "{$searchand}($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
        $query['excerpt'] = "{$searchand}($wpdb->posts.post_excerpt LIKE '{$n}{$term}{$n}')";


        if ( isset($wp_query->query_vars['ASFB_deny_title']) && $wp_query->query_vars['ASFB_deny_title'] == 1 ) {
            unset($query['title']);
        }
        if ( isset($wp_query->query_vars['ASFB_deny_content']) && $wp_query->query_vars['ASFB_deny_content'] == 1 ) {
            unset($query['content']);
        }
        if ( isset($wp_query->query_vars['ASFB_deny_excerpt']) && $wp_query->query_vars['ASFB_deny_excerpt'] == 1 ) {
            unset($query['excerpt']);
        }

        $search = implode('OR', $query);
        if (!empty($search)) {
            $search = " AND ({$search}) ";
            if (!is_user_logged_in())
                $search .= " AND ($wpdb->posts.post_password = '') ";
        }

        return $search;
    }

    public function cs_save_post($options) {
        global $post;
        global $ASFB_config;

        $fileName = $post->ID;
        $str = $options['styling_result_column_template'];
        $str = str_replace('\"', '"', $str);
        $str = str_replace("\'", "'", $str);

        if (empty($str)) {
            $str = __('Template not found', 'advanced_search_form_builder');
        }

        if ( !is_dir($ASFB_config['cache']['dir'] . 'template') ) {
            @mkdir($ASFB_config['cache']['dir'] . 'template');
        }

        $path = $ASFB_config['cache']['dir'] . 'template/'. $fileName . '.php';

        $fp = fopen($path,"wb");

        fwrite($fp,$str);
        fclose($fp);
        $options['path_template'] = $path;

        if (count($options['cf']) > 3) {
            $options['cf_relation'] = 'AND';
        }

        return $options;
    }

    public function cs_validate_save($options) {
        echo '<pre>';
        global $ASFB_config;
        foreach ($ASFB_config['form_filter']['type_input'] as $key => $item) {

            if ( isset($options[$key . '_styling_item']) ) {
                $pathFileScss = ASFB_PATH . 'public/css/'. $key .'.less';
                $options[$key . '_styling_item'] = $this->buildCssStyle($options[ $key . '_styling_item'], $pathFileScss, $key);
            }

        }

        return $options;
    }

    private function buildCssStyle($typeData, $pathFileScss, $type) {
        $variableCss = file_get_contents(ASFB_PATH . 'public/css/variable.json');
        $variableCss = json_decode($variableCss, true);

        $text_styling_new = array();
        if (is_array($typeData) && count($typeData) > 0) {
            foreach ($typeData as $item) {
                if( isset($item['text_label']) && trim($item['text_label']) != '' ) {
                    $fileName = sanitize_title(str_replace(' ', '', $item['text_label']));

                    $styling_group = array();

                    if(is_array($variableCss[$type]['data']) && count($variableCss[$type]['data']) > 0) {
                        foreach ($variableCss[$type]['data'] as $_item) {
                            if (
                                isset($_item['name'])
                                &&
                                isset($_item['default'])
                            ) {
                                if (isset($_item['name']) && isset($item['styling_group'][$_item['name']])) {
                                    $styling_group[$_item['name']] = $item['styling_group'][$_item['name']];
                                } else {
                                    $styling_group[$_item['name']] = $_item['default'];
                                }
                            }
                        }
                    }

                    $text_styling_new[] = array(
                        'text_label' => $fileName,
                        'styling_group' => $styling_group,
                        'css_file' => $this->saveCss($fileName, $pathFileScss, $styling_group, $type)
                    );
                }
            }
        }

        return $text_styling_new;
    }

    private function saveCss($fileName, $pathFileScss, $variable, $type) {
        global $ASFB_config;

        require_once ASFB_PATH . 'vendor/lessphp/lessc.inc.php';

        $less = new lessc;

        $classStyle = $type . '_' . $fileName;
        $pathFileCss = $ASFB_config['cache']['dir'] . 'css/' . $classStyle . '.css';

        $varScss = '';

        if (count($variable) > 0) {
            foreach ($variable as $_key => $_item) {
                if (!empty($_item)) {
                    $varScss .= '@' . $_key . ': ' . $_item . ';' ;
                }
            }

            $varScss .= '@classWrap: ' . $classStyle . ';';
            $varScss .= '@themeSelect: ' . $classStyle . ';';

            $_contentScss = file_get_contents($pathFileScss);
            $_contentScss = explode('//variable//', $_contentScss);

            if (!isset($_contentScss[1])) {
                $_contentScss[1] = '';
            }

            if($type != 'select') {
                $varScss = $_contentScss[0] . $varScss . '.asfbFormWrapper { ' . $_contentScss[1] . ' }';
            } else {
                $varScss = $_contentScss[0] . $varScss . $_contentScss[1];
            }

            $cssCompilered = $less->compile($varScss);
            $cssCompilered = str_replace($classStyle, '.' . $classStyle, $cssCompilered);


            if(!is_dir($ASFB_config['cache']['dir'] . 'css')) {
                @mkdir($ASFB_config['cache']['dir'] . 'css');
            }

            $fp = @fopen($pathFileCss, 'wb');
            if (!$fp) {
                $_SESSION['asfb_error'][$classStyle] =  __('[ASFB] failed to open stream: Permission denied file  '. $pathFileCss .'.css .', 'advanced_search_form_builder');
            }

            fwrite($fp, $cssCompilered);
            fclose($fp);

            return  array(
                'url' => plugin_dir_url($pathFileCss). $classStyle . '.css',
                'class' => $classStyle
            );
        }
    }
}

