<?php
namespace Hanami\Models;

use Ramsey\Uuid\Uuid;
use PDO;

/**
 * チャットメッセージモデル
 */
class ChatMessage extends Model
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected string $table = 'chat_messages';
    
    /**
     * チャットメッセージを追加
     * 
     * @param string $eventId イベントID
     * @param string $senderId 送信者ID
     * @param string $message メッセージ内容
     * @return string|null 追加されたメッセージID
     */
    public function addMessage(string $eventId, string $senderId, string $message): ?string
    {
        $id = Uuid::uuid4()->toString();
        
        $data = [
            'id' => $id,
            'event_id' => $eventId,
            'sender_id' => $senderId,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->create($data);
        return $result ? $id : null;
    }
    
    /**
     * 特定イベントのチャットメッセージを取得
     * 
     * @param string $eventId イベントID
     * @param int $limit 取得する件数
     * @param int $offset オフセット
     * @return array メッセージの配列
     */
    public function getMessagesByEvent(string $eventId, int $limit = 50, int $offset = 0): array
    {
        $sql = "
            SELECT cm.*, m.name as sender_name 
            FROM {$this->table} cm 
            JOIN members m ON cm.sender_id = m.id 
            WHERE cm.event_id = :event_id 
            ORDER BY cm.timestamp DESC 
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->query($sql, [
            ':event_id' => $eventId,
            ':limit' => $limit,
            ':offset' => $offset
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 最新のメッセージを取得
     * 
     * @param string $eventId イベントID
     * @param string|null $afterTimestamp この時間以降のメッセージのみ取得
     * @return array メッセージの配列
     */
    public function getNewMessages(string $eventId, ?string $afterTimestamp = null): array
    {
        if ($afterTimestamp) {
            $sql = "
                SELECT cm.*, m.name as sender_name 
                FROM {$this->table} cm 
                JOIN members m ON cm.sender_id = m.id 
                WHERE cm.event_id = :event_id 
                  AND cm.timestamp > :timestamp
                ORDER BY cm.timestamp ASC
            ";
            
            $params = [
                ':event_id' => $eventId,
                ':timestamp' => $afterTimestamp
            ];
        } else {
            $sql = "
                SELECT cm.*, m.name as sender_name 
                FROM {$this->table} cm 
                JOIN members m ON cm.sender_id = m.id 
                WHERE cm.event_id = :event_id 
                ORDER BY cm.timestamp DESC 
                LIMIT 20
            ";
            
            $params = [':event_id' => $eventId];
        }
        
        $stmt = $this->query($sql, $params);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 時系列順に並び替え
        if (!$afterTimestamp) {
            $messages = array_reverse($messages);
        }
        
        return $messages;
    }
    
    /**
     * 特定ユーザーのメッセージを取得
     * 
     * @param string $eventId イベントID
     * @param string $senderId 送信者ID
     * @return array メッセージの配列
     */
    public function getUserMessages(string $eventId, string $senderId): array
    {
        $sql = "
            SELECT * 
            FROM {$this->table} 
            WHERE event_id = :event_id 
              AND sender_id = :sender_id 
            ORDER BY timestamp DESC
        ";
        
        $stmt = $this->query($sql, [
            ':event_id' => $eventId,
            ':sender_id' => $senderId
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * メッセージデータをフォーマット
     * 
     * @param array $message メッセージデータ
     * @return array フォーマット済みメッセージ
     */
    public static function formatMessage(array $message): array
    {
        $timestamp = new \DateTime($message['timestamp']);
        
        return [
            'id' => $message['id'],
            'sender_id' => $message['sender_id'],
            'sender_name' => $message['sender_name'] ?? '不明',
            'message' => $message['message'],
            'time' => $timestamp->format('H:i'),
            'date' => $timestamp->format('Y/m/d'),
            'timestamp' => $message['timestamp']
        ];
    }
}
