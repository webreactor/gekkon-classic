<?php

$type=$tag['arg']['type'];
$pref="'input/'";
if(isset($tag['arg']['pref']))
$pref.=$tag['arg']['pref'];


$bin_open="<?php
initModule(".$type."['mod_name']);
\$this->display(".$pref.'.'.$type."['input'].'.tpl');
uninitModule();
?>";
?>