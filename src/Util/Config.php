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
    public string $username;
    public string $password;
    public string $key;
    public string $webhook_url;
    public string $webhook_title;
    public string $webhook_message;
    public int $webhook_color;

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
            $this->username = $yaml['username'];
            $this->password = password_hash($yaml['password'], PASSWORD_DEFAULT);
            $this->key = $yaml['key'];
            $this->webhook_url = $yaml['webhook_url'];
            $this->webhook_title = $yaml['webhook_title'];
            $this->webhook_message = $yaml['webhook_message'];
            $this->webhook_color = $yaml['webhook_color'];
        } catch (ParseException $e) {
            die("Error parsing YAML file: " . $e->getMessage());
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
?>