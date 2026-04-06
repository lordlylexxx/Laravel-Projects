<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use PDO;
use PDOException;

abstract class TestCase extends BaseTestCase
{
    private static bool $mysqlTestingDatabaseEnsured = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::ensureMysqlTestingDatabaseExists();
    }

    /**
     * When phpunit.xml targets MySQL, create the configured database if missing so
     * RefreshDatabase can migrate without a manual CREATE DATABASE step.
     */
    private static function ensureMysqlTestingDatabaseExists(): void
    {
        if (self::$mysqlTestingDatabaseEnsured) {
            return;
        }

        $driver = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?: '';
        if ($driver !== 'mysql') {
            return;
        }

        $database = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: '';
        if ($database === '' || $database === ':memory:') {
            return;
        }

        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
        $port = (int) ($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306);
        $username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
        $password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';
        $charset = $_ENV['DB_CHARSET'] ?? getenv('DB_CHARSET') ?: 'utf8mb4';
        $collation = $_ENV['DB_COLLATION'] ?? getenv('DB_COLLATION') ?: 'utf8mb4_unicode_ci';

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%d', $host, $port),
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $safeName = str_replace('`', '``', $database);
            $pdo->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s',
                $safeName,
                str_replace([';', '`'], '', $charset),
                str_replace([';', '`'], '', $collation)
            ));
            self::$mysqlTestingDatabaseEnsured = true;
        } catch (PDOException) {
            // Let the first migration / connection surface the real error.
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
