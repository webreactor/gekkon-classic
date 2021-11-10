
<!--foreach from=$data item=$item key=$key-->
    {$key} = {$item}
    <!--if $item.is_numeric()-->
        <span style="color:red;">DIGIT</span>
    <!--/if-->
    <br>
<!--/foreach-->

<pre>{$data.print_r()}</pre>
