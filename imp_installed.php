<?php
	include 'common.php';
	try {
		imp_installed();
		$msg= "現在のデータを更新しました。";
	} catch (Exception $e) {
		$msg= "現在のデータの更新に失敗しました。\n".$e->getMessage();
	}
	print $msg . "\n";
?>