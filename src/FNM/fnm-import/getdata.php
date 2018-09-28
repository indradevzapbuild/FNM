<?php
require("import-fnm.php");
error_reporting(E_ALL);
ini_set('display_errors', 2);

  $imported_data=putFnm("/var/www/html/fnm/fnm-import/sample.fnm");
  echo "<pre>";
  print_r($imported_data);
 ?>