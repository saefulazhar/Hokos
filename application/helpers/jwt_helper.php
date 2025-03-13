<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '../vendor/autoload.php'; // Sesuaikan path jika diperlukan

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generate_jwt')) {
    function generate_jwt($payload, $expire_time = null) {
        $CI =& get_instance();
        $CI->load->config('jwt');
    
        $key = $CI->config->item('jwt_key');
        $algorithm = $CI->config->item('jwt_algorithm');
    
        // Gunakan waktu default jika tidak diberikan
        $expire_time = $expire_time ?? $CI->config->item('jwt_expire_time');
    
        // Debugging
        error_log("Expire Time: " . $expire_time);
    
        $payload['iat'] = time();
        $payload['exp'] = time() + (int) $expire_time;
    
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
            $decoded = JWT::decode($token, new Key($key, $algorithm));
    
            // Debugging
            error_log("Decoded Token: " . print_r($decoded, true));
    
            // Periksa apakah token sudah expired
            if (isset($decoded->exp) && $decoded->exp < time()) {
                error_log("Token expired. Exp: " . $decoded->exp . ", Now: " . time());
                return (object) ['error' => 'Token expired'];
            }
    
            return $decoded;
        } catch (Firebase\JWT\ExpiredException $e) {
            error_log("ExpiredException: " . $e->getMessage());
            return (object) ['error' => 'Token expired'];
        } catch (Firebase\JWT\SignatureInvalidException $e) {
            error_log("SignatureInvalidException: " . $e->getMessage());
            return (object) ['error' => 'Invalid token signature'];
        } catch (Exception $e) {
            error_log("Exception: " . $e->getMessage());
            return (object) ['error' => 'Invalid token'];
        }
    }
}
