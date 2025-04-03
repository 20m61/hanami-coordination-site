<?php
namespace Hanami\Models;

use Ramsey\Uuid\Uuid;
use PDO;

/**
 * 場所候補モデル
 */
class Location extends Model
{
    /**
     * テーブル名
     * 
     * @var string
     */
    protected string $table = 'locations';
    
    /**
     * 場所候補を追加
     * 
     * @param string $eventId イベントID
     * @param string $name 場所名
     * @param string|null $url 地図URLなど
     * @return string|null 追加された場所候補ID
     */
    public function addCandidate(string $eventId, string $name, ?string $url = null): ?string
    {
        $id = Uuid::uuid4()->toString();
        
        $data = [
            'id' => $id,
            'event_id' => $eventId,
            'name' => $name,
            'url' => $url
        ];
        
        $result = $this->create($data);
        return $result ? $id : null;
    }
    
    /**
     * 特定イベントの場所候補を全て取得
     * 
     * @param string $eventId イベントID
     * @return array 場所候補の配列
     */
    public function getCandidatesByEvent(string $eventId): array
    {
        return $this->all(['event_id' => $eventId], ['name' => 'ASC']);
    }
    
    /**
     * 場所候補の投票数を含めて取得
     * 
     * @param string $eventId イベントID
     * @return array 投票数を含む場所候補の配列
     */
    public function getCandidatesWithVotes(string $eventId): array
    {
        $sql = "
            SELECT l.*, 
                   COUNT(v.vote_id) as vote_count 
            FROM {$this->table} l 
            LEFT JOIN votes v ON l.id = v.target_id AND v.target_type = 'location' 
            WHERE l.event_id = :event_id 
            GROUP BY l.id 
            ORDER BY l.name ASC
        ";
        
        $stmt = $this->query($sql, [':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 特定の場所候補に投票した参加者を取得
     * 
     * @param string $locationId 場所候補ID
     * @return array 投票した参加者の配列
     */
    public function getVotersByLocation(string $locationId): array
    {
        $sql = "
            SELECT m.* 
            FROM members m 
            JOIN votes v ON m.id = v.member_id 
            WHERE v.target_id = :location_id AND v.target_type = 'location'
        ";
        
        $stmt = $this->query($sql, [':location_id' => $locationId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * 最も多く投票された場所候補を取得
     * 
     * @param string $eventId イベントID
     * @return array|null 最も多く投票された場所候補
     */
    public function getMostVotedCandidate(string $eventId): ?array
    {
        $sql = "
            SELECT l.*, 
                   COUNT(v.vote_id) as vote_count 
            FROM {$this->table} l 
            LEFT JOIN votes v ON l.id = v.target_id AND v.target_type = 'location' 
            WHERE l.event_id = :event_id 
            GROUP BY l.id 
            ORDER BY vote_count DESC, l.name ASC 
            LIMIT 1
        ";
        
        $stmt = $this->query($sql, [':event_id' => $eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result !== false ? $result : null;
    }
    
    /**
     * 地図URLが有効かチェック
     * 
     * @param string|null $url 地図URL
     * @return bool
     */
    public static function isValidMapUrl(?string $url): bool
    {
        if (empty($url)) {
            return true; // 空のURLは許可
        }
        
        // 一般的な地図サービスのURLかチェック
        $validDomains = [
            'google.com/maps',
            'maps.google',
            'goo.gl/maps',
            'maps.apple.com',
            'openstreetmap.org',
            'bing.com/maps',
            'waze.com'
        ];
        
        foreach ($validDomains as $domain) {
            if (strpos($url, $domain) !== false) {
                return true;
            }
        }
        
        // URLの形式をチェック
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
