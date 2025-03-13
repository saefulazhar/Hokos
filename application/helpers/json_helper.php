<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('show_json')) {
    function show_json($data, $status_code = 200) {
        $CI =& get_instance();
        $CI->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode($data, JSON_PRETTY_PRINT))
            ->_display();
        exit;
    }
}
