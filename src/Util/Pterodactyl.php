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
        $url = $this->config->pterodactyl_url;
        $data = [
            'signal' => $signal
        ];
        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->config->pterodactyl_api_key
        ]);
        echo "Sending Curl\n";
        $response = curl_exec($ch);
        echo "Curl sent\n";
        if (curl_errno($ch)) {
            die("Error encountered: " . curl_error($ch));
        }
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

        return true;
    }

    /**
     * Creates a server backup for the specified server UUID with the name and locks it
     * @param string $backup_name the name of the new backup
     * @param bool $locked whether to lock the backup or not
     * @return string the UUID of the created backup
     */
    public function createBackup(string $backup_name, bool $locked): string {

        return "";
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
}