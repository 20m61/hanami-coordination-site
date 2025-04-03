<?php
namespace Hanami\Core;

use PDO;
use PDOException;

/**
 * データベース接続を管理するクラス
 */
class Database
{
    /**
     * PDOインスタンス
     * 
     * @var PDO|null
     */
    private static ?PDO $instance = null;

    /**
     * データベース設定
     * 
     * @var array
     */
    private static array $config = [];

    /**
     * コンストラクタはプライベート化（シングルトンパターン）
     */
    private function __construct()
    {
    }

    /**
     * PDOインスタンスの取得
     * 
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::connect();
        }

        return self::$instance;
    }

    /**
     * データベース接続の初期化
     * 
     * @return void
     * @throws PDOException
     */
    private static function connect(): void
    {
        // 設定が読み込まれていない場合は読み込む
        if (empty(self::$config)) {
            self::$config = require __DIR__ . '/../../config/database.php';
        }

        $defaultConnection = self::$config['default'];
        $connection = self::$config['connections'][$defaultConnection];

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $connection['driver'],
            $connection['host'],
            $connection['port'],
            $connection['database'],
            $connection['charset']
        );

        try {
            self::$instance = new PDO(
                $dsn,
                $connection['username'],
                $connection['password'],
                $connection['options'] ?? []
            );
        } catch (PDOException $e) {
            // エラーログに記録して再スロー
            error_log('データベース接続エラー: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * トランザクションの開始
     * 
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    /**
     * トランザクションのコミット
     * 
     * @return bool
     */
    public static function commit(): bool
    {
        return self::getInstance()->commit();
    }

    /**
     * トランザクションのロールバック
     * 
     * @return bool
     */
    public static function rollBack(): bool
    {
        return self::getInstance()->rollBack();
    }

    /**
     * SQLクエリの実行
     * 
     * @param string $sql SQL文
     * @param array $params バインドパラメータ
     * @return \PDOStatement|false
     */
    public static function query(string $sql, array $params = [])
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
