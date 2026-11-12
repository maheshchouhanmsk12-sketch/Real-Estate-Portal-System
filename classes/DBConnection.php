<?php
if (!defined('DB_SERVER')) {
    require_once(dirname(__DIR__) . "/initialize.php");
}

class DBConnection {

    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    private $ssl;
    
    public $conn;
    public $conn_error = null;
    
    public function __construct() {
        // PHP 8.x Safe Property Initializations inside constructor
        $this->host = defined('DB_SERVER') ? DB_SERVER : (getenv('DB_SERVER') ?: 'localhost');
        $this->username = defined('DB_USERNAME') ? DB_USERNAME : (getenv('DB_USERNAME') ?: 'root');
        $this->password = defined('DB_PASSWORD') ? DB_PASSWORD : (getenv('DB_PASSWORD') ?: '');
        $this->database = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'reps_db');
        $this->port = defined('DB_PORT') ? (int)DB_PORT : (int)(getenv('DB_PORT') ?: 3306);
        $this->ssl = defined('DB_SSL') ? DB_SSL : ((getenv('DB_SSL') !== false) ? (filter_var(getenv('DB_SSL'), FILTER_VALIDATE_BOOLEAN) || getenv('DB_SSL') === '1' || strtolower(getenv('DB_SSL')) === 'true') : false);

        if (!isset($this->conn) || !$this->conn) {
            mysqli_report(MYSQLI_REPORT_OFF);
            $this->conn = mysqli_init();

            if (!$this->conn) {
                $this->conn_error = 'mysqli_init initialization failed';
                return;
            }

            // Connection timeout (15 seconds for cloud databases like Aiven)
            $this->conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 15);

            $flags = 0;
            // Aiven MySQL SSL requirement check
            $is_cloud_db = $this->ssl || 
                           ((int)$this->port !== 3306) || 
                           (strpos($this->host, 'aivencloud.com') !== false) ||
                           (!in_array($this->host, ['localhost', '127.0.0.1', '::1']));

            if ($is_cloud_db) {
                $this->conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
                $flags = MYSQLI_CLIENT_SSL;
            }

            $connected = @$this->conn->real_connect(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port,
                NULL,
                $flags
            );

            // Fallback attempt without SSL flag if remote SSL was not required
            if (!$connected && $flags !== 0) {
                $this->conn = mysqli_init();
                $this->conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
                $connected = @$this->conn->real_connect(
                    $this->host,
                    $this->username,
                    $this->password,
                    $this->database,
                    $this->port
                );
            }

            if (!$connected || $this->conn->connect_error) {
                $this->conn_error = $this->conn ? $this->conn->connect_error : mysqli_connect_error();
            } else {
                $this->conn->set_charset("utf8mb4");
            }
        }    
    }

    public function __destruct() {
        if ($this->conn && $this->conn instanceof mysqli) {
            @$this->conn->close();
        }
    }
}
?>
