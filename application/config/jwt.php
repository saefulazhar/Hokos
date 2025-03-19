<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['jwt_key'] = 'mantapKali'; // secret key 
$config['jwt_algorithm'] = 'HS256'; // Algoritma JWT
$config['jwt_expire_time'] = 3600; // 1 jam untuk Access Token
$config['jwt_refresh_expire_time'] = 2592000; // 30 hari untuk Refresh Token
