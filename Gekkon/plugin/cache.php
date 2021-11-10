<?php

if(!isset($tag['arg']['stream'])) {
    $tag['arg']['stream']='$template_name';
}

$bin_open='<?php
$stream='.$tag['arg']['stream'].';
$cid=$this->cache_stream($template_name,$stream);
$cache_dir=dirname($bin_name)."/".$cid[1].$cid[2];
$cache_name=$cache_dir."/".$cid.".gz";
$cache_time=0;
$is_cache=is_file($cache_name);
if($is_cache) {
    $cache_time=filemtime($cache_name);
    $is_cache=($template_binary_version<$cache_time);
}
';
if(isset($tag['arg']['time']))
{
if($tag['arg']['time'][0]=='"')
$tag['arg']['time']=substr($tag['arg']['time'],1,-1);
$bin_open.='
if(time() > $cache_time + '.($tag['arg']['time']*60).')
{
$is_cache=false;
}
';
}
if(isset($tag['arg']['if']))
$bin_open.='
if(isset('.$tag['arg']['if'].')&&'.$tag['arg']['if'].'==1)
{
$is_cache=false;
}
';

$bin_open.='


if(!$is_cache)
{
if(!is_dir($cache_dir))mkdir($cache_dir);
ob_start();
?>
';
$bin_close='
<?php
$t=ob_get_contents();
file_put_contents($cache_name,gzencode($t),LOCK_EX);
ob_end_clean();
echo $t;
}
else
readgzfile($cache_name);
?>';
