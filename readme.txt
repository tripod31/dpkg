＜dpkg＞

■動作確認環境
・ubuntu11.10server
・apahce2
・php5
・smarty3
・sqlite3

■追加インストールパッケージ
apache2
php5
smarty3
php5-sqlite
apt-show-versions

■設定ファイル
/etc/apache2/mods-enabled/php5.conf
    phpのハンドラを有効にする
    <FilesMatch "\.ph(p3?|tml)$">
    ↓
    <FilesMatch "\.ph(p3?|tml|p)$">

■設定
・apacheからファイル・ディレクトリの書き込み権を設定
$cd dpkg
$sudo chgrp www-data .
$sudo chgrp www-data templates_c
$sudo chgrp www-data dpkg.db
$chmod g+w dpkg.db

■使い方
・「現在の状態を更新」ボタン
    現在のインストール状況を更新します
・「現在の状態をオリジナルの状態にする」ボタン
    ./installed_org.txtからオリジナルのインストール状況を更新します。

■トラブルシューティング
・smarty3のエラーが出る場合
templates_cのファイルを消すと動いた場合がありました。
