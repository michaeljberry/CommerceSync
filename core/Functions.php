<?php

function dd($data)
{
    echo '<br><pre>';
    print_r($data);
    echo '</pre><br>';
}

function startClock(){
    $startTime = microtime(true);
    echo date("Y/m/d H:i:s"). substr((string)$startTime, 1,6) . '<br>';
    return $startTime;
}

function endClock($startTime){
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime)/60;
    echo "Execution time: $executionTime mins";
    echo "DateTime: " . date('Y-m-d H:i:s') . "<br>";
}