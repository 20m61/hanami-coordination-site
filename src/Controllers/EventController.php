<?php
namespace Hanami\Controllers;

use Hanami\Models\Event;
use Hanami\Models\Date;
use Hanami\Models\Location;
use Hanami\Models\Member;
use Hanami\Models\Item;
use Hanami\Models\Vote;

/**
 * イベント管理コントローラー
 */
class EventController extends Controller
{
    /**
     * イベント作成処理
     * 
     * @return void
     */
    public function create(): void
    {
        // CSRF対策
        if (!$this->validateCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token validation failed'], 403);
            return;
        }
        
        // バリデーション
        $eventName = $this->post('event_name');
        $description = $this->post('description');
        
        if (empty($eventName)) {
            $this->json(['error' => 'イベント名は必須です'], 400);
            return;
        }
        
        if (mb_strlen($eventName) > 255) {
            $this->json(['error' => 'イベント名は255文字以内にしてください'], 400);
            return;
        }
        
        // イベント作成
        $eventModel = new Event();
        $eventId = $eventModel->createEvent($eventName, $description);
        
        if (!$eventId) {
            $this->json(['error' => 'イベントの作成に失敗しました'], 500);
            return;
        }
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'event_id' => $eventId,
            'redirect' => "/event/{$eventId}"
        ]);
    }
    
    /**
     * イベント詳細表示
     * 
     * @param string $eventId イベントID
     * @return void
     */
    public function show(string $eventId): void
    {
        // イベント情報の取得
        $eventModel = new Event();
        $event = $eventModel->getEventDetails($eventId);
        
        // 存在チェック
        if (!$event) {
            $this->notFound();
            return;
        }
        
        // 日時候補の取得
        $dateModel = new Date();
        $dates = $dateModel->getCandidatesWithVotes($eventId);
        
        // 場所候補の取得
        $locationModel = new Location();
        $locations = $locationModel->getCandidatesWithVotes($eventId);
        
        // 参加者の取得
        $memberModel = new Member();
        $members = $memberModel->getMembersByEvent($eventId);
        
        // 持ち物リストの取得
        $itemModel = new Item();
        $items = $itemModel->getItemsByEvent($eventId);
        
        // 各タブのコンテンツを読み込む
        ob_start();
        include __DIR__ . '/../Views/event/date_tab.php';
        $dateTabContent = ob_get_clean();
        
        ob_start();
        include __DIR__ . '/../Views/event/location_tab.php';
        $locationTabContent = ob_get_clean();
        
        ob_start();
        include __DIR__ . '/../Views/event/item_tab.php';
        $itemTabContent = ob_get_clean();
        
        // ビューを表示
        $this->view('event/show', [
            'title' => $event['event_name'] . ' - 花見調整サイト',
            'event' => $event,
            'dates' => $dates,
            'locations' => $locations,
            'members' => $members,
            'items' => $items,
            'dateTabContent' => $dateTabContent,
            'locationTabContent' => $locationTabContent,
            'itemTabContent' => $itemTabContent,
            'csrfToken' => $this->generateCsrfToken()
        ]);
    }
    
    /**
     * 存在しないイベントへのアクセス
     * 
     * @return void
     */
    private function notFound(): void
    {
        $this->view('error/404', [
            'title' => 'イベントが見つかりません - 花見調整サイト',
            'message' => '指定されたイベントは存在しないか、削除されました。'
        ]);
    }
}