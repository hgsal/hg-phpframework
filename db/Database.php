<?php

namespace app\core\db;

use app\core\Application;

class Database
{
    public \PDO $pdo;


    public function __construct(array $config)
    {
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations()
    {
        $this->createMigrationTable();
        $appliedMigrations = $this->getAppliedMigrations();
        
        $newMigrations = [];
        $files = scandir(Application::$ROOT_DIR.'/migrations');
        $toApplayMigrations = array_diff($files, $appliedMigrations);
        foreach($toApplayMigrations as $migration){
            if($migration === '.' || $migration === '..'){
                continue;
            }

            require_once Application::$ROOT_DIR.'/migrations/' . $migration;
            $classname = pathinfo($migration, PATHINFO_FILENAME);
            $classname = $classname;
            $instance = new $classname;
            $this->log("Applying migration $classname");
            $instance->up();
            $this->log("Applied migration $classname");
            $newMigrations[] = $migration;

        }
        if(!empty($newMigrations)){
            $this->saveMigrations($newMigrations);
        }else{
            $this->log("All migrations are applied");
        }
    }   

    public function createMigrationTable()
    {
        $this->pdo->exec
        (
            "CREATE TABLE IF NOT EXISTS migrations
            (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )"
        );
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration from migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations)
    {
        $str = implode(",", array_map(fn($m) => "('$m')", $migrations));
        $statement = $this->pdo->prepare("INSERT INTO migrations (migration) 
                            VALUES 
                            $str"
                            );
        $statement->execute();
    }

    protected function log($message)
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }

    public function prepare(string $sql)
    {
        return $this->pdo->prepare($sql);
    }

}
