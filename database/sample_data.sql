-- 花見調整サイト サンプルデータ
-- 開発・テスト用

USE hanami;

-- サンプルイベント
INSERT INTO events (event_id, event_name, description, confirmed_date_id, confirmed_location_id, created_at) VALUES
('e001', '会社花見2025', 'みんなで楽しく花見をしましょう！持ち寄り歓迎です。途中参加・退出もOK！', NULL, NULL, '2025-02-01 10:00:00'),
('e002', '大学同窓会＆花見', '大学の友人と久しぶりに集まって花見をしましょう！', NULL, NULL, '2025-02-15 15:30:00');

-- サンプル日時候補
INSERT INTO dates (id, event_id, datetime, created_at) VALUES
('d001', 'e001', '2025-03-28 18:00:00', '2025-02-01 10:05:00'),
('d002', 'e001', '2025-03-29 12:00:00', '2025-02-01 10:06:00'),
('d003', 'e001', '2025-04-05 13:00:00', '2025-02-01 10:07:00'),
('d004', 'e002', '2025-04-02 18:30:00', '2025-02-15 15:35:00'),
('d005', 'e002', '2025-04-09 18:30:00', '2025-02-15 15:36:00');

-- サンプル場所候補
INSERT INTO locations (id, event_id, name, url, created_at) VALUES
('l001', 'e001', '代々木公園（原宿口付近）', 'https://goo.gl/maps/example1', '2025-02-01 10:10:00'),
('l002', 'e001', '上野公園（噴水前広場）', 'https://goo.gl/maps/example2', '2025-02-01 10:11:00'),
('l003', 'e002', '新宿御苑', 'https://goo.gl/maps/example3', '2025-02-15 15:40:00'),
('l004', 'e002', '目黒川沿い', 'https://goo.gl/maps/example4', '2025-02-15 15:41:00');

-- サンプル参加者
INSERT INTO members (id, event_id, name, created_at) VALUES
('m001', 'e001', '山田太郎', '2025-02-01 10:15:00'),
('m002', 'e001', '佐藤花子', '2025-02-01 11:20:00'),
('m003', 'e001', '鈴木一郎', '2025-02-02 09:30:00'),
('m004', 'e001', '高橋幸子', '2025-02-03 12:15:00'),
('m005', 'e002', '田中博', '2025-02-15 16:00:00'),
('m006', 'e002', '伊藤和夫', '2025-02-16 10:45:00'),
('m007', 'e002', '渡辺剛', '2025-02-16 11:30:00');

-- サンプル持ち物
INSERT INTO items (id, event_id, name, category, assignee_id, status, created_at) VALUES
('i001', 'e001', 'レジャーシート（大）', '備品', 'm001', 'ready', '2025-02-05 14:00:00'),
('i002', 'e001', 'ポータブルスピーカー', '備品', 'm002', 'ready', '2025-02-05 14:05:00'),
('i003', 'e001', 'ビール', '飲み物', 'm003', 'pending', '2025-02-05 14:10:00'),
('i004', 'e001', 'お茶', '飲み物', NULL, 'pending', '2025-02-05 14:15:00'),
('i005', 'e001', 'お弁当', '食べ物', 'm004', 'pending', '2025-02-05 14:20:00'),
('i006', 'e002', 'レジャーシート', '備品', 'm005', 'ready', '2025-02-20 09:00:00'),
('i007', 'e002', 'ワイン', '飲み物', 'm006', 'pending', '2025-02-20 09:05:00'),
('i008', 'e002', 'チーズプレート', '食べ物', 'm007', 'pending', '2025-02-20 09:10:00');

-- サンプルチャットメッセージ
INSERT INTO chat_messages (id, event_id, sender_id, message, timestamp) VALUES
('c001', 'e001', 'm001', '場所取りは私がやります！', '2025-02-10 18:00:00'),
('c002', 'e001', 'm002', 'ありがとう！何時頃行く予定？', '2025-02-10 18:05:00'),
('c003', 'e001', 'm001', '正午くらいには行こうと思ってます', '2025-02-10 18:10:00'),
('c004', 'e001', 'm003', '私も少し早めに行くよ！手伝うね', '2025-02-10 19:00:00'),
('c005', 'e002', 'm005', '久しぶりに会えるの楽しみです！', '2025-02-20 20:00:00'),
('c006', 'e002', 'm006', '何年ぶりかな？5年くらい？', '2025-02-20 20:10:00'),
('c007', 'e002', 'm007', '僕は卒業以来だから7年ぶりかな', '2025-02-20 20:15:00');

-- サンプル投票データ
INSERT INTO votes (vote_id, event_id, member_id, target_type, target_id, timestamp) VALUES
-- イベント1の日時候補への投票
('v001', 'e001', 'm001', 'date', 'd001', '2025-02-04 10:00:00'),
('v002', 'e001', 'm001', 'date', 'd002', '2025-02-04 10:01:00'),
('v003', 'e001', 'm002', 'date', 'd002', '2025-02-04 11:00:00'),
('v004', 'e001', 'm003', 'date', 'd002', '2025-02-04 12:00:00'),
('v005', 'e001', 'm003', 'date', 'd003', '2025-02-04 12:01:00'),
('v006', 'e001', 'm004', 'date', 'd002', '2025-02-04 15:00:00'),
-- イベント1の場所候補への投票
('v007', 'e001', 'm001', 'location', 'l001', '2025-02-05 10:00:00'),
('v008', 'e001', 'm002', 'location', 'l001', '2025-02-05 11:00:00'),
('v009', 'e001', 'm003', 'location', 'l002', '2025-02-05 12:00:00'),
('v010', 'e001', 'm004', 'location', 'l001', '2025-02-05 15:00:00'),
-- イベント2の日時候補への投票
('v011', 'e002', 'm005', 'date', 'd004', '2025-02-17 09:00:00'),
('v012', 'e002', 'm006', 'date', 'd004', '2025-02-17 10:00:00'),
('v013', 'e002', 'm007', 'date', 'd005', '2025-02-17 11:00:00'),
-- イベント2の場所候補への投票
('v014', 'e002', 'm005', 'location', 'l003', '2025-02-18 09:00:00'),
('v015', 'e002', 'm006', 'location', 'l004', '2025-02-18 10:00:00'),
('v016', 'e002', 'm007', 'location', 'l003', '2025-02-18 11:00:00');

-- イベント1の日時候補2と場所候補1を確定済みとして設定
UPDATE events SET confirmed_date_id = 'd002', confirmed_location_id = 'l001' WHERE event_id = 'e001';
