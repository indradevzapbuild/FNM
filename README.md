# FNM
FNM package is for creating FNM file from JSON and this package can also be useful for reading fnm file ie Import and export FNM (1003 form).

# How to install
  Add package in your composer.json
```sh
 "require": {
      "others" :"packages",
        "indradevzapbuild/FNM": "dev-master",
     }
```
# Create FNM file or Export 
   Create your own json as sample.json
```sh
<?php
require "vendor/autoload.php";
use indradevzapbuild\FNM\FNM;
$fnmObj = new FNM();
$get_json = json_decode(file_get_contents('sample.json'));
$fnmObj->export($get_json);
?>
```

# Create JSON from FNM file or Import 
   Pass created FNM file to it.
```sh
<?php
require "vendor/autoload.php";
use indradevzapbuild\FNM\FNM;
$fnmObj = new FNM();
$fnmfilepath = dirname(__FILE__).'/sample.fnm';
$arrObj = $fnmObj->import($fnmfilepath);
echo "<pre>";
print_r($arrObj);
?>
```
# Note:
Please create an issue if you found any trouble.
