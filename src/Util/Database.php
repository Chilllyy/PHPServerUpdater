<?php
namespace Chilly\Util;

use PDO;
use PDOException;

class Database
{
    public static PDO $connection;

    public function __construct(string $file)
    {
        $db_path = __DIR__ . '/../../' . $file;
        try {
            $connection = new PDO('sqlite:' . $db_path);
        } catch (PDOException $e) {
            die("Connection Failed: " . $e->getMessage());
        }
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->exec("CREATE TABLE IF NOT EXISTS server_templates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            template_name TEXT NOT NULL,
            is_next INTEGER NOT NULL DEFAULT 0 CHECK (is_next IN (0,1))
        )");
        Database::$connection = $connection;
    }

    //TODO: DB connection and query methods
    public static function getConnection(): PDO
    {
        return Database::$connection;
    }
}

class ServerTemplate
{
    public int $id;
    public string $template_name;
    public bool $is_next;

    /**
     * Getes all Server Templates
     * @return array Array of ServerTemplate objects
     */
    public static function GetAll(): array {
        $query = "SELECT * FROM server_templates";
        $run = Database::getConnection()->prepare($query);
        $run->execute();
        $templates = [];
        $rows = $run->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $template = new ServerTemplate($row['id'], $row['template_name'], $row['is_next'] == 1);
            $templates[] = $template;
        }
        return $templates;
    }

    /**
     * Gets Random Server Template
     * @return ServerTemplate Template
     */
    public static function GetRandom() {
        $query = "SELECT * FROM server_templates ORDER BY RANDOM() LIMIT 1";
        $run = Database::getConnection()->prepare($query);
        $run->execute([]);
        $row = $run->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new ServerTemplate($row['id'], $row['template_name'], $row['is_next'] == 1);
        }
        return null;
    }

    public static function GetNext() {
        $query = "SELECT * FROM server_templates WHERE is_next = 1 LIMIT 1";
        $run = Database::getConnection()->prepare($query);
        $run->execute();
        $row = $run->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            //There is a "next" chosen
            return new ServerTemplate($row['id'], $row['template_name'], $row['is_next'] == 1);
        } else {
            //There is not a "next" chosen
            return ServerTemplate::GetRandom();
        }
    }

    /**
     * Gets One Server Template
     * @param string $id ID to get
     * @return ServerTemplate returns template
     */
    public static function GetOne(string $id) {
        $query = "SELECT * FROM server_templates WHERE id = :id";
        $run = Database::getConnection()->prepare($query);
        $run->execute([':id' => $id]);
        $row = $run->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new ServerTemplate($row['id'], $row['template_name'], $row['is_next'] == 1);
        }

        return null;
    }

    public static function ClearNext() {
        $query = "UPDATE server_templates SET is_next = 0";
        Database::getConnection()->exec($query);
    }

    /**
     * Creates Server Template and returns it
     * @param string $template_name
     * @return ServerTemplate the newly created template
     */
    public static function Create(string $template_name): ServerTemplate {
        $query = "INSERT INTO server_templates (template_name) VALUES (:name)";
        $run = Database::getConnection()->prepare($query);
        $run->execute([':name' => $template_name]);
        $id = Database::getConnection()->lastInsertId();
        return new ServerTemplate($id, $template_name, false);
    }

    private function __construct(int $id, string $template_name, bool $is_next)
    {
        $this->id = $id;
        $this->template_name = $template_name;
        $this->is_next = $is_next;
    }

    public function markNext() {
        $query = "UPDATE server_templates SET is_next = 0";
        Database::getConnection()->exec($query);
        $query = "UPDATE server_templates SET is_next = 1 WHERE id = :id";
        $run = Database::getConnection()->prepare($query);
        $run->execute([':id' => $this->id]);
        $this->is_next = true;
    }

    public function unmarkNext() {
        $query = "UPDATE server_templates SET is_next = 0 WHERE id = :id";
        $run = Database::getConnection()->prepare($query);
        $run->execute([':id' => $this->id]);
        $this->is_next = false;
    }

    public function delete() {
        $query = "DELETE FROM server_templates WHERE id = :id";
        $run = Database::getConnection()->prepare($query);
        $run->execute([':id' => $this->id]);
        $file = __DIR__ . '/../../uploads/' . $this->id . '.zip';
        if (file_exists($file)) {
            if (unlink($file)) {
                echo "File Deleted!";
            } else {
                echo "Unable to delete file";
            }
        } else {
            echo "Error: The file does not exist.";
        }
    }
}
?>