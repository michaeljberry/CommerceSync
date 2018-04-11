<?php

function startClock($print = true)
{

    $startTime = microtime(true);

    if($print)
    {

        echo "Start Time: " . date("Y/m/d H:i:s") . "<br>";

    }

    return $startTime;

}

function endClock($startTime, $print = true)
{

    $endTime = microtime(true);

    $executionTime = ($endTime - $startTime) / 60;

    if($print)
    {

        echo "Execution time: $executionTime mins";

        echo "End Time: " . date('Y-m-d H:i:s') . "<br>";

    }

    return $executionTime;

}

function decrypt($infoToDecrypt)
{

    return Crypt::decrypt($infoToDecrypt);

}

function standardCase($string)
{

    return ucwords(strtolower($string));

}
