<?php
$dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');

if (!defined('base_app')) {
    define('base_app', str_replace('\\', '/', __DIR__) . '/');
}

if (!defined('dev_data')) {
    define('dev_data', $dev_data);
}

// Dynamic BASE_URL detection & Env Support
$env_base_url = getenv('BASE_URL');
if (!empty($env_base_url)) {
    if (substr($env_base_url, -1) !== '/') {
        $env_base_url .= '/';
    }
    if (!defined('base_url')) define('base_url', $env_base_url);
} else {
    if (isset($_SERVER['HTTP_HOST'])) {
        $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                 (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https://' : 'http://';
        // Strip internal port (e.g. 10000 or 80) if leaked by reverse proxy
        $host = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']);
        if (!defined('base_url')) define('base_url', $proto . $host . '/');
    } else {
        if (!defined('base_url')) define('base_url', 'http://localhost/');
    }
}

// Database Credentials from Environment or Defaults
$db_server = getenv('DB_SERVER') ?: (getenv('DB_HOST') ?: "localhost");
$db_port = getenv('DB_PORT') ?: (getenv('MYSQL_PORT') ?: "3306");
$db_username = getenv('DB_USERNAME') ?: (getenv('DB_USER') ?: "root");
$db_password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : "");
$db_name = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: "reps_db");
$db_ssl = getenv('DB_SSL');

if ($db_ssl === null || $db_ssl === false) {
    // Enable SSL by default for cloud hosts (Aiven, remote) or non-standard ports
    $db_ssl = ((int)$db_port !== 3306 || strpos($db_server, 'aivencloud.com') !== false || !in_array($db_server, ['localhost', '127.0.0.1', '::1'])) ? true : false;
} else {
    $db_ssl = filter_var($db_ssl, FILTER_VALIDATE_BOOLEAN) || $db_ssl === '1' || strtolower($db_ssl) === 'true';
}

if (!defined('DB_SERVER')) define('DB_SERVER', $db_server);
if (!defined('DB_PORT')) define('DB_PORT', (int)$db_port);
if (!defined('DB_USERNAME')) define('DB_USERNAME', $db_username);
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', $db_password);
if (!defined('DB_NAME')) define('DB_NAME', $db_name);
if (!defined('DB_SSL')) define('DB_SSL', $db_ssl);
?>
