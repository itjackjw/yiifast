<?php

$services = [];
foreach (glob(__DIR__.'/services/*.php') as $filename) {
    $services=array_merge($services, require($filename));
}


$components = [];
foreach (glob(__DIR__.'/components/*.php') as $filename) {
    $components=array_merge($components, require($filename));
}


return [
    'services'=>$services,
    'components'=>$components
];