<?php
 error_reporting(-1);
 ini_set('display_errors', 'On');
require "vendor/autoload.php";
use indradevzapbuild\FNM\FNM;
$fnmObj = new FNM();
/**
*Export FNM
*JSON to FNM FILE
* @param json data
*/
/*$get_json = json_decode(file_get_contents('sample.json'));
$fnmObj->export($get_json);*/
/**
*Import FNM
*FNM file to JSON data
*@param $fnmfile path | $_FILES['fnm_file']['tmp_name']
*/
/*$fnmfilepath = dirname(__FILE__).'/sample.fnm';
$arrObj = $fnmObj->import($fnmfilepath);
echo "<pre>";
print_r($arrObj);*/
$fnmfilepath = dirname(__FILE__).'/sample.fnm';
$arrObj = $fnmObj->import($fnmfilepath);
echo "<pre>";
print_r($arrObj);

 ?>