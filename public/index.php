<?php
/**
 * public/index.php
 * SCR-01 ログイン画面 エントリポイント
 */

require_once __DIR__ . '/../src/Auth.php';

// 1. 既にログインしている場合は、権限に応じてリダイレクト
Auth::redirectIfLoggedIn();

// 2. ログインしていない場合はログインViewを表示
require_once __DIR__ . '/../views/login.php';
