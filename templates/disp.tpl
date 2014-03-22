{include file="header.tpl"}
<FORM method=POST>
<TABLE border=1><TR><TD>
<TEXTAREA name="sql" cols=100 rows=10>{$sql}</TEXTAREA><BR>
<INPUT type="submit" name="query" value="クエリー">
</TD></TR></TABLE>
</FORM>
{$q_type}:{$row_count}<BR>
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
