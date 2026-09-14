<?php

define('APP_ROOT', dirname(__DIR__));
$appUrl = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$appUrl = rtrim($appUrl, '/');
define('APP_URL', $appUrl === '' ? '' : $appUrl);
define('ASSET_URL', APP_URL . '/assets');

function app_asset(string $path): string
{
    return APP_URL . '/assets/' . ltrim($path, '/');
}

function app_route(string $route): string
{
    $route = '/' . ltrim($route, '/');
    return APP_URL . '/index.php?route=' . rawurlencode($route);
}
