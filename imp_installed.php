<?php
	include 'common.php';
	try {
		imp_installed_org();
		$msg= "現在の状態でオリジナルを置き換えました。";
	} catch (Exception $e) {
		$msg= "オリジナルのデータの更新に失敗しました。\n".$e->getMessage();
	}
	print $msg . "\n";
?>
