<?php
namespace Hanami\Controllers\API;

use Hanami\Controllers\Controller;
use Hanami\Models\Date;
use Hanami\Models\Event;

/**
 * 日時候補API
 */
class DateController extends Controller
{
    /**
     * 日時候補の一覧を取得
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
        
        // 日時候補を取得
        $dateModel = new Date();
        $dates = $dateModel->getCandidatesWithVotes($eventId);
        
        // 日時を整形して返す
        $formattedDates = [];
        foreach ($dates as $date) {
            $formattedDate = $date;
            $formattedDate['formatted'] = Date::formatDateTime($date);
            $formattedDate['formatted_short'] = Date::formatDateTime($date, 'm/d H:i');
            $formattedDates[] = $formattedDate;
        }
        
        // レスポンス
        $this->json([
            'success' => true,
            'dates' => $formattedDates
        ]);
    }
    
    /**
     * 特定の日時候補の投票者一覧を取得
     * 
     * @param string $eventId イベントID
     * @param string $dateId 日時候補ID
     * @return void
     */
    public function voters(string $eventId, string $dateId): void
    {
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
        
        // 投票者を取得
        $voters = $dateModel->getVotersByDate($dateId);
        
        // レスポンス
        $this->json([
            'success' => true,
            'date_id' => $dateId,
            'voters' => $voters
        ]);
    }
    
    /**
     * 最も投票された日時候補を取得
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
        
        // 最も投票された日時候補を取得
        $dateModel = new Date();
        $mostVoted = $dateModel->getMostVotedCandidate($eventId);
        
        if ($mostVoted) {
            $mostVoted['formatted'] = Date::formatDateTime($mostVoted);
        }
        
        // レスポンス
        $this->json([
            'success' => true,
            'most_voted' => $mostVoted
        ]);
    }
}
