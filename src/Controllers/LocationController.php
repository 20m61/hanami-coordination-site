<?php
namespace Hanami\Controllers;

use Hanami\Models\Location;
use Hanami\Models\Event;
use Hanami\Models\Member;
use Hanami\Models\Vote;
use Hanami\Utils\Realtime;

/**
 * 場所候補管理コントローラー
 */
class LocationController extends Controller
{
    /**
     * 場所候補の追加処理
     * 
     * @return void
     */
    public function add(): void
    {
        // CSRF対策
        if (!$this->validateCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token validation failed'], 403);
            return;
        }
        
        // パラメータ取得
        $eventId = $this->post('event_id');
        $name = $this->post('name');
        $url = $this->post('url');
        $memberName = $this->post('member_name');
        
        // バリデーション
        if (empty($eventId) || empty($name) || empty($memberName)) {
            $this->json(['error' => '必須項目が入力されていません'], 400);
            return;
        }
        
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // URLのバリデーション（入力されている場合）
        if (!empty($url) && !Location::isValidMapUrl($url)) {
            $this->json(['error' => '地図URLの形式が正しくありません'], 400);
            return;
        }
        
        // 参加者の取得または作成
        $memberModel = new Member();
        $memberId = $memberModel->addMember($eventId, $memberName);
        
        if (!$memberId) {
            $this->json(['error' => '参加者情報の登録に失敗しました'], 500);
            return;
        }
        
        // 場所候補の追加
        $locationModel = new Location();
        $locationId = $locationModel->addCandidate($eventId, $name, $url);
        
        if (!$locationId) {
            $this->json(['error' => '場所候補の追加に失敗しました'], 500);
            return;
        }
        
        // リアルタイム通知
        $locations = $locationModel->getCandidatesWithVotes($eventId);
        Realtime::updateLocations($eventId, ['locations' => $locations]);
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'location_id' => $locationId,
            'message' => '場所候補を追加しました',
            'location' => [
                'id' => $locationId,
                'name' => $name,
                'url' => $url,
                'vote_count' => 0
            ]
        ]);
    }
    
    /**
     * 場所候補への投票処理
     * 
     * @return void
     */
    public function vote(): void
    {
        // CSRF対策
        if (!$this->validateCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token validation failed'], 403);
            return;
        }
        
        // パラメータ取得
        $eventId = $this->post('event_id');
        $locationId = $this->post('location_id');
        $memberName = $this->post('member_name');
        
        // バリデーション
        if (empty($eventId) || empty($locationId) || empty($memberName)) {
            $this->json(['error' => '必須項目が入力されていません'], 400);
            return;
        }
        
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
        
        // 参加者の取得または作成
        $memberModel = new Member();
        $memberId = $memberModel->addMember($eventId, $memberName);
        
        if (!$memberId) {
            $this->json(['error' => '参加者情報の登録に失敗しました'], 500);
            return;
        }
        
        // 投票処理
        $voteModel = new Vote();
        $voteId = $voteModel->addVote($eventId, $memberId, 'location', $locationId);
        
        // 既に投票している場合は削除（トグル機能）
        if (!$voteId) {
            $voteRemoved = $voteModel->removeVote($eventId, $memberId, 'location', $locationId);
            
            if (!$voteRemoved) {
                $this->json(['error' => '投票の取り消しに失敗しました'], 500);
                return;
            }
        }
        
        // 投票結果を取得
        $locations = $locationModel->getCandidatesWithVotes($eventId);
        
        // リアルタイム通知
        Realtime::updateLocations($eventId, ['locations' => $locations]);
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'message' => $voteId ? '投票しました' : '投票を取り消しました',
            'vote_added' => $voteId ? true : false,
            'locations' => $locations
        ]);
    }
    
    /**
     * 場所候補一覧の取得（API用）
     * 
     * @param string $eventId イベントID
     * @return void
     */
    public function listLocations(string $eventId): void
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
}
