<?php
namespace Hanami\Controllers;

use Hanami\Models\Item;
use Hanami\Models\Event;
use Hanami\Models\Member;
use Hanami\Utils\Realtime;

/**
 * 持ち物管理コントローラー
 */
class ItemController extends Controller
{
    /**
     * 持ち物の追加処理
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
        $category = $this->post('category');
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
        
        // 参加者の取得または作成
        $memberModel = new Member();
        $memberId = $memberModel->addMember($eventId, $memberName);
        
        if (!$memberId) {
            $this->json(['error' => '参加者情報の登録に失敗しました'], 500);
            return;
        }
        
        // 持ち物の追加
        $itemModel = new Item();
        $itemId = $itemModel->addItem($eventId, $name, $category);
        
        if (!$itemId) {
            $this->json(['error' => '持ち物の追加に失敗しました'], 500);
            return;
        }
        
        // リアルタイム通知
        $items = $itemModel->getItemsByEvent($eventId);
        Realtime::updateItems($eventId, ['items' => $items]);
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'item_id' => $itemId,
            'message' => '持ち物を追加しました',
            'item' => [
                'id' => $itemId,
                'name' => $name,
                'category' => $category,
                'status' => 'pending',
                'assignee_id' => null,
                'assignee_name' => null
            ]
        ]);
    }
    
    /**
     * 持ち物の削除処理
     * 
     * @param string $itemId 持ち物ID
     * @return void
     */
    public function delete(string $itemId): void
    {
        // CSRF対策
        if (!$this->validateCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token validation failed'], 403);
            return;
        }
        
        // 持ち物の取得
        $itemModel = new Item();
        $item = $itemModel->find($itemId);
        
        if (!$item) {
            $this->json(['error' => '指定された持ち物が存在しません'], 404);
            return;
        }
        
        $eventId = $item['event_id'];
        
        // 削除処理
        $result = $itemModel->delete($itemId);
        
        if (!$result) {
            $this->json(['error' => '持ち物の削除に失敗しました'], 500);
            return;
        }
        
        // リアルタイム通知
        $items = $itemModel->getItemsByEvent($eventId);
        Realtime::updateItems($eventId, ['items' => $items]);
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'message' => '持ち物を削除しました'
        ]);
    }
    
    /**
     * 持ち物への担当者割り当て処理
     * 
     * @param string $itemId 持ち物ID
     * @return void
     */
    public function assign(string $itemId): void
    {
        // CSRF対策
        if (!$this->validateCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token validation failed'], 403);
            return;
        }
        
        // パラメータ取得
        $memberName = $this->post('member_name');
        
        // 持ち物の取得
        $itemModel = new Item();
        $item = $itemModel->find($itemId);
        
        if (!$item) {
            $this->json(['error' => '指定された持ち物が存在しません'], 404);
            return;
        }
        
        $eventId = $item['event_id'];
        
        // バリデーション
        if (empty($memberName)) {
            // 名前が空の場合は担当解除
            $result = $itemModel->unassignItem($itemId);
        } else {
            // 参加者の取得または作成
            $memberModel = new Member();
            $memberId = $memberModel->addMember($eventId, $memberName);
            
            if (!$memberId) {
                $this->json(['error' => '参加者情報の登録に失敗しました'], 500);
                return;
            }
            
            // 担当者割り当て
            $result = $itemModel->assignItem($itemId, $memberId);
        }
        
        if (!$result) {
            $this->json(['error' => '担当者の変更に失敗しました'], 500);
            return;
        }
        
        // リアルタイム通知
        $items = $itemModel->getItemsByEvent($eventId);
        Realtime::updateItems($eventId, ['items' => $items]);
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'message' => empty($memberName) ? '担当者を解除しました' : '担当者を割り当てました'
        ]);
    }
    
    /**
     * 持ち物のステータス変更処理
     * 
     * @param string $itemId 持ち物ID
     * @return void
     */
    public function updateStatus(string $itemId): void
    {
        // CSRF対策
        if (!$this->validateCsrfToken($this->post('csrf_token'))) {
            $this->json(['error' => 'CSRF token validation failed'], 403);
            return;
        }
        
        // パラメータ取得
        $status = $this->post('status');
        
        // 持ち物の取得
        $itemModel = new Item();
        $item = $itemModel->find($itemId);
        
        if (!$item) {
            $this->json(['error' => '指定された持ち物が存在しません'], 404);
            return;
        }
        
        $eventId = $item['event_id'];
        
        // バリデーション
        if (!in_array($status, ['pending', 'ready'])) {
            $this->json(['error' => '無効なステータスです'], 400);
            return;
        }
        
        // ステータス変更
        $result = $itemModel->updateStatus($itemId, $status);
        
        if (!$result) {
            $this->json(['error' => 'ステータスの変更に失敗しました'], 500);
            return;
        }
        
        // リアルタイム通知
        $items = $itemModel->getItemsByEvent($eventId);
        Realtime::updateItems($eventId, ['items' => $items]);
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'message' => $status === 'ready' ? '準備完了にしました' : '準備中に戻しました'
        ]);
    }
    
    /**
     * イベント内の持ち物一覧取得（API用）
     * 
     * @param string $eventId イベントID
     * @return void
     */
    public function listItems(string $eventId): void
    {
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // 持ち物一覧を取得
        $itemModel = new Item();
        $items = $itemModel->getItemsByEvent($eventId);
        $itemsByCategory = $itemModel->getItemsByCategory($eventId);
        $statistics = $itemModel->getEventItemStatistics($eventId);
        
        // レスポンス
        $this->json([
            'success' => true,
            'items' => $items,
            'items_by_category' => $itemsByCategory,
            'statistics' => $statistics
        ]);
    }
}