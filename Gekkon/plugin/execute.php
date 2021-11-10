<?php

if(!isset($tag['arg']['interface']))
$tag['arg']['interface']='""';

if(!isset($tag['arg']['template']))
$tag['arg']['template']='""';

if(!isset($tag['arg']['module']))
$tag['arg']['module']='""';

if(!isset($tag['arg']['action']))
$tag['arg']['action']='""';

$bin_open='<?php
execute('.$tag['arg']['interface'].','.$tag['arg']['action'].','.$tag['arg']['template'].','.$tag['arg']['module'].');
?>';

?>