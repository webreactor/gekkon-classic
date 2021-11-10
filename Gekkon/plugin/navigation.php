<?php
//ver 4.1

if( empty($tag['arg']['modern']) )
{//old-style
$data = reset( $tag['arg'] );
$bin_open="
<?php
\$all={$data}['all'];
\$now={$data}['now'];
if(!isset({$data}['frame']))
\$frame=5;
else
\$frame={$data}['frame'];
\$var={$data}['page'];
if(\$all>1)
if(\$now>\$frame)
echo '<a href=\"'.arrToUrl(array(\$var=>1)).'\"><<</a> ';

	if(\$now<\$frame)
	{
	\$from=1;
	\$to=\$frame*2-1;
	}
	else
	{
	\$to=\$now+\$frame-1;
	\$from=\$now-\$frame+1;
	if(\$to>\$all)
	{
	\$to=\$all;
	\$from=\$all-\$frame*2+2;
	}
	}




if(\$from<1)\$from=1;
if(\$to>\$all)\$to=\$all;

if(\$all>1)
{
for(\$i=\$from;\$i<=\$to;\$i++)
{

if(\$i!=\$now)
echo '| <a href=\"'.arrToUrl(array(\$var=>\$i)).'\">'.\$i.'</a> ';
else
echo '| <span>',\$i,'</span> ';
}
echo '|';
if(\$to<\$all)
echo ' <a href=\"'.arrToUrl(array(\$var=>\$all)).'\">>></a> ';
if(\$all<10)
{
if(\$now==0)
echo '| <span>Все</span> ';
else
echo '| <a href=\"'.arrToUrl(array(\$var=>0)).'\">Все</a> ';
}
}


?>
";
}

else

{//modern style
$data = $tag['arg']['data'];
$bin_open="
<?php
\$all={$data}['all'];
\$now={$data}['now'];
if(!isset({$data}['frame']))
\$frame=5;
else
\$frame={$data}['frame'];
\$var={$data}['page'];
if(\$all>1)
if(\$now>\$frame)
echo '<span><a href=\"'.arrToUrl(array(\$var=>NULL)).'\">1</a></span>&hellip;';

	if(\$now<\$frame)
	{
	\$from=1;
	\$to=\$frame*2-1;
	}
	else
	{
	\$to=\$now+\$frame-1;
	\$from=\$now-\$frame+1;
	if(\$to>\$all)
	{
	\$to=\$all;
	\$from=\$all-\$frame*2+2;
	}
	}




if(\$from<1)\$from=1;
if(\$to>\$all)\$to=\$all;

if(\$all>1)
{
for(\$i=\$from;\$i<=\$to;\$i++)
{

if(\$i!=\$now)
	if(\$i==1)
		echo '<span><a href=\"'.arrToUrl(array(\$var=>NULL)).'\">'.\$i.'</a></span>';
	else
		echo '<span><a href=\"'.arrToUrl(array(\$var=>\$i)).'\">'.\$i.'</a></span>';
else
	echo '<span class=\"active\">',\$i,'</span>';
}


if(\$to<\$all)
echo '&hellip;<span><a href=\"'.arrToUrl(array(\$var=>\$all)).'\">'.\$all.'</a></span>';
}


?>
";
}



?>