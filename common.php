<?php
    define("DBFILE","sqlite:./dpkg.db");

    function imp_installed(){
        $conn=new PDO(DBFILE,null,null);
        if ($conn->beginTransaction()===FALSE)
            throw new Exception(getErrorMsg($conn));
        if ($conn->exec("DELETE from installed")===FALSE)
            throw new Exception(getErrorMsg($conn));
        if ($conn->exec("DELETE from versions")===FALSE)
            throw new Exception(getErrorMsg($conn));
    
        if (!file_exists("/usr/bin/dpkg"))
            throw new Exception("/usr/bin/dpkgがない");
        $pp = popen("dpkg -l","r");
        if ($pp === FALSE)
            throw new Exception("dpkg -lでエラー");
        read_dpkg($conn,$pp,'installed');
        pclose($pp);
        
        /*
        if (!file_exists("/usr/bin/apt-show-versions"))
            throw new Exception("/usr/bin/apt-show-versionsがない");
        $pp = popen("apt-show-versions","r");
        if ($pp === FALSE)
            throw new Exception("apt-show-versionsでエラー");
        $lno = 0;
        while ($line = fgets($pp)){
            $res = preg_match("/(\S+)\/(\S+)/",$line,$arr);
            if ($res){
                $sql = sprintf("INSERT INTO versions values('%s','%s')",$arr[1],$arr[2]);
                if( $conn->exec($sql)===FALSE)
                    throw new Exception(getErrorMsg($conn));
            } else {
                if (substr_count($line,"No available version in archive")>=0 ) {
                    #print $line . "<BR>";
                } else {
                    print "apt-show-versions:フォーマットが変<BR>" . $line . "<BR>";
                }
            }
        }
        pclose($pp);
        */
        
        #読み取り時刻
        $sql=sprintf("UPDATE info SET info='%s' WHERE name='saved_time_cur'",date( "Y/m/d H:i:s", time()));
            if( $conn->exec($sql)===FALSE)
            throw new Exception(getErrorMsg($conn));
    
            if($conn->commit()===FALSE)
            throw new Exception(getErrorMsg($conn));
    }
    
    function imp_installed_org($fp=null){
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
        read_dpkg($conn,$fp,'installed_org');
        
        #読み取り時刻
        $sql=sprintf("UPDATE info SET info='%s' WHERE name='saved_time_org'",date( "Y/m/d H:i:s", time()));
        if( $conn->exec($sql)===FALSE)
            throw new Exception(getErrorMsg($conn));
        
        if ($conn->commit()===FALSE)
            throw new Exception(getErrorMsg($conn));
    }
    
    function read_dpkg($conn,$fp,$tbl){
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
?>
