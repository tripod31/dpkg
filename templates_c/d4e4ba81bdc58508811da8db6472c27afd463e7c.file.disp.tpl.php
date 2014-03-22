<?php /* Smarty version Smarty-3.1.17, created on 2014-03-22 22:47:45
         compiled from ".\templates\disp.tpl" */ ?>
<?php /*%%SmartyHeaderCode:32354532e0501515b28-74170867%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd4e4ba81bdc58508811da8db6472c27afd463e7c' => 
    array (
      0 => '.\\templates\\disp.tpl',
      1 => 1324604767,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32354532e0501515b28-74170867',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'q_type' => 0,
    'row_count' => 0,
    'sql' => 0,
    'col_names' => 0,
    'col_name' => 0,
    'rows' => 0,
    'row' => 0,
    'col' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_532e05016429d4_78953034',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_532e05016429d4_78953034')) {function content_532e05016429d4_78953034($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php echo $_smarty_tpl->tpl_vars['q_type']->value;?>
:<?php echo $_smarty_tpl->tpl_vars['row_count']->value;?>
<BR>
<TEXTAREA name="sql" cols=100 rows=10><?php echo $_smarty_tpl->tpl_vars['sql']->value;?>
</TEXTAREA><BR>
<INPUT type="submit" name="query" value="クエリー">
</FORM>
<TABLE border=1 width="100%">
<TR bgcolor=gray>

<?php  $_smarty_tpl->tpl_vars['col_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['col_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['col_names']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['col_name']->key => $_smarty_tpl->tpl_vars['col_name']->value) {
$_smarty_tpl->tpl_vars['col_name']->_loop = true;
?>
<TD><?php echo $_smarty_tpl->tpl_vars['col_name']->value;?>
</TD>
<?php } ?>

<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rows']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->_loop = true;
?>
</TR>
<TR>
<?php  $_smarty_tpl->tpl_vars['col'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['col']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['row']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['col']->key => $_smarty_tpl->tpl_vars['col']->value) {
$_smarty_tpl->tpl_vars['col']->_loop = true;
?>
<TD><?php echo $_smarty_tpl->tpl_vars['col']->value;?>
</TD>
<?php } ?>
</TR>
<?php } ?>

</TABLE>
</BODY></HTML>
<?php }} ?>
