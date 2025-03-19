<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generate_jwt')) {
    function generate_jwt($payload, $expire_time = null) {
        $CI =& get_instance();
        $CI->load->config('jwt');
        $CI->load->helper('url'); // Pastikan helper URL sudah dimuat

        $key = $CI->config->item('jwt_key');
        $algorithm = $CI->config->item('jwt_algorithm');

        $expire_time = $expire_time ?? $CI->config->item('jwt_expire_time');

        $payload['sub'] = $payload['user_id'] ?? null; // Subject (user_id)
        $payload['iat'] = time(); // Waktu token dibuat
        $payload['exp'] = time() + (int) $expire_time; // Waktu kadaluarsa
        $payload['iss'] = base_url(); // Issuer (server yang mengeluarkan token)
        $payload['aud'] = 'client'; // Audience (misalnya frontend/mobile)

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

            if (isset($decoded->exp) && $decoded->exp < time()) {
                log_message('error', 'JWT expired');
                return null; // Token expired
            }

            return $decoded;
        } catch (Firebase\JWT\ExpiredException $e) {
            log_message('error', 'JWT Expired: ' . $e->getMessage());
            return null;
        } catch (Firebase\JWT\SignatureInvalidException $e) {
            log_message('error', 'Invalid JWT Signature: ' . $e->getMessage());
            return null;
        } catch (Exception $e) {
            log_message('error', 'Invalid JWT: ' . $e->getMessage());
            return null;
        }
    }
}