<?php
namespace Chilly\Util;

class Pterodactyl
{
    private Config $config;
    public function __construct() {
        $this->config = Init::$config;
    }

    /**
     * Internal helper function to set power state
     * @param string $signal power signal
     * @return void
     */
    private function setServerPowerState(string $signal) {
        echo "Setting Power State\n";
        $url = $this->config->pterodactyl_url . "/api/client/servers/{$this->config->server_uuid}/power";
        $data = [
            'signal' => $signal
        ];
        echo "Sending POST request\n";
        $this->__post($url, $data);
    }

    /**
     * Starts server with UUID
     */
    public function startServer() {
        return $this->setServerPowerState("start");
    }

    /**
     * Stops Server with UUID
     */
    public function stopServer() {
        return $this->setServerPowerState("stop");
    }
    
    /**
     * Gets whether server is running or not
     * @return bool whether server is running (true) or not (false)
     */
    public function getServerRunning(): bool {
        $url = $this->config->pterodactyl_url . "/api/client/servers/{$this->config->server_uuid}/resources";
        $response = $this->__get($url);
        echo $response;
        $response_data = json_decode($response, true);
        print_r("JSON DATA: " . $response_data);
        $running = $response_data['attributes']['current_state'] != 'offline';
        return $running;
    }

    /**
     * Creates a server backup for the specified server UUID with the name and locks it
     * @param string $backup_name the name of the new backup
     * @param bool $locked whether to lock the backup or not
     * @return string the UUID of the created backup
     */
    public function createBackup(): string {
        echo "Creating Backup\n";
        $url = $this->config->pterodactyl_url . "/api/client/servers/{$this->config->server_uuid}/backups";
        $response = $this->__post($url, []);
        $response_data = json_decode($response, true);
        $backup_uuid = $response_data['attributes']['uuid'];
        echo "Created Backup with UUID " . $backup_uuid;
        return $backup_uuid;
    }
    /**
     * Checks to see if backup with given UUID on given server UUID is finished
     * @param string $backup_uuid backup UUID to check
     * @return bool whether or not the backup is complete
     */
    public function checkBackup(string $backup_uuid): bool {

        return true;
    }

    /**
     * Deletes file(s) on given server
     * @param array $files array of files
     * @return void
     */
    public function deleteFile(array $files) {

    }

    /**
     * Uploads file to given server at the root and decompresses it, then deletes the compressed version
     * @param string $file_name file in uploads folder
     * @return void
     */
    public function uploadFile(string $file_name) {

        $this->deleteFile([$file_name]);
    }

    /**
     * Internal Helper function, sends GET request with auth headers
     * @param string $url the URL to send it to
     * @return String response The Response
     */
    private function __get(string $url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config->pterodactyl_api_key
        ]);
        echo "Sending Curl\n";
        $response = curl_exec($curl);
        echo "Curl Sent!\n";
        if (curl_errno($curl)) {
            die("Error Encountered while curling: " . curl_error($curl));
        }
        echo "Received Response: $response";
        return $response;
    }

    /**
     * Internal Helper function, sends POST request with auth headers
     * @param string $url the URL to send it to
     * @param array $data The POST data to send
     * @return String response The Response
     */
    private function __post(string $url, array $data) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config->pterodactyl_api_key
        ]);
        echo "Sending Curl\n";
        $response = curl_exec($curl);
        echo "Curl Sent!\n";
        if (curl_errno($curl)) {
            die("Error Encountered while curling: " . curl_error($curl));
        }
        echo "Received Response: $response";
        return $response;
    }
}