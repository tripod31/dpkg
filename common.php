<?php
    define("DBFILE","sqlite:./dpkg.db");
    interface IImport{
        public function imp_installed();
        public function imp_installed_org($fp=null);
    }
    
    class import_dpkg implements IImport{
        public function imp_installed(){
            $conn=new PDO(DBFILE,null,null);
            if ($conn->beginTransaction()===FALSE)
                throw new Exception(getErrorMsg($conn));
            if ($conn->exec("DELETE from installed")===FALSE)
                throw new Exception(getErrorMsg($conn));
            if (!file_exists("/usr/bin/dpkg"))
                throw new Exception("/usr/bin/dpkgがない");
            $pp = popen("dpkg -l","r");
            if ($pp === FALSE)
                throw new Exception("dpkg -lでエラー");
            $this->read_packages($conn,$pp,'installed');
            pclose($pp);
                    
            #読み取り時刻
            $sql=sprintf("UPDATE info SET info='%s' WHERE name='saved_time_cur'",date( "Y/m/d H:i:s", time()));
            if( $conn->exec($sql)===FALSE)
            throw new Exception(getErrorMsg($conn));
    
            if($conn->commit()===FALSE)
            throw new Exception(getErrorMsg($conn));
        }
        
        public function imp_installed_org($fp=null){
            $conn=new PDO(DBFILE,null,null);
            if ($conn->beginTransaction()===FALSE)
                throw new Exception(getErrorMsg($conn));
            if ($conn->exec("DELETE from installed_org")===FALSE)
                throw new Exception(getErrorMsg($conn));
            if ($fp==null){
                if (!file_exists("/usr/bin/dpkg"))
                    throw new Exception("/usr/bin/dpkgがない");
                $fp = popen("dpkg -l","r");
                if ($fp === FALSE)
                    throw new Exception("dpkg -lでエラー");
            }
            $this->read_packages($conn,$fp,'installed_org');
            
            #読み取り時刻
            $sql=sprintf("UPDATE info SET info='%s' WHERE name='saved_time_org'",date( "Y/m/d H:i:s", time()));
            if( $conn->exec($sql)===FALSE)
                throw new Exception(getErrorMsg($conn));
            
            if ($conn->commit()===FALSE)
                throw new Exception(getErrorMsg($conn));
        }
        
        private function read_packages($conn,$fp,$tbl){
            $lno = 0;
            while ($line = fgets($fp)){
                $lno++;
                #ヘッダスキップ
                if ($lno <= 5) {
                    continue;            
                }
                $res = preg_match("/(\S+)\s+(\S+)\s+(\S+)(.+)/",$line,$arr);
                if ($res){
                    $sql = sprintf("INSERT INTO %s values('%s','%s','%s','%s')",$tbl,$arr[1],$arr[2],$arr[3],str_replace("'","",$arr[4]));
                    #print_r ($sql);
                    if ( $conn->exec($sql)===FALSE)
                        throw new Exception(getErrorMsg($conn));
                } else {
                    print "dpkg-l:フォーマットが変<BR>" . $line . "<BR>";
                }
            }
        }
    }
    
    class import_rpm implements IImport{
        public function imp_installed(){
            $conn=new PDO(DBFILE,null,null);
            if ($conn->beginTransaction()===FALSE)
                throw new Exception(getErrorMsg($conn));
            if ($conn->exec("DELETE from installed")===FALSE)
                throw new Exception(getErrorMsg($conn));
            if (!file_exists("/bin/rpm")&&!file_exists("/usr/bin/rpm"))
                throw new Exception("rpmがない");
            $pp = popen("rpm -qa","r");
            if ($pp === FALSE)
                throw new Exception("rpm -qaでエラー");
            $this->read_packages($conn,$pp,'installed');
            pclose($pp);
                    
            #読み取り時刻
            $sql=sprintf("UPDATE info SET info='%s' WHERE name='saved_time_cur'",date( "Y/m/d H:i:s", time()));
            if( $conn->exec($sql)===FALSE)
            throw new Exception(getErrorMsg($conn));
    
            if($conn->commit()===FALSE)
            throw new Exception(getErrorMsg($conn));
        }
        
        public function imp_installed_org($fp=null){
            $conn=new PDO(DBFILE,null,null);
            if ($conn->beginTransaction()===FALSE)
                throw new Exception(getErrorMsg($conn));
            if ($conn->exec("DELETE from installed_org")===FALSE)
                throw new Exception(getErrorMsg($conn));
            if ($fp==null){
                if (!file_exists("/usr/bin/rpm")&&!file_exists("/bin/rpm"))
                    throw new Exception("rpmがない");
                $fp = popen("rpm -qa","r");
                if ($fp === FALSE)
                    throw new Exception("rpm -qaでエラー");
            }
            $this->read_packages($conn,$fp,'installed_org');
            
            #読み取り時刻
            $sql=sprintf("UPDATE info SET info='%s' WHERE name='saved_time_org'",date( "Y/m/d H:i:s", time()));
            if( $conn->exec($sql)===FALSE)
                throw new Exception(getErrorMsg($conn));
            
            if ($conn->commit()===FALSE)
                throw new Exception(getErrorMsg($conn));
        }
        
        private function read_packages($conn,$p_fp,$tbl){
            $lno = 0;
            while ($package = fgets($p_fp)){
                $lno++;
                #if ($package == "gpg-pubkey")
                #    continue;
                $fp = popen(" rpm --queryformat '%{NAME}__TAB__%{VERSION}__TAB__%{DESCRIPTION}' -q " . $package,"r");
                if ($fp === FALSE)
                    throw new Exception("rpm -qでエラー");
                $all_line="";
                while($line = fgets($fp)){
                    $all_line.=str_replace("\n","",$line);
                }
                $res = preg_match("/(.+)__TAB__(.+)__TAB__(.+)/",$all_line,$arr);
                if ($res){
                    $name =$arr[1];
                    if ($name !="gpg-pubkey"){
                        $sql = sprintf("INSERT INTO %s values('%s','%s','%s','%s')",$tbl,'installed',$name,$arr[2],str_replace("'","",$arr[3]));
                        #print_r ($sql);
                        if ( $conn->exec($sql)===FALSE)
                            throw new Exception(getErrorMsg($conn));
                    }
                } else {
                    print "rpm -q:フォーマットが変<BR>" . $all_line . "<BR>";
                }
            }
        }
    }
       
    #ファクトリメソッド
    function create_import(){
        $oImp = null;
        if (file_exists("/usr/bin/dpkg")){
            $oImp = new import_dpkg();
        }else{
            $oImp = new import_rpm();
        }
        return $oImp;
    }
?>
