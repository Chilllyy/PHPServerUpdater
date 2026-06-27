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
        $this->post($url, $data);
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
        $response = $this->get($url);
        $response_data = json_decode($response, true);
        $running = $response_data['attributes']['current_state'] != 'offline';
        return $running;
    }

    /**
     * Creates a server backup for the specified server UUID with the name and locks it
     * @param string $backup_name the name of the new backup
     * @param bool $locked whether to lock the backup or not
     * @return string | array string containing the backup UUID, or an array of the errors
     */
    public function createBackup() {
        echo "Creating Backup\n";
        $url = $this->config->pterodactyl_url . "/api/client/servers/{$this->config->server_uuid}/backups";
        $response = $this->post($url, []);
        $response_data = json_decode($response, true);
        if (isset($response_data['errors'])) {
            return $response_data;
        }
        $backup_uuid = $response_data['attributes']['uuid'];
        return $backup_uuid;
    }
    /**
     * Checks to see if backup with given UUID on given server UUID is finished
     * @param string $backup_uuid backup UUID to check
     * @return bool whether or not the backup is complete
     */
    public function checkBackup(string $backup_uuid): bool {
        $url = $this->config->pterodactyl_url . "/api/client/servers/{$this->config->server_uuid}/backups/$backup_uuid";
        $response = $this->get($url);
        $response_data = json_decode($response, true);
        return $response_data['attributes']['is_successful'];
    }

    /**
     * Deletes file(s) on given server
     * @param array $files array of files
     * @return void
     */
    public function deleteFile(array $files) {
        $url = $this->config->pterodactyl_url . "/api/client/servers/{$this->config->server_uuid}/files/delete";
        $data = [
            'root' => '/',
            'files'=> $files
        ];
        $this->post($url, $data);
    }

    /**
     * Uploads file to given server at the root and decompresses it, then deletes the compressed version
     * @param string $file_name file in uploads folder
     * @return void
     */
    public function uploadFile(string $file_name, string $name) {
        $url = $this->getUploadURL();
        $path = realpath($file_name);

        $this->postFile($url, $path, $name);//Upload File

        $url = $this->config->pterodactyl_url . "/api/client/servers/{$this->config->server_uuid}/files/decompress";
        $data = [
            'root' => '/',
            'file' => $name
        ];

        $this->post($url, $data); //Decompress File
        
        $this->deleteFile([$name]);//Delete File
    }

    /**
     * Helper function to get Upload URL with JWT Token
     * @return string URL to upload to
     */
    private function getUploadURL() {
        $url = $this->config->pterodactyl_url . "/api/client/servers/{$this->config->server_uuid}/files/upload?directory=/";
        $response = $this->get($url);
        $response_json = json_decode($response, true);
        $jwt_url = $response_json['attributes']['url'];
        return $jwt_url;
    }

    /**
     * Internal Helper function, sends GET request with auth headers
     * @param string $url the URL to send it to
     * @return String response The Response
     */
    private function get(string $url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config->pterodactyl_api_key,
            'Accept: Application/vnd.pterodactyl.v1+json'
        ]);
        echo "Sending Curl\n";
        $response = curl_exec($curl);
        echo "Curl Sent!\n";
        if (curl_errno($curl)) {
            die("Error Encountered while curling: " . curl_error($curl));
        }
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $time = 60;
        $attempts = 2;
        while ($code == 429) {
            echo "Received code 429, waiting $time seconds for rate limit";
            $attempts++;
            $time = $attempts * 30;
            sleep($time);
            return $response = $this->get($url);
        }
        echo "Received Response: $response\n";
        return $response;
    }

    /**
     * Internal Helper function, sends POST request with auth headers
     * @param string $url the URL to send it to
     * @param array $data The POST data to send
     * @return String response The Response
     */
    private function post(string $url, array $data) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config->pterodactyl_api_key,
            'Accept: Application/vnd.pterodactyl.v1+json'
        ]);
        echo "Sending Curl\n";
        $response = curl_exec($curl);
        echo "Curl Sent!\n";
        if (curl_errno($curl)) {
            die("Error Encountered while curling: " . curl_error($curl));
        }
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $time = 60;
        $attempts = 2;
        while ($code == 429) {
            echo "Received code 429, waiting $time seconds for rate limit";
            $attempts++;
            $time = $attempts * 30;
            sleep($time);
            return $response = $this->post($url, $data);
        }
        echo "Received Response: $response\n";
        return $response;
    }

    private function postFile(string $url, string $files, string $filename) {
        $curlFile = curl_file_create($files, 'application/zip', $filename);
        $data = [
            'files'=>$curlFile
        ];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: multipart/form-data',
            'Authorization: Bearer ' . $this->config->pterodactyl_api_key,
            'Accept: Application/vnd.pterodactyl.v1+json'
        ]);
        echo "Sending Curl\n";
        $response = curl_exec($curl);
        echo "Curl Sent!\n";
        if (curl_errno($curl)) {
            die("Error Encountered while curling: " . curl_error($curl));
        }
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $time = 60;
        $attempts = 2;
        while ($code == 429) {
            echo "Received code 429, waiting $time seconds for rate limit";
            $attempts++;
            $time = $attempts * 30;
            sleep($time);
            return $response = $this->post($url, $data);
        }
        echo "Received Response: $response\n";
        return $response;
    }
}