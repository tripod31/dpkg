<?php
    include 'common.php';
    if (count($argv)<2){
        print "dpkg -lの結果ファイルを指定してください\n";
        exit;
    }

    try {
         $fp = fopen($argv[1],"r");       
        imp_installed_org($fp);
        $msg= sprintf("%sからオリジナルを置き換えました。",$argv[1]);
    } catch (Exception $e) {
        $msg= "オリジナルのデータの更新に失敗しました。\n".$e->getMessage();
    }
    print $msg . "\n";
?>
