<?php

class ASFB_cache
{
    static $tableCache = 'asfb_cache';

    static function get($cacheName) {
        global $wpdb;
        $query = $wpdb->prepare("
            SELECT * 
            FROM {$wpdb->prefix}". self::$tableCache ." c
            WHERE  c.key_name = '%s'
        ", $cacheName);

        $cacheData = $wpdb->get_row($query, 'ARRAY_A');
        if ( !file_exists($cacheData['path_file'])
            || $cacheData['expired_time'] <= time() ) {
            $cacheData = false;
        } else {
            $cacheData['expired_time'] = date('H:i:s d-m-Y', $cacheData['expired_time']);
        }
        return $cacheData;
    }

    static  function save($cacheName, $data, $lifeTime = '') {
        global $wpdb;
        global $ASFB_config;

        $wpdb->delete( $wpdb->prefix . self::$tableCache, array( 'key_name' => $cacheName ) );

        $m = date('m', time());
        $y = date('Y', time());
        $d = date('d', time());

        $path = $ASFB_config['cache']['dir']. $y;
        if ( !is_dir($path ) ) {
            @mkdir($path);
        }

        $path .= '/' . $m;
        if (!is_dir($path)) {
            @mkdir($path);
        }

        $path .= '/' . $d;
        if (!is_dir($path)) {
            @mkdir($path);
        }

        $path .= '/' . $cacheName . '.json';
        if (is_array($data)) {
            $content = json_encode($data);
        } else {
            $content = $data;
        }

        $fp = fopen($path,"wb");
        fwrite($fp,$content);
        fclose($fp);

        $dataSave = array(
            'key_name' => $cacheName,
            'path_file' => $path,
            'expired_time' => time() + $ASFB_config['cache']['lifetime']
        );
        $wpdb->insert(
            $wpdb->prefix . self::$tableCache,
            $dataSave,
            array(
                '%s',
                '%s',
                '%s',
            )
        );

        $dataSave['id'] = $wpdb->insert_id;
        $dataSave['expired_time'] = date('H:i:s d-m-Y', $dataSave['expired_time']);

        return $dataSave;
    }
}