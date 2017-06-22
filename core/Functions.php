<?php

function startClock()
{
    $startTime = microtime(true);
    echo "Start Time: " . date("Y/m/d H:i:s") . "<br>";
    return $startTime;
}

function endClock($startTime)
{
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) / 60;
    echo "Execution time: $executionTime mins";
    echo "End Time: " . date('Y-m-d H:i:s') . "<br>";
}

function decrypt($infoToDecrypt)
{
    return Crypt::decrypt($infoToDecrypt);
}