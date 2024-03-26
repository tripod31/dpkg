import sqlite3
import subprocess
from subprocess import PIPE
import os
import re
import argparse
import datetime

DB_FILE = 'dpkg.db'
    
class ImportDb:   
    def import_installed(self):
        conn=sqlite3.connect(DB_FILE)
        cur = conn.cursor()
        cur.execute("DELETE from installed")
        self.read_packages(conn,'installed')

        #読み取り時刻を保存
        dt_now = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        sql="UPDATE info SET info='{}' WHERE name='saved_time_cur'".format(dt_now)
        cur.execute(sql)
        conn.commit()

    def import_installed_org(self,fo=None):
        """
        fp: ファイルオブジェクト。Noneの場合、コマンドから情報を読み取る
        """
        conn=sqlite3.connect(DB_FILE)
        cur = conn.cursor()
        cur.execute("DELETE from installed_org")
        
        self.read_packages(conn,'installed_org')

        #読み取り時刻
        dt_now = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        sql="UPDATE info SET info='{}' WHERE name='saved_time_org'".format(dt_now)
        #print(sql)
        cur.execute(sql)
        conn.commit()
    
    def read_packages(self,conn,tbl):
        #子クラスでオーバーライド
        pass
       
class ImportFromDpkg(ImportDb):
    """
    dpkg -lコマンドの出力よりパッケージ情報を読み込む
    """
    def __init__(self,path=None) -> None:
        """
        param:  path:dpkg -lの結果が入ったファイルのパス。指定された場合、このファイルからパッケージ情報を読み込む。指定されない場合、dpkg -lコマンドを実行してパッケージ情報を読み込む
        """
        super().__init__()
        self.path = path

    def read_packages(self,conn,tbl):
        if self.path:
            fo = open(self.path,"r")
        else:
            if not os.path.exists("/usr/bin/dpkg"):
                raise Exception("/usr/bin/dpkgがない")      
            res = subprocess.Popen(["dpkg", "-l"],stdout=PIPE,text=True)
            fo = res.stdout

        lno = 0
        for line in fo:
            lno +=1
            self.read_package(conn,tbl,line.strip(),lno)

    def read_package(self,conn,tbl,line,lno):
        if lno <= 5:
            return #ヘッダスキップ
        
        m = re.match("(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(.+)",line)
        if m:
            status,name,version,arch,desc = m.groups()
            desc = desc.strip().replace("'","") #"'"はSQLでエラーになるので除く
            sql =  f"INSERT INTO {tbl} values('{status}','{name}','{version}','{arch}','{desc}')"
            #print(sql)
            conn.cursor().execute(sql)
        else:
            print("dpkg -l:フォーマットが変<BR>" + line + "<BR>")

class ImportFromDebDir(ImportDb):
    """
    指定ディレクトリを検索し*.debファイルからパッケージ情報を読み込む
    """
    def __init__(self,pkg_dir):
        if not os.path.exists(pkg_dir):
            raise Exception(pkg_dir+"がありません")
        self.pkg_dir = pkg_dir

    def read_packages(self,conn,tbl):
        command = ['find',self.pkg_dir, '-name','*.deb', '-print']
        res = subprocess.Popen(command,stdout=PIPE,text=True)
        lno = 0
        for line in res.stdout:
            lno +=1
            self.read_package(conn,tbl,line.strip())

    def read_package(self,conn,tbl,package):
        """
        package:    debファイルのパス
        """
        res = subprocess.Popen(['dpkg', '-I', package],stdout=PIPE,text=True)

        cols = {}
        for line in res.stdout:
            m = re.match("(Package|Version|Architecture|Description): (.+)", line.strip())
            if m:
                cols[m.group(1)]=m.group(2)
        if len(cols)==4:
            #必要情報が読み取れた場合
            status,name,version,arch,desc = ('ii',cols['Package'],cols['Version'],cols['Architecture'],
                cols['Description'].replace("'",""))    #"'"はSQLでエラーになるので除く
            sql = f"INSERT INTO {tbl} values('{status}','{name}','{version}','{arch}','{desc}')"
            #print(sql)
            conn.cursor().execute(sql)
        else:
            print( "dpkg -I:フォーマットが変<BR>" + package + "<BR>")

class ImportFromInitialFile(ImportDb):
    """
    /var/log/installer/initial-status.gzからパッケージ情報を読み込む
    """
    def __init__(self,initial_file_test=None):
        if initial_file_test is not None:
            if not os.path.exists(initial_file_test):
                raise Exception(initial_file_test+"がありません")
            self.initial_file_test = initial_file_test
        else:
            if not os.path.exists("/var/log/installer/initial-status.gz"):
                raise Exception("/var/log/installer/initial-status.gzがありません")

    def read_packages(self,conn,tbl):
        if self.initial_file_test is not None:
            fo = open(self.initial_file_test,"r",encoding='utf-8')
        else:
            res = subprocess.Popen("gzip -dc /var/log/installer/initial-package.gz",stdout=PIPE,text=True,shell=True)
            fo=res.stdout

        pkg_info = {}
        for line in fo:
            m = re.match("Package: (.+)", line.strip())
            if m:
                pkg_info.clear()
                package = m.group(1)
                continue

            m = re.match("(Version|Architecture|Description): (.+)", line.strip())
            if m:
                pkg_info[m.group(1)]=m.group(2)
            if len(pkg_info)==3:
                #パッケージ情報が全て取得できた場合
                status,version,arch,desc = ('ii',pkg_info['Version'],pkg_info['Architecture'],
                    pkg_info['Description'].replace("'",""))    #"'"はSQLでエラーになるので除く
                sql = f"INSERT INTO {tbl} values('{status}','{package}','{version}','{arch}','{desc}')"
                print(sql)
                conn.cursor().execute(sql)
                pkg_info.clear()

if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--package_dir'     ,help='*.debファイルがあるディレクトリ')
    parser.add_argument('--dpkg_file'       ,help='dpkg -lの出力結果ファイルのパス')
    parser.add_argument('--initial_file_test'    ,help='/var/log/installer/initial-status.gzを解凍したファイルのパス')
    parser.add_argument('--initial_file',    
                        action='store_true',
                        help='/var/log/installer/initial-status.gzからパッケージ情報を読み込む')

    args=parser.parse_args()
    print(args)

    if args.package_dir:
        print(f"オリジナルのパッケージ情報を{args.package_dir}の*.debファイルから読み込みます")
        obj = ImportFromDebDir(args.package_dir)
        obj.import_installed_org()

    elif args.dpkg_file:
        print(f"オリジナルのパッケージ情報を{args.dpkg_file}(dpkg -lの出力結果のファイル)から読み込みます")
        obj = ImportFromDpkg(args.dpkg_file)
        obj.import_installed_org()

    elif args.initial_file_test:
        #Windowsでのテスト用
        print(f"オリジナルのパッケージ情報を{args.initial_file_test}から読み込みます")
        obj = ImportFromInitialFile(args.initial_file_test)
        obj.import_installed_org()

    elif args.initial_file:
        print(f"オリジナルのパッケージ情報を/var/log/installer/initial-status.gzから読み込みます")
        obj = ImportFromInitialFile()
        obj.import_installed_org()

    else:
        print("現在のパッケージ情報をdpkg -lコマンドから読み込みます")
        obj = ImportFromDpkg()
        obj.import_installed()
