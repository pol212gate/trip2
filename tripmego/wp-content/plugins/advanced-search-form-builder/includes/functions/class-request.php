<?php


class ASFB_request
{
    static function getQuery($key, $default = '')
    {
        if ( isset($_GET[$key]) ) {
            return $_GET[$key];
        } else {
            return $default;
        }
    }

    static function getPost($key, $default = '')
    {
        if ( isset($_POST[$key]) ) {
            return $_POST[$key];
        } else {
            return $default;
        }
    }
}