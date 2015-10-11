<?php
    define("DBFILE","sqlite:./dpkg.db");
    
    abstract class import {
        abstract protected function open_list_command();
        abstract protected function read_package($stmt,$tbl,$line,$lno);
        
        public function imp_installed(){
            $conn=new PDO(DBFILE,null,null);
            if ($conn->beginTransaction()===FALSE)
                throw new Exception($this->getErrorMsg($conn));
            if ($conn->exec("DELETE from installed")===FALSE)
                throw new Exception($this->getErrorMsg($conn));
            $pp = $this->open_list_command();
            $this->read_packages($conn,$pp,'installed');
            pclose($pp);
    
            #読み取り時刻
            $sql=sprintf("UPDATE info SET info='%s' WHERE name='saved_time_cur'",date( "Y/m/d H:i:s", time()));
            if( $conn->exec($sql)===FALSE)
                throw new Exception($this->getErrorMsg($conn));
    
            if($conn->commit()===FALSE)
                throw new Exception($this->getErrorMsg($conn));
        }
    
        public function imp_installed_org($fp=null){
            $conn=new PDO(DBFILE,null,null);
            if ($conn->beginTransaction()===FALSE)
                throw new Exception($this->getErrorMsg($conn));
            if ($conn->exec("DELETE from installed_org")===FALSE)
                throw new Exception($this->getErrorMsg($conn));
            if ($fp==null){
                $fp=$this->open_list_command();
            }
            $this->read_packages($conn,$fp,'installed_org');
    
            #読み取り時刻
            $sql=sprintf("UPDATE info SET info='%s' WHERE name='saved_time_org'",date( "Y/m/d H:i:s", time()));
            if( $conn->exec($sql)===FALSE)
                throw new Exception($this->getErrorMsg($conn));
    
            if ($conn->commit()===FALSE)
                throw new Exception($this->getErrorMsg($conn));
        }
        
        protected function read_packages($conn,$fp,$tbl){
            $lno = 0;
            $stmt=$conn->prepare(sprintf("INSERT INTO %s values(:status,:name,:version,:desc)",$tbl));
            if ( $stmt===FALSE)
                throw new Exception("sql prepare err:".$this->getErrorMsg($conn));
            while ($line = fgets($fp)){
                $lno++;
                $this->read_package($stmt,$tbl,$line,$lno);
            }
        }
        protected function getErrorMsg($conn){        
            $arr=$conn->errorInfo();
            $msg = implode(":",$arr);     
            return $msg;
        }
    }
    
    abstract class import_dir extends import{
        protected $pkg_dir="";
        
        function __construct($pkg_dir){
            if (!file_exists($pkg_dir))
                throw new Exception($pkg_dir."がありません");
            $this->pkg_dir = $pkg_dir;
        }
	}
    
    class import_dpkg extends import{
        protected function open_list_command(){
            if (!file_exists("/usr/bin/dpkg"))
                throw new Exception("/usr/bin/dpkgがない");      
            $pp = popen("dpkg -l","r");
            if ($pp === FALSE)
                throw new Exception("dpkg -lでエラー");
            return $pp;            
        }
                
        protected function read_package($stmt,$tbl,$line,$lno){
            if ($lno <= 5)
                return; #ヘッダスキップ
            
            $res = preg_match("/(\S+)\s+(\S+)\s+(\S+)(.+)/",$line,$arr);
            if ($res){
                $parm = array(':status' => $arr[1],':name'=>$arr[2],':version'=>$arr[3],':desc'=>str_replace("'","",$arr[4]));
                if ( $stmt->execute($parm)===FALSE)
                    throw new Exception($this->getErrorMsg($conn));
            } else {
                print "dpkg-l:フォーマットが変<BR>" . $line . "<BR>";
            }
        }       
    }
    
    class import_deb_dir extends import_dir{        
        protected function open_list_command(){
            $command = sprintf("find '%s' -name *.deb -print",$this->pkg_dir);
            $pp = popen($command,"r");
            if ($pp === FALSE)
                throw new Exception("findでエラー");
            return $pp;
        }
        
        protected function read_package($stmt,$tbl,$package,$lno){
            $fp = popen(sprintf("dpkg -I '%s'", rtrim($package)),"r");
            if ($fp === FALSE)
                throw new Exception("dpkg -Iでエラー");
            $all_line="";
            $cols = array();
            while($line = fgets($fp)){
                if( preg_match("/(Package|Version|Architecture|Description): (.+)$/",$line,$matches))
                    $cols[$matches[1]]=$matches[2];
            }
            
            if (count($cols)==4){
                $parm = array(
                    'status'=>'ii',':name' => $cols["Package"],':version'=>$cols["Version"],':desc'=>$cols["Architecture"]." ".$cols["Description"]);
                if ( $stmt->execute($parm)===FALSE)
                    throw new Exception("sql exec err:".$this->getErrorMsg($conn));
            } else {
                print "dpkg -I:フォーマットが変<BR>" . $package . "<BR>";
            }
        }
    }
    
    class import_rpm extends import{

        protected function open_list_command(){
            if (!file_exists("/bin/rpm")&&!file_exists("/usr/bin/rpm"))
                throw new Exception("rpmがない");
            $pp = popen("rpm -qa","r");
            if ($pp === FALSE)
                throw new Exception("rpm -qaでエラー");
            return $pp;
        }
                
        protected function read_package($stmt,$tbl,$package,$lno){
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
                    $parm = array(':status'=>'installed',':name' => $name,':version'=>$arr[2],':desc'=>str_replace("'","",$arr[3]));
                    if ( $stmt->execute($parm)===FALSE)
                        throw new Exception($this->getErrorMsg($conn));
                }
            } else {
                print "rpm -q:フォーマットが変<BR>" . $all_line . "<BR>";
            }
        }
    }
    
    class import_rpm_dir extends import_dir{        
        protected function open_list_command(){
            $command = sprintf("find '%s' -name *.rpm -print",$this->pkg_dir);
            $pp = popen($command,"r");
            if ($pp === FALSE)
                throw new Exception("findでエラー");
            return $pp;
        }
                
        protected function read_package($stmt,$tbl,$package,$lno){
			$cmd =sprintf("rpm --queryformat '%%{NAME}__TAB__%%{VERSION}__TAB__%%{DESCRIPTION}' -qp '%s'", rtrim($package));
            #print("[".$cmd."]"."\n");
            $fp = popen($cmd,"r");
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
                    $parm = array(':status'=>'installed',':name' => $name,':version'=>$arr[2],':desc'=>str_replace("'","",$arr[3]));
                    if ( $stmt->execute($parm)===FALSE)
                        throw new Exception($this->getErrorMsg($conn));
                }
            } else {
                print "rpm -q:フォーマットが変<BR>" . $all_line . "<BR>";
            }
        }
    }
              
    #ファクトリメソッド
    function create_import(){
        $oImp = null;
        if (file_exists("/usr/bin/dpkg")){
            $oImp = new import_dpkg();
        }elseif (file_exists("/bin/rpm")){
            $oImp = new import_rpm();
        }else{
            throw new Exception("dpkgもrpmもありません");
        }
        return $oImp;
    }
    
    function create_import_dir($pkg_dir){
        $oImp = null;
        if (file_exists("/usr/bin/dpkg")){
            $oImp = new import_deb_dir($pkg_dir);
        }elseif (file_exists("/bin/rpm")||file_exists("/usr/bin/rpm")){
            $oImp = new import_rpm_dir($pkg_dir);
        }else{
            throw new Exception("dpkgもrpmもありません");
        }
        return $oImp;
    }
?>
