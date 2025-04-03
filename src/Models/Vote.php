<?php
namespace Hanami\Models;

use Ramsey\Uuid\Uuid;
use PDO;

/**
 * 投票モデル
 */
class Vote extends Model
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected string $table = 'votes';
    
    /**
     * 主キー
     * 
     * @var string
     */
    protected string $primaryKey = 'vote_id';
    
    /**
     * 投票を追加
     * 
     * @param string $eventId イベントID
     * @param string $memberId 参加者ID
     * @param string $targetType 投票対象タイプ（'date'または'location'）
     * @param string $targetId 投票対象ID
     * @return string|null 追加された投票ID
     */
    public function addVote(string $eventId, string $memberId, string $targetType, string $targetId): ?string
    {
        // すでに投票済みかチェック
        if ($this->hasVoted($eventId, $memberId, $targetType, $targetId)) {
            return null;
        }
        
        $voteId = Uuid::uuid4()->toString();
        
        $data = [
            'vote_id' => $voteId,
            'event_id' => $eventId,
            'member_id' => $memberId,
            'target_type' => $targetType,
            'target_id' => $targetId
        ];
        
        $result = $this->create($data);
        return $result ? $voteId : null;
    }
    
    /**
     * 投票を削除
     * 
     * @param string $eventId イベントID
     * @param string $memberId 参加者ID
     * @param string $targetType 投票対象タイプ
     * @param string $targetId 投票対象ID
     * @return bool
     */
    public function removeVote(string $eventId, string $memberId, string $targetType, string $targetId): bool
    {
        $sql = "
            DELETE FROM {$this->table} 
            WHERE event_id = :event_id 
              AND member_id = :member_id 
              AND target_type = :target_type 
              AND target_id = :target_id
        ";
        
        $params = [
            ':event_id' => $eventId,
            ':member_id' => $memberId,
            ':target_type' => $targetType,
            ':target_id' => $targetId
        ];
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * 特定の参加者が特定の候補に投票済みかチェック
     * 
     * @param string $eventId イベントID
     * @param string $memberId 参加者ID
     * @param string $targetType 投票対象タイプ
     * @param string $targetId 投票対象ID
     * @return bool
     */
    public function hasVoted(string $eventId, string $memberId, string $targetType, string $targetId): bool
    {
        $conditions = [
            'event_id' => $eventId,
            'member_id' => $memberId,
            'target_type' => $targetType,
            'target_id' => $targetId
        ];
        
        return $this->findOne($conditions) !== null;
    }
    
    /**
     * 特定の参加者が特定のイベント内で投票した全ての候補を取得
     * 
     * @param string $eventId イベントID
     * @param string $memberId 参加者ID
     * @param string $targetType 投票対象タイプ
     * @return array
     */
    public function getVotesByMember(string $eventId, string $memberId, string $targetType): array
    {
        $conditions = [
            'event_id' => $eventId,
            'member_id' => $memberId,
            'target_type' => $targetType
        ];
        
        return $this->all($conditions);
    }
    
    /**
     * 特定の候補の投票数を取得
     * 
     * @param string $targetType 投票対象タイプ
     * @param string $targetId 投票対象ID
     * @return int
     */
    public function getVoteCount(string $targetType, string $targetId): int
    {
        $sql = "
            SELECT COUNT(*) as count 
            FROM {$this->table} 
            WHERE target_type = :target_type 
              AND target_id = :target_id
        ";
        
        $params = [
            ':target_type' => $targetType,
            ':target_id' => $targetId
        ];
        
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * 投票情報をリアルタイム更新用の形式に整形
     * 
     * @param string $eventId イベントID
     * @param string $targetType 投票対象タイプ
     * @return array
     */
    public function getVotesForRealtime(string $eventId, string $targetType): array
    {
        $sql = "
            SELECT target_id, COUNT(*) as count 
            FROM {$this->table} 
            WHERE event_id = :event_id 
              AND target_type = :target_type 
            GROUP BY target_id
        ";
        
        $params = [
            ':event_id' => $eventId,
            ':target_type' => $targetType
        ];
        
        $stmt = $this->query($sql, $params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $votes = [];
        foreach ($results as $result) {
            $votes[$result['target_id']] = (int) $result['count'];
        }
        
        return $votes;
    }
}
