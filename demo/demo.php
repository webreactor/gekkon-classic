<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require 'vendor/autoload.php';

$Gekkon=new \Reactor\Gekkon\Gekkon(__dir__.'/tpl/', __dir__.'/tpl_bin/');

$Gekkon->register('data', $_SERVER);
$Gekkon->display('demo.tpl');

