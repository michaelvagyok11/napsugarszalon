<?php
// db.php
require_once __DIR__ . '/config.php';

/**
 * Visszaad egy PDO objektumot az adatbázis-kapcsolathoz.
 */
function getPDO() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Hibakezelés: ne mutass érzékeny adatokat éles környezetben
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}
