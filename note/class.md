## クラス相関図
```Mermaid
graph TB
    Route
    subgraph Middleware
        Auth
    end
    subgraph Gate
        memo_id  --> Auth
        article_id  --> Auth
    end
    subgraph Model
        User
        Memo
        Tag
        Article
    end
    subgraph Repository
        UserRepository --> User
        MemoRepository --> Memo
        TagRepository --> Tag
        ArticleRepository --> Article
    end
    subgraph Service
        UserService --> UserRepository
        MemoService --> MemoRepository
        TagService --> TagRepository
        ArticleService --> ArticleRepository
        CartService
    end
    subgraph FormRequest
        UserRequest --> UserService
        MemoRequest --> MemoService
        ArticleRequest --> ArticleService
        CartRequest
    end
    subgraph Controller
        LoginController
        UserController --> UserRequest
        MemoController --> MemoRequest
        ArticleController --> ArticleRequest
        CartController --> CartRequest
    end
    DB[(データベース)]
    Model <--> DB
    Route --> Middleware --> Controller
    LoginController --> UserService 
    UserController --> UserService
    MemoController --> MemoService
    MemoController --> TagService
    ArticleController --> ArticleService
    CartController --> CartService
    CartService --> MemoRepository
    CartRequest --> MemoRepository
```

## 機能リスト
### Controller
#### LoginController
| 機能名       | リクエスト     | URI           | 機能概要         |
|------------|--------------|----------------|--------------|
|ログイン入力|GET|/|ユーザー名入力枠表示<br> パスワード入力枠表示|
|新規ユーザー登録|POST|/login/|アカウント情報認証<br> 認可|
|ログアウト|GET|/logout/|ログイン中ユーザーのログアウト|

#### UserController
| 機能名       | リクエスト     | URI           | 機能概要         |
|------------|--------------|----------------|--------------|
|ユーザー一覧|GET|/user/|ユーザー名表示<br> 削除ボタン表示<br>  編集リンク設置<br>  ユーザー名検索<br>  削除済み表示/非表示切り替え|
|新規ユーザー入力|GET|/user/create|ユーザー名入力枠表示<br> パスワード入力枠表示<br>    確認パスワード入力枠表示|
|新規ユーザー登録|POST|/user/|ユーザー名保存<br>    パスワード保存<br>  ユーザーID採番|
|ユーザー削除|DELETE|/user/{user}|指定ユーザーIDの論理削除|
|ユーザー編集|GET|/user/{user}/edit|ユーザー名表示<br>  パスワード入力枠表示<br>    確認パスワード入力枠表示|
|ユーザー更新|PUT|/user/{user}|パスワード更新|

#### MemoController
| 機能名       | リクエスト     | URI           | 機能概要         |
|------------|--------------|----------------|--------------|
|新規メモ入力|GET|/memo/create|メモ内容入力枠表示<br>タグ登録枠表示
|新規メモ登録|POST|/memo/|メモ内容保存<br>タグ登録<br>タグIDとメモIDの紐づけ|
|メモ削除|DELETE|/memo/{memo}|指定メモIDの論理削除|
|メモ一覧|GET|/memo/|内容前方30文字表示<br> タグ編集欄表示<br> 作成日時表示<br> 削除ボタン表示<br> 内容検索<br> 作成日時絞り込み<br> タグ絞り込み<br> 削除済み表示/非表示切り替え|
|タグ変更|POST|/memo/tag/{memo}|タグIDとメモIDの紐づけ変更

#### CartController
| 機能名       | リクエスト     | URI           | 機能概要         |
|------------|--------------|----------------|--------------|
|素材メモ一覧|GET|/cart/|内容前方30文字表示<br> 作成日時表示<br> メモ順番調整機能<br> メモ順番調整機能<br> 記事生成ボタン表示<br> カートから削除ボタン表示<br> カートを空にするボタン表示|
|素材メモ登録|GET|/cart/{memo}/add|素材となるメモをセッションに保存<br> カート内限界数判定|
|素材メモ削除|GET|/cart/{memo}/remove|素材となるメモをセッションから削除|
|素材メモ全削除|GET|/cart/empty|素材となるメモをセッションから全削除|

#### ArticleController
| 機能名       | リクエスト     | URI           | 機能概要         |
|------------|--------------|----------------|--------------|
|新規記事生成|POST|/article/|素材メモから記事を生成<br>素材メモの限界数判定|
|記事表示|GET|/article/{article}/show|記事タイトル表示<br>  記事内容表示<br>  作成日時表示|
|記事削除|DELETE|/article/{article}|指定記事IDの論理削除|
|記事一覧|GET|/article/|タイトル表示<br> 作成日時表示<br> 削除ボタン表示<br> タイトル検索<br>  内容検索<br> 作成日時絞り込み<br> 削除済み表示/非表示切り替え|
|記事編集|GET|/article/{article}/edit|記事タイトル入力枠表示<br>  記事内容入力枠表示|
|記事更新|PUT|/article/{article}|記事タイトル更新<br>  記事内容更新|

### FormRequest
#### UserRequest
- users.*.name
    - 文字種チェック（半角英数字_-のみ）
- users.add.name
    - 文字数チェック（255）
    - 未入力チェック
    - 重複チェック
- users.find.name
    - 文字数チェック（100）
- users.*.password
    - 文字数チェック（255）
    - 文字種チェック（半角英数字記号のみ）
    - 確認と同一チェック
- users.add.password
    - 未入力チェック
- users.deleted
    - 入力値チェック(1のみ)

#### MemoRequest
- memo.add.content
    - 文字数チェック（x方式140文字）
- memo.find.name
    - 文字数チェック（100）
- memo.find.created_at
    - 日付チェック
- memo.*.tag
    - 文字数チェック（20）
- memo.deleted
    - 入力値チェック(1のみ)
- タグ分割処理
    - 全角空白を半角に変換
    - 半角空白毎にタグを区切る

#### ArticleRequest
- article.edit.title
    - 文字数チェック（255）
    - 未入力チェック
- article.find.name
    - 文字数チェック（100）
- article.edit.content
    - 未入力チェック
- article.find.content
    - 文字数チェック（100）
- article.find.created_at
    - 日付チェック
- article.deleted
    - 入力値チェック(1のみ)
