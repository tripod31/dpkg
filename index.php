<?php
    include 'common.php';          

    require('sqlbuilder.php');
    
    #smarty
    $smarty = new Smarty;
    $smarty->compile_check = true;
    #$smarty->debugging = true;

    // 作業用ディレクトリの指定 
    $smarty->template_dir = './templates/';
    $smarty->compile_dir  = './templates_c/';
    $smarty->config_dir   = './configs/';
    $smarty->cache_dir    = './cache/';
    
    session_start();
    
    #CGI引数処理
    //print_r($_POST);
    //print_r($_COOKIE);
    //print_r($_SESSION);
    
    #$q_type
    $q_type = "current";
    if (array_key_exists("query",$_POST)){
        $q_type="user";
    }
    
    if (array_key_exists("current",$_POST)){
        $q_type="current";
    }
    if (array_key_exists("org",$_POST)){
        $q_type="org";
    }    
    if (array_key_exists("common",$_POST)){
        $q_type="common";
    }
    if (array_key_exists("diff_cur",$_POST)){
        $q_type="diff_cur";
    } 
    
    if (array_key_exists("diff_org",$_POST)){
        $q_type="diff_org";
    }
    if (array_key_exists("update",$_POST)){
        $q_type="update";
    }
    
    if (array_key_exists("ref_cur",$_POST)){
        $q_type="ref_cur";
    }
    if (array_key_exists("ref_org",$_POST)){
        $q_type="ref_org";
    }
    
    #$q_name
    if (array_key_exists("q_name",$_POST)) {
        $q_name = $_POST['q_name'];
    } else {
        if (array_key_exists('q_name',$_SESSION)){
            $q_name = $_SESSION['q_name'];
        } else {
            $q_name = "";
        }
    }
    $_SESSION['q_name'] = $q_name;
    
    #$hide_lib
    if (!array_key_exists("hide_lib",$_SESSION)) {
        #セッションがない場合
        $hide_lib = true;
    } else {
        $hide_lib = array_key_exists("hide_lib",$_POST)? true:false;
    }
    $_SESSION['hide_lib'] = $hide_lib?"true":"false";
    
    #$hide_rc
    if (!array_key_exists("hide_rc",$_SESSION)) {
        #セッションがない場合
        $hide_rc = true;
    } else {
        $hide_rc = array_key_exists("hide_rc",$_POST)? true:false;
    }
    $_SESSION['hide_rc'] = $hide_rc?"true":"false";    
    
    $msg="";
    switch ($q_type){
        case "ref_cur":
            try {
                imp_installed();
                $msg= "現在のデータを更新しました。";
            } catch (Exception $e) {
                $msg= "現在のデータの更新に失敗しました。\n".$e->getMessage();
            }

            break;

        case "ref_org":
            try {
                imp_installed_org();
                $msg="現在の状態でオリジナルを置き換えました。";
            } catch (Exception $e){
                $msg= "オリジナルのデータの更新に失敗しました。\n".$e->getMessage();
            }
            break;
        default:

    }

    disp($q_type,$smarty,$msg,$q_name,$hide_lib,$hide_rc);
    
    function getErrorMsg($conn) {
    $err = $conn->errorInfo();
        return $err[2];
    }
    
    function disp($q_type,$smarty,$msg,$q_name,$hide_lib,$hide_rc){
        #クエリ表示
        $conn=new PDO(DBFILE,null,null);

        #SQL作成
        $osql = new SQLBuilder();
        switch($q_type){
            case "user":
                $str = $_POST["sql"];
                $str = str_replace("\\",'',$str);
                $osql->base_sql = $str;
                break;
            case "current":
            case "ref_cur":
                $osql->base_sql="SELECT status,installed.name as name,version,description FROM installed";
                $osql->order_by="installed.name";
                break;
            case "org":
            case "ref_org":
                $osql->base_sql="SELECT * FROM installed_org";
                $osql->order_by="installed_org.name";
                break;
            case "common":
                $osql->base_sql = "SELECT installed.name, installed_org.version as original, installed.version as current,installed.status " .
    "FROM installed, installed_org";
                $osql->add_cond("installed.name = installed_org.name");
                $osql->order_by="installed.name";
                break;
            case "update":
                $osql->base_sql = "SELECT installed.name, installed_org.version as original, installed.version as current,installed.status,installed.description " .
    "FROM installed, installed_org";
                $osql->add_cond("installed.name = installed_org.name");
                $osql->add_cond("not installed.version = installed_org.version");
                $osql->order_by="installed.name";
                break;
            case "diff_org":
                $osql->base_sql = "SELECT installed_org.name,installed_org.status as status,installed_org.version as version,installed_org.description as description " .
    "FROM installed_org LEFT JOIN installed ON installed.name = installed_org.name";
                $osql->add_cond("installed.name is null");
                $osql->order_by="installed_org.name";
                break;
            case "diff_cur":
                $osql->base_sql="SELECT installed.name ,installed.status as status,installed.version as version,installed.description " . 
    "FROM installed LEFT JOIN installed_org ON installed.name = installed_org.name";
                $osql->add_cond("installed_org.name is null");
                $osql->order_by="installed.name";
                break;
           
            default:
                $osql->base_sql="SELECT * FROM installed";
        }

        if ($hide_lib) {
            switch($q_type) {
            case "user":
                break;
            case "current":
            case "ref_cur":
            case "common":
            case "update":
            case "diff_cur":
                $osql->add_cond("not installed.name like 'lib%'");
                break;
            case "org":
            case "ref_org":
            case "diff_org":
                $osql->add_cond("not installed_org.name like 'lib%'");
                break;                
            default:
                $osql->add_cond("not installed.name like 'lib%'");                
            }
        }
        
        if ($hide_rc) {
            switch($q_type) {
            case "user":
                break;
            case "current":
            case "ref_cur":
            case "common":
            case "update":
            case "diff_cur":
                $osql->add_cond("not installed.status='rc'");
                break;
            case "org":
            case "ref_org":
            case "diff_org";
                $osql->add_cond("not installed_org.status='rc'");
                break;                
            default:
                $osql->add_cond("not installed.status='rc'");                
            }
        }
        
        if (strlen($q_name)>0 ) {
            switch($q_type) {
            case "user":
                break;
            case "current":
            case "ref_cur":
            case "common":
            case "update":
            case "diff_cur":
                $osql->add_cond(sprintf("installed.name like '%%%s%%'",$q_name));
                break;
            case "org":
            case "ref_org":
            case "diff_org":
                $osql->add_cond(sprintf("installed_org.name like '%%%s%%'",$q_name));
                break;                
            default:
                $osql->add_cond(sprintf("installed.name like '%%%s%%'",$q_name));
            }
        }
        
        $sql=$osql->get_sql();
        $st=$conn->query($sql);
        if ($st == null) {
            #エラー表示
            $smarty->assign("sql",$sql);
            $err = $conn->errorInfo();
            $smarty->assign("error",$err[2]);
            if ($hide_lib ){
                $smarty->assign("hide_lib","checked");
            } else {
                $smarty->assign("hide_lib","");
            }
            if ($hide_rc ){
                $smarty->assign("hide_rc","checked");
            } else {
                $smarty->assign("hide_rc","");
            }            
            
            $smarty->assign("q_name",$q_name);
            $smarty->assign("msg","");
            $smarty->display("error.tpl");
            return;
        }
        $cnt_col = $st->columnCount();
        
        $col_names=array();
        for ($i=0;$i<$cnt_col;$i++){
            $c=$st->getColumnMeta($i);
            array_push($col_names,$c["name"]);
        }
        
        $rows=array();
        while ($row=$st->fetch(PDO::FETCH_NUM)){
            $cols = array();
            for ($col=0;$col<$cnt_col;$col++){
                array_push($cols,$row[$col] != null?$row[$col]:"&nbsp;");
            }
            array_push($rows,$cols);
        }
        
        get_saved_time($conn,$smarty);
        $conn=null;
        
        #表示
        $smarty->assign("col_names",$col_names);
        $smarty->assign("rows",$rows);
        $smarty->assign("row_count",count($rows));
        $smarty->assign("msg",$msg);
        $smarty->assign("sql",$sql);
        if ($hide_lib ){
            $smarty->assign("hide_lib","checked");
        } else {
            $smarty->assign("hide_lib","");
        }
        if ($hide_rc ){
            $smarty->assign("hide_rc","checked");
        } else {
            $smarty->assign("hide_rc","");
        }
        $smarty->assign("q_name",$q_name);
        
        $q_names = array(
            "user" => "ユーザークエリー",
            "current" => "現在","ref_cur" => "現在",
            "org"=>"オリジナル","ref_org"=>"オリジナル",
            "common"=>"共通",
            "diff_cur"=>"差分：現在のみ",
            "diff_org"=>"差分：オリジナルのみ",            
            "update"=>"アップデート");
        $smarty->assign("q_type",$q_names[$q_type]);
        
        $smarty->display("disp.tpl");
    }
    
    function get_saved_time($conn,$smarty){
        $saved_time_org="未読み込み";
        $saved_time_cur="未読み込み";
        $st = $conn->query("SELECT info FROM info WHERE name='saved_time_org'");
        if ($st!=null){
            $row = $st->fetch(PDO::FETCH_ASSOC);            
            if ($row!=null){
                $saved_time_org=$row['info'];
            }
        }
        
        $st = $conn->query("SELECT info FROM info WHERE name='saved_time_cur'");
        if ($st!=null){
            $row = $st->fetch(PDO::FETCH_ASSOC);            
            if ($row!=null){
                $saved_time_cur=$row['info'];
            }

        }
        $smarty->assign("saved_time_org",$saved_time_org);        
        $smarty->assign("saved_time_cur",$saved_time_cur);
    }
?>
