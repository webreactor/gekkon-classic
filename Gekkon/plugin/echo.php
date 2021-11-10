<?php
$t=explode(' ',$tag['arg']);
$bin_open='<?php echo ';
foreach($t as $tt)
{
if(isset($tt[0]))
if($tt[0]=='$'||$tt[0]=='@')$tt=$Gekkon->parse_var($tt);
$bin_open.=' '.$tt;
}
$bin_open.='; ?>';

?>
