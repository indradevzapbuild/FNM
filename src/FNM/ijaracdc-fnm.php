<?php

function getFnm($data, $add_more_data = array()) {
    //set config
//    $DIR = ROOT . '/src/Controller/FNM/';
//    file_put_contents($DIR . 'config.local.json', '{"dir":"' . $DIR . 'bin/ijaracdc"}'); //dynamic config have been set now fnm will generate
    require_once 'src/Formatter.php';
    $fnm = new \InterestSmart\FnmExporter\Formatter($data, $add_more_data);
    return $fnm->get();
}
