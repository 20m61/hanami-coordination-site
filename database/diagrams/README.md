# データベース図表

このディレクトリには、花見調整サイトのデータベース設計に関する図表が含まれています。

## ER図

ER図（Entity-Relationship図）は、データベースの構造を視覚的に表現したものです。
各テーブル（エンティティ）と、テーブル間の関係（リレーションシップ）を示しています。

### PlantUML版ER図

PlantUMLを使用してER図を作成する場合は、以下のコードを使用できます：

```plantuml
@startuml
' データベースER図 - 花見調整サイト

' エンティティの定義
entity "events" as events {
  * event_id : VARCHAR(36) <<PK>>
  --
  * event_name : VARCHAR(255)
  description : TEXT
  confirmed_date_id : VARCHAR(36) <<FK>>
  confirmed_location_id : VARCHAR(36) <<FK>>
  * created_at : TIMESTAMP
  * updated_at : TIMESTAMP
}

entity "dates" as dates {
  * id : VARCHAR(36) <<PK>>
  --
  * event_id : VARCHAR(36) <<FK>>
  * datetime : DATETIME
  * created_at : TIMESTAMP
}

entity "locations" as locations {
  * id : VARCHAR(36) <<PK>>
  --
  * event_id : VARCHAR(36) <<FK>>
  * name : VARCHAR(255)
  url : VARCHAR(512)
  * created_at : TIMESTAMP
}

entity "members" as members {
  * id : VARCHAR(36) <<PK>>
  --
  * event_id : VARCHAR(36) <<FK>>
  * name : VARCHAR(100)
  * created_at : TIMESTAMP
}

entity "items" as items {
  * id : VARCHAR(36) <<PK>>
  --
  * event_id : VARCHAR(36) <<FK>>
  * name : VARCHAR(255)
  category : VARCHAR(100)
  assignee_id : VARCHAR(36) <<FK>>
  * status : ENUM
  * created_at : TIMESTAMP
}

entity "chat_messages" as chat_messages {
  * id : VARCHAR(36) <<PK>>
  --
  * event_id : VARCHAR(36) <<FK>>
  * sender_id : VARCHAR(36) <<FK>>
  * message : TEXT
  * timestamp : TIMESTAMP
}

entity "votes" as votes {
  * vote_id : VARCHAR(36) <<PK>>
  --
  * event_id : VARCHAR(36) <<FK>>
  * member_id : VARCHAR(36) <<FK>>
  * target_type : ENUM
  * target_id : VARCHAR(36)
  * timestamp : TIMESTAMP
}

' リレーションシップの定義
events ||--o{ dates : "has"
events ||--o{ locations : "has"
events ||--o{ members : "has"
events ||--o{ items : "has"
events ||--o{ chat_messages : "has"
events ||--o{ votes : "has"
events ||--o| dates : "confirms"
events ||--o| locations : "confirms"

members ||--o{ votes : "casts"
members ||--o{ chat_messages : "sends"
members ||--o{ items : "is assigned to"

@enduml
```

## その他の図

データベース設計に関するその他の図表（例：テーブル間の関係図、データフロー図など）も追加される予定です。