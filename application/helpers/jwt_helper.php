<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '../vendor/autoload.php'; // Sesuaikan path jika diperlukan

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generate_jwt')) {
    function generate_jwt($payload, $expire_time) {
        $CI =& get_instance();
        $CI->load->config('jwt');

        $key = $CI->config->item('jwt_key');
        $algorithm = $CI->config->item('jwt_algorithm');

        // Tambahkan waktu pembuatan token (iat)
        $payload['iat'] = time();
        $payload['exp'] = time() + $expire_time;

        return JWT::encode($payload, $key, $algorithm);
    }
}

if (!function_exists('decode_jwt')) {
    function decode_jwt($token) {
        $CI =& get_instance();
        $CI->load->config('jwt');

        $key = $CI->config->item('jwt_key');
        $algorithm = $CI->config->item('jwt_algorithm');

        try {
            return JWT::decode($token, new Key($key, $algorithm));
        } catch (\Firebase\JWT\ExpiredException $e) {
            return (object) ['error' => 'Token expired'];
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return (object) ['error' => 'Invalid token signature'];
        } catch (Exception $e) {
            return (object) ['error' => 'Invalid token'];
        }
    }
}
