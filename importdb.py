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
        fo = self.open_list_command()
        self.read_packages(conn,fo,'installed')

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
        if fo==None:
            fo=self.open_list_command()
        
        self.read_packages(conn,fo,'installed_org')

        #読み取り時刻
        dt_now = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        sql="UPDATE info SET info='{}' WHERE name='saved_time_org'".format(dt_now)
        #print(sql)
        cur.execute(sql)
        conn.commit()
    
    def read_packages(self,conn,fo,tbl):
        lno = 0
        for line in fo:
            lno +=1
            self.read_package(conn,tbl,line.strip(),lno)
       
class ImportDpkg(ImportDb):
    """
    dpkg -lコマンドの出力よりパッケージ情報を読み込む
    """
    def open_list_command(self):
        if not os.path.exists("/usr/bin/dpkg"):
            raise Exception("/usr/bin/dpkgがない")      
        res = subprocess.Popen(["dpkg", "-l"],stdout=PIPE,text=True)
        return res.stdout
        
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

class ImportDebDir(ImportDb):
    """
    指定ディレクトリを検索し*.debファイルからパッケージ情報を読み込む
    """
    def __init__(self,pkg_dir):
        if not os.path.exists(pkg_dir):
            raise Exception(pkg_dir+"がありません")
        self.pkg_dir = pkg_dir

    def open_list_command(self):
        command = ['find',self.pkg_dir, '-name','*.deb', '-print']
        res = subprocess.Popen(command,stdout=PIPE,text=True)
        return res.stdout
    
    def read_package(self,conn,tbl,package,lno):
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
            status,name,version,arch,desc = ('ii',cols['Package'],cols['Version'],cols['Architecture'],
                cols['Description'].replace("'",""))    #"'"はSQLでエラーになるので除く
            sql = f"INSERT INTO {tbl} values('{status}','{name}','{version}','{arch}','{desc}')"
            #print(sql)
            conn.cursor().execute(sql)
        else:
            print( "dpkg -I:フォーマットが変<BR>" + package + "<BR>")

if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--package_dir' ,help='*.debファイルがあるディレクトリ。')
    parser.add_argument('--dpkg_file'   ,help='dpkgの出力ファイル(dpkg -l>dpkg.txt)')
    args=parser.parse_args()

    if args.package_dir:
        print(f"オリジナルのパッケージ情報を{args.package_dir}の*.debファイルから読み込みます")
        obj = ImportDebDir(args.package_dir)
        obj.import_installed_org()
    elif args.dpkg_file:
        print(f"オリジナルのパッケージ情報を{args.dpkg_file}の*.debファイルから読み込みます")
        with open(args.dpkg_file) as fo:
            obj = ImportDpkg()
            obj.import_installed_org(fo)
    else:
        print("現在のパッケージ情報をdpkg -lコマンドから読み込みます")
        obj = ImportDpkg()
        obj.import_installed()
