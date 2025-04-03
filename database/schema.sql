-- 花見調整サイト データベーススキーマ
-- MySQL 8.0+向け

-- 既存のデータベースがある場合は削除（開発時のみ使用）
-- DROP DATABASE IF EXISTS hanami;

-- データベースの作成
CREATE DATABASE IF NOT EXISTS hanami CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- データベースを使用
USE hanami;

-- イベントテーブル
CREATE TABLE IF NOT EXISTS events (
    event_id VARCHAR(36) PRIMARY KEY COMMENT 'ユニークなイベントID (UUID)',
    event_name VARCHAR(255) NOT NULL COMMENT 'イベント名',
    description TEXT COMMENT 'イベントの説明',
    confirmed_date_id VARCHAR(36) NULL COMMENT '確定された日時候補ID',
    confirmed_location_id VARCHAR(36) NULL COMMENT '確定された場所候補ID',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
    FOREIGN KEY (confirmed_date_id) REFERENCES dates(id) ON DELETE SET NULL,
    FOREIGN KEY (confirmed_location_id) REFERENCES locations(id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='イベント情報';

-- 日時候補テーブル
CREATE TABLE IF NOT EXISTS dates (
    id VARCHAR(36) PRIMARY KEY COMMENT '日時候補ID',
    event_id VARCHAR(36) NOT NULL COMMENT 'イベントID',
    datetime DATETIME NOT NULL COMMENT '候補日時',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id)
) ENGINE=InnoDB COMMENT='日時候補';

-- 場所候補テーブル
CREATE TABLE IF NOT EXISTS locations (
    id VARCHAR(36) PRIMARY KEY COMMENT '場所候補ID',
    event_id VARCHAR(36) NOT NULL COMMENT 'イベントID',
    name VARCHAR(255) NOT NULL COMMENT '場所名',
    url VARCHAR(512) COMMENT '地図URLなど',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id)
) ENGINE=InnoDB COMMENT='場所候補';

-- 参加者テーブル
CREATE TABLE IF NOT EXISTS members (
    id VARCHAR(36) PRIMARY KEY COMMENT '参加者ID',
    event_id VARCHAR(36) NOT NULL COMMENT 'イベントID',
    name VARCHAR(100) NOT NULL COMMENT '参加者名',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id)
) ENGINE=InnoDB COMMENT='参加者';

-- 持ち物リストテーブル
CREATE TABLE IF NOT EXISTS items (
    id VARCHAR(36) PRIMARY KEY COMMENT '持ち物ID',
    event_id VARCHAR(36) NOT NULL COMMENT 'イベントID',
    name VARCHAR(255) NOT NULL COMMENT '持ち物名',
    category VARCHAR(100) COMMENT 'カテゴリ',
    assignee_id VARCHAR(36) NULL COMMENT '担当者ID',
    status ENUM('pending', 'ready') DEFAULT 'pending' COMMENT '状態（準備中/準備完了）',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (assignee_id) REFERENCES members(id) ON DELETE SET NULL,
    INDEX idx_event_id (event_id)
) ENGINE=InnoDB COMMENT='持ち物リスト';

-- チャットメッセージテーブル
CREATE TABLE IF NOT EXISTS chat_messages (
    id VARCHAR(36) PRIMARY KEY COMMENT 'メッセージID',
    event_id VARCHAR(36) NOT NULL COMMENT 'イベントID',
    sender_id VARCHAR(36) NOT NULL COMMENT '送信者ID',
    message TEXT NOT NULL COMMENT 'メッセージ内容',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '送信日時',
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES members(id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id),
    INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB COMMENT='チャットメッセージ';

-- 投票テーブル
CREATE TABLE IF NOT EXISTS votes (
    vote_id VARCHAR(36) PRIMARY KEY COMMENT '投票ID',
    event_id VARCHAR(36) NOT NULL COMMENT 'イベントID',
    member_id VARCHAR(36) NOT NULL COMMENT '投票者ID',
    target_type ENUM('date', 'location') NOT NULL COMMENT '投票対象タイプ',
    target_id VARCHAR(36) NOT NULL COMMENT '投票対象ID',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '投票日時',
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id),
    INDEX idx_target (target_type, target_id),
    UNIQUE KEY unique_vote (event_id, member_id, target_type, target_id) COMMENT '同一人物による重複投票防止'
) ENGINE=InnoDB COMMENT='投票記録';

-- 外部キー制約の順序問題を解決するための修正
ALTER TABLE events
    ADD CONSTRAINT fk_confirmed_date FOREIGN KEY (confirmed_date_id) REFERENCES dates(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_confirmed_location FOREIGN KEY (confirmed_location_id) REFERENCES locations(id) ON DELETE SET NULL;
