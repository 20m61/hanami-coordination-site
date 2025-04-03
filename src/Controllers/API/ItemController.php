<?php
namespace Hanami\Controllers\API;

use Hanami\Controllers\Controller;
use Hanami\Models\Item;
use Hanami\Models\Event;
use Hanami\Models\Member;

/**
 * 持ち物管理API
 */
class ItemController extends Controller
{
    /**
     * イベント内の持ち物一覧取得
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
    
    /**
     * 特定の持ち物の詳細を取得
     * 
     * @param string $itemId 持ち物ID
     * @return void
     */
    public function get(string $itemId): void
    {
        // 持ち物の取得
        $itemModel = new Item();
        $item = $itemModel->find($itemId);
        
        if (!$item) {
            $this->json(['error' => '指定された持ち物が存在しません'], 404);
            return;
        }
        
        // 担当者情報を追加
        if (!empty($item['assignee_id'])) {
            $memberModel = new Member();
            $assignee = $memberModel->find($item['assignee_id']);
            $item['assignee_name'] = $assignee ? $assignee['name'] : null;
        }
        
        // レスポンス
        $this->json([
            'success' => true,
            'item' => $item
        ]);
    }
    
    /**
     * 持ち物カテゴリー一覧取得
     * 
     * @return void
     */
    public function categories(): void
    {
        // カテゴリー一覧は静的に定義
        $categories = [
            '食べ物',
            '飲み物',
            'レジャー用品',
            'キッチン用品',
            '緊急・安全',
            'その他'
        ];
        
        // レスポンス
        $this->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
    
    /**
     * イベント内の持ち物統計情報取得
     * 
     * @param string $eventId イベントID
     * @return void
     */
    public function statistics(string $eventId): void
    {
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // 統計情報を取得
        $itemModel = new Item();
        $statistics = $itemModel->getEventItemStatistics($eventId);
        
        // 進捗率を計算
        $total = intval($statistics['total'] ?? 0);
        $ready = intval($statistics['ready'] ?? 0);
        $percentage = $total > 0 ? round(($ready / $total) * 100) : 0;
        
        $statistics['progress_percentage'] = $percentage;
        
        // レスポンス
        $this->json([
            'success' => true,
            'statistics' => $statistics
        ]);
    }
}