<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = self::resoudreConfig();

            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}";

            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => true,
                ]);
            } catch (PDOException $e) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    /**
     * Sur Upsun (production), les identifiants arrivent via la variable
     * d'environnement PLATFORM_RELATIONSHIPS (JSON encodé en base64).
     * En local (WAMP), cette variable n'existe pas : on retombe sur config.php.
     */
    private static function resoudreConfig(): array
    {
        $relationships = getenv('PLATFORM_RELATIONSHIPS');

        if ($relationships !== false) {
            $decoded = json_decode(base64_decode($relationships), true);
            $db = $decoded['database'][0]; // nom défini dans .upsun/config.yaml (relationships: database)

            return [
                'host' => $db['host'],
                'port' => $db['port'],
                'name' => $db['path'],
                'user' => $db['username'],
                'pass' => $db['password'],
                'charset' => 'utf8mb4',
            ];
        }

        $config = require __DIR__ . '/../../config/config.php';
        $db = $config['db'];
        $db['port'] = $db['port'] ?? 3306; // config.php local n'a pas de port explicite jusqu'ici

        return $db;
    }
}