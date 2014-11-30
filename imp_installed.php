<?php
    include 'common.php';
    if (count($argv)<2){
    	print "dpkg -lの結果ファイルを指定してください\n";
    	exit;
    }

    try {
     	$fp = fopen($argv[1],"r");   	
        imp_installed_org($fp);
        $msg= "現在の状態でオリジナルを置き換えました。";
    } catch (Exception $e) {
        $msg= "オリジナルのデータの更新に失敗しました。\n".$e->getMessage();
    }
    print $msg . "\n";
?>
