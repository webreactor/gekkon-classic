<?php
$k=$tag['arg']['var'];
$v=$tag['arg']['value'];
$bin_open="<?php
\$t=array();
\$t[$k]=$v;

echo arrToUrl(\$t);
?>";


?>