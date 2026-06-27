<?php
namespace Chilly\Util;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class Config
{
    public string $pterodactyl_url;
    public string $pterodactyl_api_key;
    public string $db_file;
    public string $server_uuid;

    public function __construct(string $file) {
        try {
            if (!file_exists($file)) {
                throw new \Exception("Config file not found: $file");
            }
            $yaml = Yaml::parseFile($file);
            $this->pterodactyl_url = $yaml['panel_url'];
            $this->pterodactyl_api_key = $yaml['panel_api_key'];
            $this->db_file = $yaml['db_file'];
            $this->server_uuid = $yaml['server_uuid'];
        } catch (ParseException $e) {
            die("Error parsing YAML file: " . $e->getMessage());
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
?>