<?php
$env = $_ENV ?? [];
$host = getenv('DB_HOST') ?: ($env['DB_HOST'] ?? 'localhost');
$port = getenv('DB_PORT') ?: ($env['DB_PORT'] ?? '3306');
$dbName = getenv('DB_NAME') ?: ($env['DB_NAME'] ?? 'hschulerf');
$dbUser = getenv('DB_USER') ?: ($env['DB_USER'] ?? 'root');
$dbPass = getenv('DB_PASS') ?: ($env['DB_PASS'] ?? 'lucas123');

define('usuario', $dbUser);
define('senha', $dbPass);
define('bd', $dbName);
define('servidor', $host);
define('porta', $port);
define('dsn', 'mysql:host=' . servidor . ';port=' . porta . ';dbname=' . bd);
define('host', $host);
?>