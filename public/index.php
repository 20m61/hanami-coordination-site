<?php
/**
 * Hanami Coordination Site - Front Controller
 * 
 * このファイルはアプリケーションへのすべてのリクエストのエントリーポイントとなります。
 * 環境設定、ルーティング、アプリケーションの起動を行います。
 */

// 現在の時間帯を東京に設定
date_default_timezone_set('Asia/Tokyo');

// オートローダーの読み込み
require_once __DIR__ . '/../vendor/autoload.php';

// 環境変数の読み込み
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// エラー表示設定（開発環境のみ）
if ($_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// アプリケーションの初期化と実行
try {
    // ルーターの初期化
    $router = new Hanami\Core\Router();
    
    // ルートの登録
    require_once __DIR__ . '/../config/routes.php';
    
    // リクエストの処理
    $router->dispatch();
} catch (\Exception $e) {
    // エラーログの記録
    $logger = new Monolog\Logger('hanami');
    $logger->pushHandler(new Monolog\Handler\StreamHandler(
        __DIR__ . '/../logs/error.log',
        Monolog\Logger::ERROR
    ));
    $logger->error($e->getMessage(), ['exception' => $e]);
    
    // エラーページの表示
    if ($_ENV['APP_ENV'] === 'development') {
        echo '<h1>エラーが発生しました</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        include __DIR__ . '/../src/Views/error.php';
    }
}
