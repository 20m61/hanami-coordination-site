<?php
namespace Hanami\Models;

use Hanami\Core\Database;
use PDO;

/**
 * すべてのモデルの基底クラス
 */
abstract class Model
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected string $table;
    
    /**
     * 主キー
     * 
     * @var string
     */
    protected string $primaryKey = 'id';
    
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // テーブル名が未設定の場合は、クラス名から自動生成（複数形）
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($className) . 's';
        }
    }
    
    /**
     * 全レコードの取得
     * 
     * @param array $conditions 検索条件
     * @param array $orderBy 並び順
     * @param int|null $limit 上限数
     * @param int|null $offset オフセット
     * @return array
     */
    public function all(array $conditions = [], array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        $sql = "SELECT * FROM `{$this->table}`";
        
        // 検索条件の追加
        $params = [];
        if (!empty($conditions)) {
            $sql .= " WHERE ";
            $conditionParts = [];
            
            foreach ($conditions as $key => $value) {
                $conditionParts[] = "`{$key}` = :{$key}";
                $params[":{$key}"] = $value;
            }
            
            $sql .= implode(' AND ', $conditionParts);
        }
        
        // 並び順の追加
        if (!empty($orderBy)) {
            $sql .= " ORDER BY ";
            $orderParts = [];
            
            foreach ($orderBy as $column => $direction) {
                $orderParts[] = "`{$column}` {$direction}";
            }
            
            $sql .= implode(', ', $orderParts);
        }
        
        // 上限とオフセットの追加
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
            
            if ($offset !== null) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        // クエリの実行
        $stmt = Database::query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 主キーによるレコード取得
     * 
     * @param mixed $id 主キーの値
     * @return array|null
     */
    public function find($id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1";
        $stmt = Database::query($sql, [':id' => $id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $record !== false ? $record : null;
    }
    
    /**
     * 条件に合致する最初のレコード取得
     * 
     * @param array $conditions 検索条件
     * @return array|null
     */
    public function findOne(array $conditions): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE ";
        $params = [];
        $conditionParts = [];
        
        foreach ($conditions as $key => $value) {
            $conditionParts[] = "`{$key}` = :{$key}";
            $params[":{$key}"] = $value;
        }
        
        $sql .= implode(' AND ', $conditionParts);
        $sql .= " LIMIT 1";
        
        $stmt = Database::query($sql, $params);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $record !== false ? $record : null;
    }
    
    /**
     * レコードの作成
     * 
     * @param array $data 作成するデータ
     * @return int|string|null 挿入されたIDまたはnull
     */
    public function create(array $data)
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_map(function ($key) {
            return ":{$key}";
        }, array_keys($data)));
        
        $sql = "INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})";
        
        $params = [];
        foreach ($data as $key => $value) {
            $params[":{$key}"] = $value;
        }
        
        $stmt = Database::query($sql, $params);
        
        if ($stmt->rowCount() > 0) {
            return Database::getInstance()->lastInsertId();
        }
        
        return null;
    }
    
    /**
     * レコードの更新
     * 
     * @param mixed $id 主キーの値
     * @param array $data 更新するデータ
     * @return bool
     */
    public function update($id, array $data): bool
    {
        $setParts = [];
        $params = [':id' => $id];
        
        foreach ($data as $key => $value) {
            $setParts[] = "`{$key}` = :{$key}";
            $params[":{$key}"] = $value;
        }
        
        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $setParts) . " WHERE `{$this->primaryKey}` = :id";
        
        $stmt = Database::query($sql, $params);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * レコードの削除
     * 
     * @param mixed $id 主キーの値
     * @return bool
     */
    public function delete($id): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id";
        $stmt = Database::query($sql, [':id' => $id]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * カスタムSQLクエリの実行
     * 
     * @param string $sql SQLクエリ
     * @param array $params バインドパラメータ
     * @return \PDOStatement
     */
    public function query(string $sql, array $params = [])
    {
        return Database::query($sql, $params);
    }
}
