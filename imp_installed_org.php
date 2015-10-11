<?php
    require_once 'common.php';
    if (count($argv)<2){
        print "パッケージファイルがあるフォルダを指定してください\n";
        exit;
    }

    try {       
        $oImp=create_import_dir($argv[1]);
        $oImp->imp_installed_org();
        $msg= sprintf("%s下のパッケージファイルからオリジナルを置き換えました。",$argv[1]);
    } catch (Exception $e) {
        $msg= "オリジナルのデータの更新に失敗しました。\n".$e->getMessage();
    }
    print $msg . "\n";
?>
