<HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="yoshi.css" type="text/css">
<TITLE>dpkg</TITLE>
</HEAD>
<BODY>
<TABLE border=1>
<TR><TD>
<FORM method=POST>
<TABLE border=0>
<TR><TD>現在:</TD><TD>{$saved_time_cur}</TD></TR>
<TR><TD>オリジナル:</TD><TD>{$saved_time_org}</TD></TR>
</TABLE>
絞り込み：<br>
パッケージ名<INPUT type="text" name="q_name" value={$q_name}>
<INPUT TYPE="button" value="クリア" onclick="document.all['q_name'].value=''">
<INPUT type="checkbox" name="hide_lib" value="on" {$hide_lib}>lib*以外
<INPUT type="checkbox" name="hide_rc" value="on" {$hide_rc}>status=rc以外<BR>
<INPUT type="submit" name="current" value="現在">
<INPUT type="submit" name="org" value="オリジナル">
<INPUT type="submit" name="common" value="共通">
<INPUT type="submit" name="update" value="アップデート">
<INPUT type="submit" name="diff_cur" value="差分：現在のみ">
<INPUT type="submit" name="diff_org" value="差分：オリジナルのみ">
<INPUT type="submit" name="ref_cur" value="現在のデータを更新" onclick="return confirm('現在のデータを更新しますか？')">
<INPUT type="submit" name="ref_org" value="現在の状態でオリジナルを置き換える" onclick="return confirm('現在の状態でオリジナルを置き換えますか？')">
</FORM>
オリジナルのdpkg出力ファイルをアップロード：
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="dpkg" />
    <input type="submit" name="upload_org" value="アップロード"/>
</form>
{if $msg != ""}
{$msg}<BR>
{/if}
</TD></TR>
