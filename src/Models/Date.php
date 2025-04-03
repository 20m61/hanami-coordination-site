<?php
namespace Hanami\Models;

use Ramsey\Uuid\Uuid;
use PDO;

/**
 * 日時候補モデル
 */
class Date extends Model
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected string $table = 'dates';
    
    /**
     * 日時候補を追加
     * 
     * @param string $eventId イベントID
     * @param string $datetime 日時（Y-m-d H:i:s形式）
     * @return string|null 追加された日時候補ID
     */
    public function addCandidate(string $eventId, string $datetime): ?string
    {
        $id = Uuid::uuid4()->toString();
        
        $data = [
            'id' => $id,
            'event_id' => $eventId,
            'datetime' => $datetime
        ];
        
        $result = $this->create($data);
        return $result ? $id : null;
    }
    
    /**
     * 特定イベントの日時候補を全て取得
     * 
     * @param string $eventId イベントID
     * @return array 日時候補の配列
     */
    public function getCandidatesByEvent(string $eventId): array
    {
        return $this->all(['event_id' => $eventId], ['datetime' => 'ASC']);
    }
    
    /**
     * 日時候補の投票数を含めて取得
     * 
     * @param string $eventId イベントID
     * @return array 投票数を含む日時候補の配列
     */
    public function getCandidatesWithVotes(string $eventId): array
    {
        $sql = "
            SELECT d.*, 
                   COUNT(v.vote_id) as vote_count 
            FROM {$this->table} d 
            LEFT JOIN votes v ON d.id = v.target_id AND v.target_type = 'date' 
            WHERE d.event_id = :event_id 
            GROUP BY d.id 
            ORDER BY d.datetime ASC
        ";
        
        $stmt = $this->query($sql, [':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 特定の日時候補に投票した参加者を取得
     * 
     * @param string $dateId 日時候補ID
     * @return array 投票した参加者の配列
     */
    public function getVotersByDate(string $dateId): array
    {
        $sql = "
            SELECT m.* 
            FROM members m 
            JOIN votes v ON m.id = v.member_id 
            WHERE v.target_id = :date_id AND v.target_type = 'date'
        ";
        
        $stmt = $this->query($sql, [':date_id' => $dateId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 最も多く投票された日時候補を取得
     * 
     * @param string $eventId イベントID
     * @return array|null 最も多く投票された日時候補
     */
    public function getMostVotedCandidate(string $eventId): ?array
    {
        $sql = "
            SELECT d.*, 
                   COUNT(v.vote_id) as vote_count 
            FROM {$this->table} d 
            LEFT JOIN votes v ON d.id = v.target_id AND v.target_type = 'date' 
            WHERE d.event_id = :event_id 
            GROUP BY d.id 
            ORDER BY vote_count DESC, d.datetime ASC 
            LIMIT 1
        ";
        
        $stmt = $this->query($sql, [':event_id' => $eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result !== false ? $result : null;
    }
    
    /**
     * フォーマットされた日時文字列を取得
     * 
     * @param array $date 日時候補データ
     * @param string $format 日付フォーマット
     * @return string フォーマットされた日時
     */
    public static function formatDateTime(array $date, string $format = 'Y年m月d日 H:i'): string
    {
        $datetime = new \DateTime($date['datetime']);
        return $datetime->format($format);
    }
}
