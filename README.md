# memo2doc

## 概要

短文メモから生成AIを利用して文書を作成するウェブアプリです。個人の研究学習用に作成しました。

## 特徴

- 140文字以内の短文メモを保存
- 保存した短文メモを組み合わせて自動で文書を生成
- Laravelのプラグインを別途インストールせずに開発
- Bootstrap5(外部読込)を利用したデザイン

## 開発環境
- Docker Desktop 4.12.0
- Ubuntu 22.04.3 LTS（Windows 11 WSL上）
- Laravel Sail (1.18)
    - PHP 8.2.13 (cli)
        - Composer 2.6.6
        - Laravel Framework 10.35.0
    - MySQL 8.0.32
    - Redis 7.2.3

## インストール

1. 上記と同等の開発環境を用意（Redisは任意）
2. ＜ドキュメントルート＞に本リポジトリをクローンする
3. composer installを実行してvendorディレクトリを生成する
4. .env.exampleをコピーし.envにリネームする
5. php artisan key:generateを実行してアプリケーションキーを生成する
6. .envのQUEUE_CONNECTIONをredis(あるいはdatabase)に編集する
7. その他.envを環境に応じた値に編集する
8. /config/api.php.exampleをコピーして/config/api.phpにリネームする
9. 上記のapi.php内のopenai.secretを編集する（ご自身でAPIキーを発行してください）
10. バックグラウンドジョブを定時実行させるため、cronに下記のコマンドを設定する<br>（＜ドキュメントルート＞の部分はご自身の環境で読み替えてください）
    ```bash
    * * * * * cd ＜ドキュメントルート＞ && php artisan schedule:run >> /dev/null 2>&1
    ```
11. ＜ドキュメントルート＞でLaravelのmigrationを実行しデータベースを構築する
    ```bash
    php artisan migrate
    ```
以上

### Tips
- Laravel Sailを使用する場合、laravelコンテナにcronとCLIエディタをインストールする必要がある。
    ```bash
    # cronとCLIエディタのインストール
    apt-get update
    apt-get install cron vim
    # cronの設定コマンド(実行後CLIエディタが起動)
    crontab -e
    # cronの設定確認
    crontab -l
    # cronの起動
    /etc/init.d/cron start
    ```
- storageとbootstrap/cacheのパーミッションエラーが出る場合は所有者を変更する
    ```bash
    # 例）Laravel sailの場合
    chown sail:sail -R ./bootstrap/cache
    chown sail:sail -R ./storage
    ```

## 使い方

1. ブラウザでドキュメントルートに指定したトップページにアクセスしログイン画面を表示
2. 「新規作成」リンクからユーザー登録を行う
3. 認証メールが飛ぶのでメーラーでリンクをクリック
4. ＜メモ作成＞画面に移動するので、任意のメモをいくつか作成する
5. ＜メモ一覧＞画面に移動して、任意のメモの右にある「書類アイコン」をクリックしてパーツを増やす
6. 画面下のPartsメニューから＜パーツ一覧＞を表示し、最下部の「文書生成」ボタンをクリック
7. ＜ジョブ一覧＞画面に移動するので、1~3分待ったのちに画面更新し、ジョブがsuccessになったのを確認
8. 画面下のDocsメニューから＜文書一覧＞を表示し、文書が作成されているのを確認する
以上

## 今後の開発予定
（順不同）
- デモサイト公開
- X(旧Twitter)への投稿機能実装
- パーツ内メモの順番入れ替え機能実装
- タグ入力フォームの改良

## ライセンス

このプロジェクトは[MITライセンス](LICENSE)の下でライセンスされています。

