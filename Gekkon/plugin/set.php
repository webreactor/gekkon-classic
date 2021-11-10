<?php
/*
$tag['arg']=explode('=',$tag['arg'],2);
$tag['arg'][0]=parse_var($tag['arg'][0]);
if($tag['arg'][1][0]=='$')
$tag['arg'][1]=parse_var($tag['arg'][1]);
$bin_open='<? '.$tag['arg'][0].'='.$tag['arg'][1].'; ?>';

*/

$t=explode(' ',$tag['arg']);
$bin_open='<?php ';
foreach($t as $tt)
{
if(isset($tt[0]))
if($tt[0]=='$'||$tt[0]=='@')$tt=parse_var($tt);
$bin_open.=$tt.' ';
}
$bin_open.='; ?>';

?>