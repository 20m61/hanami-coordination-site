<?php
namespace Hanami\Models;

use Ramsey\Uuid\Uuid;
use PDO;

/**
 * イベントモデル
 */
class Event extends Model
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected string $table = 'events';
    
    /**
     * 主キー
     * 
     * @var string
     */
    protected string $primaryKey = 'event_id';
    
    /**
     * 新しいイベントを作成
     * 
     * @param string $eventName イベント名
     * @param string|null $description イベントの説明
     * @return string|null 作成されたイベントID
     */
    public function createEvent(string $eventName, ?string $description = null): ?string
    {
        $eventId = Uuid::uuid4()->toString();
        
        $data = [
            'event_id' => $eventId,
            'event_name' => $eventName,
            'description' => $description
        ];
        
        $result = $this->create($data);
        return $result ? $eventId : null;
    }
    
    /**
     * 最近作成されたイベントのリストを取得
     * 
     * @param int $limit 取得する件数
     * @return array
     */
    public function getRecentEvents(int $limit = 10): array
    {
        $sql = "SELECT * FROM `{$this->table}` ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->query($sql, [':limit' => $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * イベントの詳細情報を取得（関連データを含む）
     * 
     * @param string $eventId イベントID
     * @return array|null
     */
    public function getEventDetails(string $eventId): ?array
    {
        // イベント基本情報を取得
        $event = $this->find($eventId);
        if (!$event) {
            return null;
        }
        
        // 確定された日時候補を取得
        if ($event['confirmed_date_id']) {
            $dateModel = new Date();
            $confirmedDate = $dateModel->find($event['confirmed_date_id']);
            $event['confirmed_date'] = $confirmedDate;
        } else {
            $event['confirmed_date'] = null;
        }
        
        // 確定された場所候補を取得
        if ($event['confirmed_location_id']) {
            $locationModel = new Location();
            $confirmedLocation = $locationModel->find($event['confirmed_location_id']);
            $event['confirmed_location'] = $confirmedLocation;
        } else {
            $event['confirmed_location'] = null;
        }
        
        return $event;
    }
    
    /**
     * 日時候補を確定する
     * 
     * @param string $eventId イベントID
     * @param string $dateId 日時候補ID
     * @return bool
     */
    public function confirmDate(string $eventId, string $dateId): bool
    {
        return $this->update($eventId, ['confirmed_date_id' => $dateId]);
    }
    
    /**
     * 場所候補を確定する
     * 
     * @param string $eventId イベントID
     * @param string $locationId 場所候補ID
     * @return bool
     */
    public function confirmLocation(string $eventId, string $locationId): bool
    {
        return $this->update($eventId, ['confirmed_location_id' => $locationId]);
    }

    /**
     * イベントが存在するかチェック
     * 
     * @param string $eventId イベントID
     * @return bool
     */
    public function exists(string $eventId): bool
    {
        return $this->find($eventId) !== null;
    }
}
