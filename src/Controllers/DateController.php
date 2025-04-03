<?php
namespace Hanami\Controllers;

use Hanami\Models\Date;
use Hanami\Models\Event;
use Hanami\Models\Member;
use Hanami\Models\Vote;
use Hanami\Utils\Realtime;

/**
 * 日時候補管理コントローラー
 */
class DateController extends Controller
{
    /**
     * 日時候補の追加処理
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
        $datetime = $this->post('datetime');
        $memberName = $this->post('member_name');
        
        // バリデーション
        if (empty($eventId) || empty($datetime) || empty($memberName)) {
            $this->json(['error' => '必須項目が入力されていません'], 400);
            return;
        }
        
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // 日時のフォーマット確認
        $datetimeObj = \DateTime::createFromFormat('Y-m-d H:i', $datetime);
        if (!$datetimeObj) {
            $this->json(['error' => '日時の形式が正しくありません'], 400);
            return;
        }
        
        // 過去の日時はNG
        $now = new \DateTime();
        if ($datetimeObj < $now) {
            $this->json(['error' => '過去の日時は選択できません'], 400);
            return;
        }
        
        // 参加者の取得または作成
        $memberModel = new Member();
        $memberId = $memberModel->addMember($eventId, $memberName);
        
        if (!$memberId) {
            $this->json(['error' => '参加者情報の登録に失敗しました'], 500);
            return;
        }
        
        // 日時候補の追加
        $dateModel = new Date();
        $dateId = $dateModel->addCandidate($eventId, $datetimeObj->format('Y-m-d H:i:s'));
        
        if (!$dateId) {
            $this->json(['error' => '日時候補の追加に失敗しました'], 500);
            return;
        }
        
        // リアルタイム通知
        $dates = $dateModel->getCandidatesWithVotes($eventId);
        Realtime::updateDates($eventId, ['dates' => $dates]);
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'date_id' => $dateId,
            'message' => '日時候補を追加しました',
            'date' => [
                'id' => $dateId,
                'datetime' => $datetimeObj->format('Y-m-d H:i:s'),
                'formatted' => Date::formatDateTime(['datetime' => $datetimeObj->format('Y-m-d H:i:s')]),
                'vote_count' => 0
            ]
        ]);
    }
    
    /**
     * 日時候補への投票処理
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
        $dateId = $this->post('date_id');
        $memberName = $this->post('member_name');
        
        // バリデーション
        if (empty($eventId) || empty($dateId) || empty($memberName)) {
            $this->json(['error' => '必須項目が入力されていません'], 400);
            return;
        }
        
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // 日時候補の存在確認
        $dateModel = new Date();
        $date = $dateModel->find($dateId);
        if (!$date || $date['event_id'] !== $eventId) {
            $this->json(['error' => '指定された日時候補が存在しません'], 404);
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
        $voteId = $voteModel->addVote($eventId, $memberId, 'date', $dateId);
        
        // 既に投票している場合は削除（トグル機能）
        if (!$voteId) {
            $voteRemoved = $voteModel->removeVote($eventId, $memberId, 'date', $dateId);
            
            if (!$voteRemoved) {
                $this->json(['error' => '投票の取り消しに失敗しました'], 500);
                return;
            }
        }
        
        // 投票結果を取得
        $dates = $dateModel->getCandidatesWithVotes($eventId);
        
        // リアルタイム通知
        Realtime::updateDates($eventId, ['dates' => $dates]);
        
        // 成功レスポンス
        $this->json([
            'success' => true,
            'message' => $voteId ? '投票しました' : '投票を取り消しました',
            'vote_added' => $voteId ? true : false,
            'dates' => $dates
        ]);
    }
    
    /**
     * 日時候補一覧の取得（API用）
     * 
     * @param string $eventId イベントID
     * @return void
     */
    public function listDates(string $eventId): void
    {
        // イベントの存在確認
        $eventModel = new Event();
        if (!$eventModel->exists($eventId)) {
            $this->json(['error' => '指定されたイベントが存在しません'], 404);
            return;
        }
        
        // 日時候補を取得
        $dateModel = new Date();
        $dates = $dateModel->getCandidatesWithVotes($eventId);
        
        // レスポンス
        $this->json([
            'success' => true,
            'dates' => $dates
        ]);
    }
}
