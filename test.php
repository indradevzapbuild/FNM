<?php
 error_reporting(-1);
 ini_set('display_errors', 'On');
require "vendor/autoload.php";
use FNM\FNM;
$test = new FNM();
echo $test->test("Say me Hello");

 ?>