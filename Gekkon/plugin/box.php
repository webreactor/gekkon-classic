<?php
if(strpos($tag['arg'],'=')!==false)
{	$tag['arg']=parse_arg($tag['arg']);
	$bin_open='<?php initModule('.$tag['arg']['module'].'); $this->display("box/".'.$tag['arg']['name'].'."_open.tpl"); uninitModule(); ?>';
	$bin_close='<?php initModule('.$tag['arg']['module'].'); $this->display("box/".'.$tag['arg']['name'].'."_close.tpl"); uninitModule(); ?>';
}
else
{
	$bin_open='<?php $this->display("box/".'.parse_var($tag['arg']).'."_open.tpl"); ?>';
	$bin_close='<?php $this->display("box/".'.parse_var($tag['arg']).'."_close.tpl"); ?>';
}


?>