<?php
/*
    Author: Indradev <indradev@zapbuild>
*/

function putFnm($file_path) {
    require_once 'src/Formatter.php';
    $directory_path=dirname(__FILE__);
    $fnm = new \InterestSmart\FnmImporter\Formatter($file_path,$directory_path);
    return $fnm->get();
}
    