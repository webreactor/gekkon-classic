<?php
//version 3.1.6 - reactor edition
class Gekkon
{
var $template_path;
var $bin_path;
var $gekkon_path;
var $data;
var $plugin;
var $ukey;

//constructor
function __construct()
{
$this->template_path=SITE_DIR.'tpl/';
$this->bin_path=VAR_DIR.'tpl_bin/';
$this->gekkon_path=LIB_DIR.'Gekkon/';

$this->data=array();
$this->plugin=array();
$this->ukey=0;
$this->data['ukey']=&$this->ukey;
}

function get_ukey()
{
$this->ukey++;
return $this->ukey;
}

function add_plugin($name,$close='1',$compile='1',$st_arg='1')
{
$this->plugin[$name]['close']=$close;
if($close=='0')$compile='0';
$this->plugin[$name]['compile']=$compile;
$this->plugin[$name]['st_arg']=$st_arg;

}
//registration variable for using
function register($name,$value)
{
$this->data[$name]=$value;
}

function registers($name,&$value)
{
$this->data[$name]=&$value;
}


function fullTemplatePath($template_name)
{
return MOD_DIR.$this->template_path.$template_name;
}

function fullBinPath($template_name)
{
return $this->bin_path.$this->binDir($template_name).basename($template_name).'.php';
}

function binDir($template_name)
{
return crc32($this->template_path.$template_name).'/';
}

function display($file_name)
{
reactor_trace('display '.$file_name.' bin:'.$this->binDir($file_name));

$tfile_name=$this->fullTemplatePath($file_name);
$bin_file_name=$this->fullBinPath($file_name);

if(!is_file($tfile_name))
$virt=1;
else
$virt=0;

if(!is_file($bin_file_name) && $virt==1)
{
reactor_error('Gekkon: file '.$tfile_name.' does not exist');
return 0;
}

if($virt==0)
{
$lmt=filemtime($tfile_name);
if(is_file($bin_file_name))
$lmb=filemtime($bin_file_name);
else
$lmb=0;
if($lmt>$lmb)
$this->compile_file($file_name);
}
$this->execute($bin_file_name,$file_name,$lmb);
return 1;
}

function display_into($file_name)
{
ob_start();
ob_clean();
$this->display($file_name);
$r=ob_get_contents();
ob_end_clean();
return $r;
}

function execute($bin_name,$template_name,$template_binary_version=0)
{
global $_reactor,$Gekkon,$_db,$_user,$_RGET;
$data=array();
include $bin_name;

}

function cache_stream($template_name,$stream='')
{
	global $_reactor;
	return MD5(serialize(array($template_name,$stream,$_reactor['language'])));
}

function clear_cache($template_name,$stream='')
{
	reactor_trace('clear_cache '.$template_name.' '.$stream);

	$compile_path=$this->bin_path.$this->binDir($template_name);

	if($stream!='')
	{
		$cid=$this->cache_stream($template_name,$stream);
		$file=$compile_path.$cid[1].$cid[2].'/'.$cid.'.gz';
		reactor_trace('cid '.$file);
		if(is_file($file))unlink($file);
		return;
	}

	$bin_file_name=$this->fullBinPath($template_name);
	if (is_file($bin_file_name))
	touch($bin_file_name);
}

function compile_file($file_name,$virt_name='')
{
reactor_trace('compile_file '.$file_name.' '.$virt_name);

if($virt_name=='')$virt_name=$file_name;

include_once $this->gekkon_path.'config.php';

$template_text=file_get_contents($this->fullTemplatePath($file_name));

$bin=$this->compile($template_text);

$compile_path=$this->bin_path.$this->binDir($virt_name);
if(!is_dir($compile_path)) 	mkdir(substr($compile_path,0,-1));

file_put_contents($this->fullBinPath($virt_name), $bin, LOCK_EX);
}

function compile(&$str)
{
include_once $this->gekkon_path.'config.php';

$str=preg_replace_callback('/(<a[^>]*href=)["\']([^!].+)(["\'])/Uis','urlHeader',$str);
$str=preg_replace('/(<[^>]+src=)[",\']([^\/!].+)([",\'])/Uis','\1\3'.MOD_URL.'\2\3',$str);
$str=preg_replace('/(<[^>]+background=)["\']([^\/!].+)(["\'])/Uis','\1\3'.MOD_URL.'\2\3',$str);
$str=preg_replace('/(<link[^>]+href=)["\']([^\/!].+)(["\'])/Uis','\1\3'.MOD_URL.'\2\3',$str);

$str=preg_replace('/(<[^>]*href=)["\']!(.+)(["\'])/Uis','\1\3\2\3',$str);
$str=preg_replace('/(<[^>]*src=)["\']!(.+)(["\'])/Uis','\1\3\2\3',$str);
$str=preg_replace('/(<[^>]*background=)["\']!(.+)(["\'])/Uis','\1\3\2\3',$str);


$str=preg_replace('/{([\$@][^}]+)}/Uis','<!--echo \1-->',$str);

$rez=$this->compile_r($str);
$rez=str_replace('?><?php','',$rez);
$rez=str_replace("?>\r\n<?php","\r\n",$rez);
$rez=str_replace("?>\n<?php","\n",$rez);
$rez=str_replace('?><?','',$rez);
$rez=str_replace("?>\r\n<?","\r\n",$rez);
$rez=str_replace("?>\n<?","\n",$rez);

return $rez;
}

function compile_r(&$str)
{
if($str=='')return '';

if(!preg_match('/<!--\s*([^\s]+)\b(.*)-->/sU',$str,$arr))return $str;

$tag_pos=strpos($str,$arr[0]);
$tag_len=strlen($arr[0]);
$before_tag=substr($str,0,$tag_pos);
$tag=array();
$tag['name']=trim($arr[1]);
$tag['arg']=trim($arr[2]);
if($tag['name'][0]=='/')
reactor_error('Gekkon: compile error, dont open tag bloc - '.$arr[1].$arr[2],1);


if(!isset($this->plugin[$tag['name']]))
$tag['name']='comment';
//die('Compile error: Dont know tag - '.$tag['name']);

$tag['compile']=$this->plugin[$tag['name']]['compile'];
$tag['inner']='';
if($this->plugin[$tag['name']]['st_arg']==1)
$tag['arg']=parse_arg($tag['arg']);


if($this->plugin[$tag['name']]['close']==1)
{
$now=$tag_pos;

preg_match_all('/<!--\s*'.$tag['name'].'\b/Us',$str,$m1,PREG_OFFSET_CAPTURE);
preg_match_all('/<!--\s*\/'.$tag['name'].'\s*-->/Us',$str,$m2,PREG_OFFSET_CAPTURE);
$r=array();
foreach($m1[0] as $item)
{
if($item[1]>$now)
{
$r[$item[1]]['type']=1;
$r[$item[1]]['len']=strlen($item[0]);
}
}

foreach($m2[0] as $item)
{
if($item[1]>$now)
{
$r[$item[1]]['type']=-1;
$r[$item[1]]['len']=strlen($item[0]);
}
}

ksort($r);
$f=1;
foreach($r as $pos=>$item)
{
$f+=$item['type'];
if($f==0)
{
$now=$pos;
$end_len=$item['len'];
break;
}
}
if($f!=0)
reactor_error('Gekkon: compile error: Dont close tag '.$tag['name'],1);


$tag['inner']=substr($str,$tag_pos+$tag_len,$now-$tag_pos-$tag_len);


$after_tag=substr($str,$now+$end_len);

}
else
$after_tag=substr($str,$tag_pos+$tag_len);


return $before_tag.$this->compile_tag($tag).$this->compile_r($after_tag);

}


function compile_tag($tag)
{
$bin_open='';
$bin_close='';
include $this->gekkon_path.'plugin/'.$tag['name'].'.php';
if($tag['compile']==1)
$tag['inner']=$this->compile_r($tag['inner']);
return $bin_open.$tag['inner'].$bin_close;
}

}
//-----------------------------------------------------------------------------

