{include file="header.tpl"}
{$q_type}:{$row_count}<BR>
<TEXTAREA name="sql" cols=100 rows=10>{$sql}</TEXTAREA><BR>
<INPUT type="submit" name="query" value="クエリー">
</FORM>
<TABLE border=1 width="100%">
<TR bgcolor=gray>

{foreach item=col_name from=$col_names}
<TD>{$col_name}</TD>
{/foreach}

{foreach item=row from=$rows}
</TR>
<TR>
{foreach item=col from=$row}
<TD>{$col}</TD>
{/foreach}
</TR>
{/foreach}

</TABLE>
</BODY></HTML>
