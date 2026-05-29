<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// ベースURLの計算 (サブディレクトリ環境対応)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$publicPos = strpos($scriptName, '/public/');
if ($publicPos !== false) {
    $baseUrl = substr($scriptName, 0, $publicPos + 8);
} else {
    $baseUrl = '/';
}
define('BASE_URL', $baseUrl);
