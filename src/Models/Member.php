<?php
namespace Hanami\Models;

use Ramsey\Uuid\Uuid;
use PDO;

/**
 * 参加者モデル
 */
class Member extends Model
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected string $table = 'members';
    
    /**
     * 参加者を追加
     * 
     * @param string $eventId イベントID
     * @param string $name 参加者名
     * @return string|null 追加された参加者ID
     */
    public function addMember(string $eventId, string $name): ?string
    {
        // 同じイベント内での同一名の参加者が存在するかチェック
        $existingMember = $this->findOne([
            'event_id' => $eventId,
            'name' => $name
        ]);
        
        if ($existingMember) {
            return $existingMember['id']; // 既存の参加者IDを返す
        }
        
        // 新規参加者の作成
        $id = Uuid::uuid4()->toString();
        
        $data = [
            'id' => $id,
            'event_id' => $eventId,
            'name' => $name
        ];
        
        $result = $this->create($data);
        return $result ? $id : null;
    }
    
    /**
     * 特定イベントの参加者を全て取得
     * 
     * @param string $eventId イベントID
     * @return array 参加者の配列
     */
    public function getMembersByEvent(string $eventId): array
    {
        return $this->all(['event_id' => $eventId], ['name' => 'ASC']);
    }
    
    /**
     * 参加者IDから参加者を取得
     * 
     * @param string $memberId 参加者ID
     * @return array|null 参加者情報
     */
    public function getMemberById(string $memberId): ?array
    {
        return $this->find($memberId);
    }
    
    /**
     * 参加者名から参加者を取得（特定イベント内）
     * 
     * @param string $eventId イベントID
     * @param string $name 参加者名
     * @return array|null 参加者情報
     */
    public function getMemberByName(string $eventId, string $name): ?array
    {
        return $this->findOne([
            'event_id' => $eventId,
            'name' => $name
        ]);
    }
    
    /**
     * 参加者の持ち物リストを取得
     * 
     * @param string $memberId 参加者ID
     * @return array 持ち物の配列
     */
    public function getAssignedItems(string $memberId): array
    {
        $sql = "
            SELECT i.* 
            FROM items i 
            WHERE i.assignee_id = :member_id
        ";
        
        $stmt = $this->query($sql, [':member_id' => $memberId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 参加者の投票情報を取得
     * 
     * @param string $memberId 参加者ID
     * @param string $eventId イベントID
     * @return array 投票情報の配列
     */
    public function getMemberVotes(string $memberId, string $eventId): array
    {
        $voteModel = new Vote();
        $dateVotes = $voteModel->getVotesByMember($eventId, $memberId, 'date');
        $locationVotes = $voteModel->getVotesByMember($eventId, $memberId, 'location');
        
        return [
            'date_votes' => $dateVotes,
            'location_votes' => $locationVotes
        ];
    }
    
    /**
     * 参加者名の検証
     * 
     * @param string $name 参加者名
     * @return bool
     */
    public static function isValidName(string $name): bool
    {
        $name = trim($name);
        
        // 空白チェック
        if (empty($name)) {
            return false;
        }
        
        // 長さチェック（1〜100文字）
        if (mb_strlen($name) > 100) {
            return false;
        }
        
        return true;
    }
}
