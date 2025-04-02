<?php
/**
 * アプリケーションのルート定義
 * 
 * このファイルではアプリケーションのルートパスとコントローラーアクションのマッピングを定義します。
 */

// ホームページ（イベント作成ページ）
$router->get('/', 'HomeController@index');

// イベント作成処理
$router->post('/event/create', 'EventController@create');

// イベント詳細ページ
$router->get('/event/{event_id}', 'EventController@show');

// 日時候補関連
$router->post('/event/{event_id}/date/add', 'DateController@add');
$router->post('/event/{event_id}/date/vote', 'DateController@vote');

// 場所候補関連
$router->post('/event/{event_id}/location/add', 'LocationController@add');
$router->post('/event/{event_id}/location/vote', 'LocationController@vote');

// 持ち物リスト関連
$router->post('/event/{event_id}/item/add', 'ItemController@add');
$router->post('/event/{event_id}/item/assign', 'ItemController@assign');

// 参加者関連
$router->post('/event/{event_id}/member/join', 'MemberController@join');

// チャット関連
$router->post('/event/{event_id}/chat/post', 'ChatController@post');
$router->get('/event/{event_id}/chat/messages', 'ChatController@messages');

// API用のエンドポイント（リアルタイム更新用）
$router->get('/api/event/{event_id}/dates', 'API\\DateController@index');
$router->get('/api/event/{event_id}/locations', 'API\\LocationController@index');
$router->get('/api/event/{event_id}/items', 'API\\ItemController@index');
$router->get('/api/event/{event_id}/members', 'API\\MemberController@index');
$router->get('/api/event/{event_id}/chat', 'API\\ChatController@index');