function parse_arg($str)
{
//echo '<b>Gekkon debug</b> parse_arg - '.$str.'<br>';
$now=0;
$par=array();
$len=strlen($str)-1;
while($now<$len)
{
$t1=strpos($str,'=',$now);
$name=trim(substr($str,$now,$t1-$now));
while($str[++$t1]==' ');
if($str[$t1]=='"'||$str[$t1]=="'")$find=$str[$t1++];else $find=' ';
$now=strpos($str,$find,$t1);
if($now === false)$now=$len+1;
$val=substr($str,$t1,$now++-$t1);
if($find==' '&&($val[0]=='$'||$val[0]=='@'))
$val=parse_var($val);
else
$val='"'.$val.'"';
$par[$name]=$val;
}
return $par;
}

function parse_var($str)
{
//echo '<b>Gekkon debug</b> parse_var - '.$str.'<br>';
if($str[0]!='$'&&$str[0]!='@')
{if($str[0]=="'"||$str[0]=='"')return $str; else return "'$str'";}

$len=strlen($str);
if($len==1)return $str;
$now=0;
$ret='';
$p=0;
$ff=1;
$e=$len;

$t=strpos($str,'.');
if($t !== false ){$e=$t;$ff=1;}
$t=strpos($str,'->');
if($t !== false )if($t<$e){$e=$t;$ff=2;$p=1;}
if($str[0]=='@')
$ret='$data["'.substr($str,1,$e-1).'"]';
else
$ret='$this->data["'.substr($str,1,$e-1).'"]';


$now=$e;

while($now<$len)
{
	$now++;
	if($p==1)$now++;

	$e=$len;
	$p=0;$f=$ff;
	$t=strpos($str,'.',$now);
	if($t !== false ){$e=$t;$ff=1;}
	$t=strpos($str,'->',$now);
	if($t !== false )if($t<$e){$e=$t;$p=1;$ff=2;}



	$v=substr($str,$now,$e-$now);
	if($f==1)
	{
		$t=strpos($v,'(');
		if($t !== false)
		{
			$vv=substr($v,0,$t);
			$tt=strpos($v,')');

			if($tt-$t-1>0)
			$ret="$vv($ret,".parse_var(substr($v,$t+1,$tt-$t-1)).")";
			else
			$ret="$vv($ret)";
		}
		else
		{
			$vv=explode('&',$v);
			$t=parse_var($vv[0]);
			$c=count($vv);
			for($i=1;$i<$c;$i++)
			{
			$t.='['.parse_var($vv[$i]).']';
			}
			$ret.="[$t]";
		}
	}
	if($f==2)
	{
	$ret.='->'.$v;
	}
	$now=$e;

}

return $ret;
}

/**/
?>