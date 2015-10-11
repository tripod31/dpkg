<?php
    require_once 'common.php';
    
    $opts = getopt("d:");
    if ($opts===FALSE){
        print("オプションが変\n");
        exit;
    }
    if (array_key_exists("d", $opts)){
        try {       
            $oImp=create_import_dir($opts["d"]);
            $oImp->imp_installed_org();
            $msg= sprintf("%s下のパッケージファイルからオリジナルを置き換えました。",$opts["d"]);
        } catch (Exception $e) {
            $msg= "オリジナルのデータの更新に失敗しました。\n".$e->getMessage();
        }    
    }else{
        try {
            $oImp=create_import();
            $oImp->imp_installed_org();
            $msg= sprintf("オリジナルを置き換えました。");
        } catch (Exception $e) {
            $msg= "オリジナルのデータの更新に失敗しました。\n".$e->getMessage();
        }
    }

    print $msg . "\n";
?>
