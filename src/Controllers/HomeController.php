<?php
namespace Hanami\Controllers;

use Hanami\Models\Event;

/**
 * ホーム画面のコントローラー
 */
class HomeController extends Controller
{
    /**
     * イベント作成ページの表示
     * 
     * @return void
     */
    public function index(): void
    {
        // 最近作成されたイベントを取得
        $eventModel = new Event();
        $recentEvents = $eventModel->getRecentEvents(5);
        
        // ビューを表示
        $this->view('home/index', [
            'title' => '花見調整サイト - イベント作成',
            'recentEvents' => $recentEvents,
            'csrfToken' => $this->generateCsrfToken()
        ]);
    }
}
