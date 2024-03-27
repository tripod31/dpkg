dpkg
=====
dpkgの出力を見るためのアプリケーション。サーバーにFlask、フロントエンドにvueを使用

動作確認環境
-----
+ ubuntu23.10
+ python3.10.6
+ flask2.2.3
+ sqlite3

設定
-----
DBを作成します  

    $sqlite3 dpkg.db < create.sql

使い方
-----
#### ウェブサーバー起動
    $python3 app.py

http://localhost:5000
で見れます

importdb.py
----
DBを更新するプログラムです

```
usage: importdb.py [-h] [--dpkg_file DPKG_FILE] [--initial_file] [--initial_file_test INITIAL_FILE_TEST] [--package_dir PACKAGE_DIR]

options:
  -h, --help            show this help message and exit
  --dpkg_file DPKG_FILE
                        dpkg -lの出力結果ファイルのパス。オリジナルのパッケージ情報を読み込む
  --initial_file        /var/log/installer/initial-status.gzからオリジナルのパッケージ情報を読み込む
  --initial_file_test INITIAL_FILE_TEST
                        /var/log/installer/initial-status.gzを解凍したファイルのパス。オリジナルのパッケージ情報を読み込む
  --package_dir PACKAGE_DIR
                        *.debファイルがあるディレクトリ。オリジナルのパッケージ情報を読み込む
```

#### dpkgコマンドの出力から現在の状態を更新する  
```
$python3 importdb.py
```
#### debファイルがあるディレクトリからオリジナルの状態を更新する  
```
$python3 importdb.py --package_dir PACKAGE_DIR
```
#### dpkgコマンドの出力を保存したファイルからオリジナルの状態を更新する  
```
$dpkg -l > dpkg.txt
$python3 importdb.py --dpkg_file dpkg.txt
```
#### /var/log/installer/initial-status.gzからオリジナルの状態を更新する  
```
$python3 importdb.py --initial_file
```

<img src="https://user-images.githubusercontent.com/6335693/222418411-32b51acd-b91c-4794-ba52-4e0e7b0c8b35.png" >
