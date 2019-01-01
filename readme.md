dpkg
=====
dpkgの出力を見るためのPHPアプリケーション

■動作確認環境
-----
+ debian stretch
+ virtualbox5.1.22
+ apahce2
+ php7.0
+ smarty3
+ sqlite3

■必要パッケージ
-----
以下を追加でインストールしました
+ apache2
+ php
+ smarty3
+ sqlite3
+ php7.0-sqlite3
+ apt-show-versions

■設定
-----
    $sh setup.sh

以下を行なっています  
+ DB作成
+ apacheからファイル・ディレクトリの書き込み権を設定

■使い方
-----
#### 「現在の状態を更新」ボタン  
現在のインストール状況を更新します

#### 「現在の状態をオリジナルの状態にする」ボタン  
オリジナルのインストール状況を現在の状態にします

#### 「アップロード」ボタン  
他のマシンで  

    dpkg -l>dpkg.txt

そのファイルから、オリジナルの状態を更新します

#### debファイルがあるディレクトリからオリジナルの状態を更新する  
    $php imp_installed_org.php -d [debファイルがあるディレクトリ]

<img src="https://user-images.githubusercontent.com/6335693/50577285-6f683a00-0e68-11e9-9c31-ca620fe36c8e.jpg" >
