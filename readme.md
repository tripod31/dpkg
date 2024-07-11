# dpkg
dpkgの出力を見るためのアプリケーション。サーバーにFlask、フロントエンドにvueを使用

## 動作確認環境
+ ubuntu23.10
+ python3.10.6
+ flask2.2.3
+ sqlite3

## 設定
DBを作成します  

    $sqlite3 dpkg.db < create.sql

## 使い方
#### ウェブサーバー起動
    $python3 app.py

http://localhost:5000
で見れます

## importdb.py
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

#### dpkgコマンドの出力から現在のパッケージ情報を更新する  
```
$python3 importdb.py
```
#### /var/log/installer/initial-status.gzからオリジナルのパッケージ情報を更新する  
```
$python3 importdb.py --initial_file
```
#### dpkgコマンドの出力を保存したファイルからオリジナルのパッケージ情報を更新する  
```
$dpkg -l > dpkg.txt
$python3 importdb.py --dpkg_file dpkg.txt
```
#### debファイルがあるディレクトリからオリジナルのパッケージ情報を更新する  
```
$python3 importdb.py --package_dir PACKAGE_DIR
```

<img src="https://private-user-images.githubusercontent.com/6335693/344838908-16814d4e-bca3-4d1b-9826-f638443bdf81.png?jwt=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJnaXRodWIuY29tIiwiYXVkIjoicmF3LmdpdGh1YnVzZXJjb250ZW50LmNvbSIsImtleSI6ImtleTUiLCJleHAiOjE3MTk4ODg3MDgsIm5iZiI6MTcxOTg4ODQwOCwicGF0aCI6Ii82MzM1NjkzLzM0NDgzODkwOC0xNjgxNGQ0ZS1iY2EzLTRkMWItOTgyNi1mNjM4NDQzYmRmODEucG5nP1gtQW16LUFsZ29yaXRobT1BV1M0LUhNQUMtU0hBMjU2JlgtQW16LUNyZWRlbnRpYWw9QUtJQVZDT0RZTFNBNTNQUUs0WkElMkYyMDI0MDcwMiUyRnVzLWVhc3QtMSUyRnMzJTJGYXdzNF9yZXF1ZXN0JlgtQW16LURhdGU9MjAyNDA3MDJUMDI0NjQ4WiZYLUFtei1FeHBpcmVzPTMwMCZYLUFtei1TaWduYXR1cmU9YzQzNTFhOTVlNDAyNmZiNTAwNDNlNzZmODU3ZWY1YzI3ZmZhNzY1Njc0NDUxNDVkNDVjNTkxMGU0M2UwYmUyZSZYLUFtei1TaWduZWRIZWFkZXJzPWhvc3QmYWN0b3JfaWQ9MCZrZXlfaWQ9MCZyZXBvX2lkPTAifQ.-X2-kbXDc1-vnJjHjLh0p3sjTClk930FiMG6rzMpMRg" >
