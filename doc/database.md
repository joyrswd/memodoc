# データベース構成
## ER図
```mermaid
erDiagram

user {}
memo {}
tag {}
document {}
api_job {}
memo_tag {}
document_memo {}

user ||--o{ memo : ""
user ||--o{ document : ""
user ||--o{ api_job : ""
memo ||--o{ memo_tag : ""
memo ||--o{ document_memo : ""
memo ||--o{ api_job_memo : ""
tag ||--o{ memo_tag : ""
document ||--|{ document_memo : ""
api_job ||--o| document : ""
api_job ||--|{ api_job_memo : ""
```

## テーブル定義書

### テーブル名: users

| 列名       | データ型     | 制約           | 説明         |
|------------|--------------|----------------|--------------|
| id         | INT          | PK             | ユーザーID   |
| name       | VARCHAR(255) | UK, NOT NULL | ユーザー名（半角英数字_-のみ）   |
| email      | VARCHAR(255) | UK, NOT NULL       | メールアドレス   |
| password   | VARCHAR(255) | NOT NULL  | パスワード   |
| email_verified_at | timestamp | |メール認証日時 |
| created_at | timestamp    | DEFAULT current_timestamp   | 作成日時     |
| updated_at | timestamp    | DEFAULT NULL   | 更新日時     |
| deleted_at | timestamp    | DEFAULT NULL   | 削除日時     |

### テーブル名: memos

| 列名       | データ型     | 制約           | 説明         |
|------------|--------------|----------------|--------------|
| id         | INT          | PK             | メモID       |
| content    | TEXT         | NOT NULL       | 内容         |
| user_id    | INT          | FK             | ユーザーID   |
| created_at | timestamp    | DEFAULT current_timestamp   | 作成日時     |
| updated_at | timestamp    | DEFAULT NULL   | 更新日時     |
| deleted_at | timestamp    | DEFAULT NULL   | 削除日時     |

### テーブル名: tags

| 列名       | データ型     | 制約           | 説明         |
|------------|--------------|----------------|--------------|
| id         | INT          | PK             | タグID       |
| name       | VARCHAR(20)  | NOT NULL       | タグ名       |
| created_at | DATETIME     | DEFAULT NULL   | 作成日時     |

### テーブル名: documents

| 列名       | データ型     | 制約           | 説明         |
|------------|--------------|----------------|--------------|
| id         | INT          | PK             | 記事ID       |
| title      | VARCHAR(255) |        | タイトル     |
| content    | TEXT         | NOT NULL       | 内容         |
| user_id    | INT          | FK, NOT NULL   | ユーザーID   |
| api_job_id | INT          | FK, NOT NULL   | APIジョブID   |
| created_at | timestamp    | DEFAULT current_timestamp   | 作成日時     |
| updated_at | timestamp    | DEFAULT NULL   | 更新日時     |
| deleted_at | timestamp    | DEFAULT NULL   | 削除日時     |

### テーブル名: api_jobs
| 列名       | データ型     | 制約           | 説明         |
|------------|--------------|----------------|--------------|
| id         | INT          | PK             | API実行ID       |
| api_name   | VARCHAR(20)  | NOT NULL       | APIの種別       |
| status     | VARCHAR(10)  | DEFAULT 'started' | API実行状況       |
| response    | TEXT         |                   | 内容（JSON）|
| started_at | timestamp    | DEFAULT NULL   | 処理開始日時|
| finished_at | timestamp    | DEFAULT NULL   | 処理終了日時|
| user_id    | INT          | FK, NOT NULL   | ユーザーID   |
| created_at | timestamp    | DEFAULT current_timestamp   | 作成日時     |
| updated_at | timestamp    | DEFAULT NULL   | 更新日時     |

### テーブル名: memo_tag

| 列名       | データ型     | 制約           | 説明         |
|------------|--------------|----------------|--------------|
| memo_id    | INT          | FK             | メモID       |
| tag_id     | INT          | FK             | タグID       |

### テーブル名: document_memo

| 列名       | データ型     | 制約           | 説明         |
|------------|--------------|----------------|--------------|
| article_id | INT          | FK             | 記事ID       |
| memo_id    | INT          | FK             | メモID       |

### テーブル名: api_job_memo

| 列名       | データ型     | 制約           | 説明         |
|------------|--------------|----------------|--------------|
| api_job_id     | INT          | FK             | API実行ID       |
| memo_id        | INT          | FK             | メモID       |
| order          | TINYINT      | NOT NULL      |　パーツ内の順番 |
