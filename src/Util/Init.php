<?php
namespace Chilly\Util;
use Chilly\Util\Config;
use Chilly\Util\Database;
require_once __DIR__ . '/../../vendor/autoload.php';


class Init
{
    public static Config $config;
    public static Database $db;

    public function __construct() {
        self::$config = new Config(__DIR__ . '/../../config.yml');
        self::$db = new Database(self::$config->db_file);
    }
}
new Init();
?>