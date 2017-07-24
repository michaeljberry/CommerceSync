<?php

namespace controllers\channels\order;


interface OrderTracking
{
    function getOrderNumber();
    function getTrackingNumber();
    function getCarrier();
    function getShipped();
    function getSuccess();
}