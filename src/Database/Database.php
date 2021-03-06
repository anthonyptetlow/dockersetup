<?php

namespace App\Database;

use App\Config;
use App\FileManager;

class Database implements DatabaseInterface
{
    /**
     * @var array
     */
    protected const PORTS = [
        'mysql' => 3306,
        'postgres' => 5432
    ];

    /**
     * @var FileManager
     */
    protected FileManager $fileManager;

    /**
     * Database constructor.
     *
     * @param FileManager $fileManager
     */
    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * @inheritDoc
     */
    public function setup(): void
    {
        $rootDirectory = Config::get('rootDirectory');

        $this->fileManager->createFileContent("{$rootDirectory}/config/default/databaseMappings.json", $this->portMappings());
    }

    /**
     * Generate the port mappings.
     *
     * @return string
     */
    private function portMappings(): string
    {
        $databases = Config::get('docker.services.db');

        array_walk($databases, static function(&$database, $key) {
            $basePort = self::PORTS[$key];
            $versionPort = $basePort + 1;

            $database = array_filter($database, static function($key) {
                return $key !== '_default';
            }, ARRAY_FILTER_USE_KEY);

            $database = array_map(static function() use ($basePort, &$versionPort) {
                $versionPort--;

                return "{$versionPort}:{$basePort}";
            }, $database);
        });

        return json_encode($databases, JSON_THROW_ON_ERROR);
    }
}
