<?php
$t=explode(' ',$tag['arg']);
$bin_open='<?php break; case ';
foreach($t as $tt)
{
if($tt[0]=='$'||$tt[0]=='@')$tt=$Gekkon->parse_var($tt);
$bin_open.=$tt;
}
$bin_open.=': ?>';



?>
