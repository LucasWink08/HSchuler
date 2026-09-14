<?php
require dirname(__DIR__) . '/config/app.php';
require dirname(__DIR__) . '/config/database.php';
require dirname(__DIR__) . '/app/Core/Database.php';
require dirname(__DIR__) . '/app/Repositories/UserRepository.php';
require dirname(__DIR__) . '/app/Services/AuthService.php';
require dirname(__DIR__) . '/app/Services/QuestaoService.php';
require dirname(__DIR__) . '/app/Services/TrilhaService.php';
require dirname(__DIR__) . '/app/Controllers/AuthController.php';
require dirname(__DIR__) . '/app/Controllers/AlunoController.php';
require dirname(__DIR__) . '/app/Controllers/ProfessorController.php';

session_start();

$route = $_GET['route'] ?? '/';
$route = '/' . ltrim($route, '/');

$routes = require dirname(__DIR__) . '/routes/web.php';

if (!isset($routes[$route])) {
    http_response_code(404);
    echo 'Página não encontrada.';
    exit;
}

$handler = $routes[$route];

if (is_callable($handler)) {
    $handler();
    exit;
}

if (is_string($handler) && file_exists($handler)) {
    include $handler;
    exit;
}

http_response_code(500);
echo 'Rota inválida.';
