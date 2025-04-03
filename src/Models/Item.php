<?php
namespace Hanami\Models;

use Ramsey\Uuid\Uuid;
use PDO;

/**
 * 持ち物モデル
 */
class Item extends Model
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected string $table = 'items';
    
    /**
     * アイテムを追加
     * 
     * @param string $eventId イベントID
     * @param string $name アイテム名
     * @param string|null $category カテゴリー
     * @param string|null $assigneeId 担当者ID
     * @return string|null 追加されたアイテムID
     */
    public function addItem(string $eventId, string $name, ?string $category = null, ?string $assigneeId = null): ?string
    {
        $id = Uuid::uuid4()->toString();
        
        $data = [
            'id' => $id,
            'event_id' => $eventId,
            'name' => $name,
            'category' => $category,
            'assignee_id' => $assigneeId,
            'status' => 'pending'
        ];
        
        $result = $this->create($data);
        return $result ? $id : null;
    }
    
    /**
     * 特定イベントの持ち物を全て取得
     * 
     * @param string $eventId イベントID
     * @return array アイテムの配列
     */
    public function getItemsByEvent(string $eventId): array
    {
        $sql = "
            SELECT i.*, m.name as assignee_name 
            FROM {$this->table} i 
            LEFT JOIN members m ON i.assignee_id = m.id 
            WHERE i.event_id = :event_id 
            ORDER BY i.category, i.name
        ";
        
        $stmt = $this->query($sql, [':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * カテゴリー別に持ち物を取得
     * 
     * @param string $eventId イベントID
     * @return array カテゴリー別のアイテム配列
     */
    public function getItemsByCategory(string $eventId): array
    {
        $items = $this->getItemsByEvent($eventId);
        
        $result = [];
        foreach ($items as $item) {
            $category = empty($item['category']) ? 'その他' : $item['category'];
            if (!isset($result[$category])) {
                $result[$category] = [];
            }
            $result[$category][] = $item;
        }
        
        return $result;
    }
    
    /**
     * アイテムに担当者を割り当て
     * 
     * @param string $itemId アイテムID
     * @param string $memberId 担当者ID
     * @return bool
     */
    public function assignItem(string $itemId, string $memberId): bool
    {
        return $this->update($itemId, ['assignee_id' => $memberId]);
    }
    
    /**
     * アイテムの担当者を解除
     * 
     * @param string $itemId アイテムID
     * @return bool
     */
    public function unassignItem(string $itemId): bool
    {
        return $this->update($itemId, ['assignee_id' => null]);
    }
    
    /**
     * アイテムの状態を変更
     * 
     * @param string $itemId アイテムID
     * @param string $status 状態（'pending'または'ready'）
     * @return bool
     */
    public function updateStatus(string $itemId, string $status): bool
    {
        if (!in_array($status, ['pending', 'ready'])) {
            return false;
        }
        
        return $this->update($itemId, ['status' => $status]);
    }
    
    /**
     * イベント内の状態統計を取得
     * 
     * @param string $eventId イベントID
     * @return array 状態統計情報
     */
    public function getEventItemStatistics(string $eventId): array
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'ready' THEN 1 ELSE 0 END) as ready,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN assignee_id IS NOT NULL THEN 1 ELSE 0 END) as assigned,
                SUM(CASE WHEN assignee_id IS NULL THEN 1 ELSE 0 END) as unassigned
            FROM {$this->table}
            WHERE event_id = :event_id
        ";
        
        $stmt = $this->query($sql, [':event_id' => $eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result;
    }
}
