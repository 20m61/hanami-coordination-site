<?php
namespace Hanami\Controllers\API;

use Hanami\Controllers\Controller;
use Hanami\Models\Location;
use Hanami\Models\Event;

/**
 * 場所候補API
 */
class LocationController extends Controller
{
    /**
     * 場所候補の一覧を取得
     * 
     * @param string $eventId イベントID
     * @return void
     */
    public function index(string $eventId): void
    {
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // 場所候補を取得
        $locationModel = new Location();
        $locations = $locationModel->getCandidatesWithVotes($eventId);
        
        // レスポンス
        $this->json([
            'success' => true,
            'locations' => $locations
        ]);
    }
    
    /**
     * 特定の場所候補の投票者一覧を取得
     * 
     * @param string $eventId イベントID
     * @param string $locationId 場所候補ID
     * @return void
     */
    public function voters(string $eventId, string $locationId): void
    {
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // 場所候補の存在確認
        $locationModel = new Location();
        $location = $locationModel->find($locationId);
        if (!$location || $location['event_id'] !== $eventId) {
            $this->json(['error' => '指定された場所候補が存在しません'], 404);
            return;
        }
        
        // 投票者を取得
        $voters = $locationModel->getVotersByLocation($locationId);
        
        // レスポンス
        $this->json([
            'success' => true,
            'location_id' => $locationId,
            'voters' => $voters
        ]);
    }
    
    /**
     * 最も投票された場所候補を取得
     * 
     * @param string $eventId イベントID
     * @return void
     */
    public function mostVoted(string $eventId): void
    {
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // 最も投票された場所候補を取得
        $locationModel = new Location();
        $mostVoted = $locationModel->getMostVotedCandidate($eventId);
        
        // レスポンス
        $this->json([
            'success' => true,
            'most_voted' => $mostVoted
        ]);
    }
    
    /**
     * 地図URLの検証
     * 
     * @return void
     */
    public function validateMapUrl(): void
    {
        $url = $this->post('url');
        
        if (empty($url)) {
            $this->json([
                'success' => true,
                'valid' => true,
                'message' => 'URLは入力されていません'
            ]);
            return;
        }
        
        $isValid = Location::isValidMapUrl($url);
        
        $this->json([
            'success' => true,
            'valid' => $isValid,
            'message' => $isValid 
                ? '有効な地図URLです' 
                : '一般的な地図サービスのURLではないようです。URLが正しいか確認してください。'
        ]);
    }
}
