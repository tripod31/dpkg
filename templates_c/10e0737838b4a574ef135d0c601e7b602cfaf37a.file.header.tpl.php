<?php /* Smarty version Smarty-3.1.17, created on 2014-03-22 22:47:45
         compiled from ".\templates\header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:23539532e050166dd30-52559220%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '10e0737838b4a574ef135d0c601e7b602cfaf37a' => 
    array (
      0 => '.\\templates\\header.tpl',
      1 => 1387533412,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '23539532e050166dd30-52559220',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'q_name' => 0,
    'hide_lib' => 0,
    'hide_rc' => 0,
    'msg' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_532e05016b8429_95832699',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_532e05016b8429_95832699')) {function content_532e05016b8429_95832699($_smarty_tpl) {?><HTML>
<HEAD>
<META http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="./yoshi.css" type="text/css">
<TITLE>dpkg</TITLE>
</HEAD>
<BODY>
<FORM method=POST>
<INPUT type="submit" name="current" value="現在">
<INPUT type="submit" name="org" value="オリジナル">
<INPUT type="submit" name="common" value="共通">
<INPUT type="submit" name="update" value="アップデート">
<INPUT type="submit" name="diff_cur" value="差分：現在のみ">
<INPUT type="submit" name="diff_org" value="差分：オリジナルのみ">
<INPUT type="submit" name="ref_cur" value="現在のデータを更新" onclick="return confirm('現在のデータを更新しますか？')">
<INPUT type="submit" name="ref_org" value="現在の状態でオリジナルを置き換える" onclick="return confirm('現在の状態でオリジナルを置き換えますか？')">
<BR><BR>
パッケージ名<INPUT type="text" name="q_name" value=<?php echo $_smarty_tpl->tpl_vars['q_name']->value;?>
>
<INPUT TYPE="button" value="クリア" onclick="document.all['q_name'].value=''">
<INPUT type="checkbox" name="hide_lib" value="on" <?php echo $_smarty_tpl->tpl_vars['hide_lib']->value;?>
>lib*以外
<INPUT type="checkbox" name="hide_rc" value="on" <?php echo $_smarty_tpl->tpl_vars['hide_rc']->value;?>
>status=rc以外<BR>
<?php if ($_smarty_tpl->tpl_vars['msg']->value!='') {?>
<?php echo $_smarty_tpl->tpl_vars['msg']->value;?>
<BR>
<?php }?>
<?php }} ?>
