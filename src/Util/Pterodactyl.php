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
     * @param string $server_uuid server UUID
     * @param string $signal power signal
     * @return void
     */
    private function setServerPowerState(string $server_uuid, string $signal) {

    }

    /**
     * Starts server with UUID
     * @param string $server_uuid server to start
     */
    public function startServer(string $server_uuid) {
        return $this->setServerPowerState($server_uuid, "start");
    }

    /**
     * Stops Server with UUID
     * @param string $server_uuid server to stop
     */
    public function stopServer(string $server_uuid) {
        return $this->setServerPowerState($server_uuid, "stop");
    }
    
    /**
     * Gets whether server is running or not
     * @param string $server_uuid UUID to check
     * @return bool whether server is running (true) or not (false)
     */
    public function getServerRunning(string $server_uuid): bool {

        return true;
    }

    /**
     * Creates a server backup for the specified server UUID with the name and locks it
     * @param string $server_uuid the UUID Of server to create backup of
     * @param string $backup_name the name of the new backup
     * @param bool $locked whether to lock the backup or not
     * @return string the UUID of the created backup
     */
    public function createBackup(string $server_uuid, string $backup_name, bool $locked): string {

        return "";
    }
    /**
     * Checks to see if backup with given UUID on given server UUID is finished
     * @param string $server_uuid server UUID to check
     * @param string $backup_uuid backup UUID to check
     * @return bool whether or not the backup is complete
     */
    public function checkBackup(string $server_uuid, string $backup_uuid): bool {

        return true;
    }

    /**
     * Deletes file(s) on given server
     * @param string $server_uuid server UUID to delete
     * @param array $files array of files
     * @return void
     */
    public function deleteFile(string $server_uuid, array $files) {

    }

    /**
     * Uploads file to given server at the root and decompresses it, then deletes the compressed version
     * @param string $server_uuid UUID to upload to
     * @param string $file_name file in uploads folder
     * @return void
     */
    public function uploadFile(string $server_uuid, string $file_name) {

        $this->deleteFile($server_uuid, [$file_name]);
    }
}