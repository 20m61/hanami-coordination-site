# データベース設計ドキュメント

## 概要

花見調整サイトのデータベースは、イベント情報、日時候補、場所候補、参加者リスト、持ち物リスト、チャットメッセージ、投票情報など、全ての主要機能のデータを管理します。多数のイベントを同時に管理でき、各イベントが独立したデータを持つように設計されています。

## テーブル構造

### 1. eventsテーブル

イベントの基本情報を管理します。

| カラム名 | データ型 | 説明 |
|---------|--------|------|
| event_id | VARCHAR(36) | 主キー、UUID形式 |
| event_name | VARCHAR(255) | イベント名 |
| description | TEXT | イベントの説明文 |
| confirmed_date_id | VARCHAR(36) | 確定された日時候補ID（外部キー） |
| confirmed_location_id | VARCHAR(36) | 確定された場所候補ID（外部キー） |
| created_at | TIMESTAMP | 作成日時 |
| updated_at | TIMESTAMP | 更新日時 |

### 2. datesテーブル

イベントの日時候補を管理します。

| カラム名 | データ型 | 説明 |
|---------|--------|------|
| id | VARCHAR(36) | 主キー、UUID形式 |
| event_id | VARCHAR(36) | イベントID（外部キー） |
| datetime | DATETIME | 候補日時 |
| created_at | TIMESTAMP | 作成日時 |

### 3. locationsテーブル

イベントの場所候補を管理します。

| カラム名 | データ型 | 説明 |
|---------|--------|------|
| id | VARCHAR(36) | 主キー、UUID形式 |
| event_id | VARCHAR(36) | イベントID（外部キー） |
| name | VARCHAR(255) | 場所名 |
| url | VARCHAR(512) | 地図URLなど |
| created_at | TIMESTAMP | 作成日時 |

### 4. membersテーブル

イベントの参加者を管理します。

| カラム名 | データ型 | 説明 |
|---------|--------|------|
| id | VARCHAR(36) | 主キー、UUID形式 |
| event_id | VARCHAR(36) | イベントID（外部キー） |
| name | VARCHAR(100) | 参加者名 |
| created_at | TIMESTAMP | 作成日時 |

### 5. itemsテーブル

イベントの持ち物リストを管理します。

| カラム名 | データ型 | 説明 |
|---------|--------|------|
| id | VARCHAR(36) | 主キー、UUID形式 |
| event_id | VARCHAR(36) | イベントID（外部キー） |
| name | VARCHAR(255) | 持ち物名 |
| category | VARCHAR(100) | カテゴリ（食べ物、飲み物など） |
| assignee_id | VARCHAR(36) | 担当者ID（外部キー） |
| status | ENUM('pending', 'ready') | 状態（準備中/準備完了） |
| created_at | TIMESTAMP | 作成日時 |

### 6. chat_messagesテーブル

イベントのチャットメッセージを管理します。

| カラム名 | データ型 | 説明 |
|---------|--------|------|
| id | VARCHAR(36) | 主キー、UUID形式 |
| event_id | VARCHAR(36) | イベントID（外部キー） |
| sender_id | VARCHAR(36) | 送信者ID（外部キー） |
| message | TEXT | メッセージ内容 |
| timestamp | TIMESTAMP | 送信日時 |

### 7. votesテーブル

日時候補と場所候補への投票を管理します。

| カラム名 | データ型 | 説明 |
|---------|--------|------|
| vote_id | VARCHAR(36) | 主キー、UUID形式 |
| event_id | VARCHAR(36) | イベントID（外部キー） |
| member_id | VARCHAR(36) | 投票者ID（外部キー） |
| target_type | ENUM('date', 'location') | 投票対象タイプ |
| target_id | VARCHAR(36) | 投票対象ID |
| timestamp | TIMESTAMP | 投票日時 |

## ER図

![データベースER図](https://example.com/images/er-diagram.png)

※実装時にER図を作成してアップロードする予定です。

## インデックス設計

パフォーマンスを考慮して、以下のインデックスを設定しています：

1. 全テーブルにevent_idのインデックス（イベント単位のデータ検索を高速化）
2. chat_messagesテーブルにtimestampのインデックス（時系列順の表示を高速化）
3. votesテーブルにtarget_typeとtarget_idの複合インデックス（投票集計を高速化）
4. votesテーブルに一意の制約（同一人物による重複投票防止）

## 使用方法

1. データベースの作成と初期スキーマの構築:
```shell
mysql -u your_username -p < database/schema.sql
```

2. サンプルデータのインポート（開発環境用）:
```shell
mysql -u your_username -p < database/sample_data.sql
```

## 注意事項

1. 外部キー制約が設定されているため、関連データの削除に注意してください。
2. UUIDには任意の生成ライブラリを使用できますが、一貫性のために同じライブラリを使い続けることを推奨します。
3. 本番環境では、`ON DELETE CASCADE`制約の影響を十分に理解した上で使用してください。