<?php

namespace models\channels\order;

interface OrderInterface
{
    function getOrders();

    function parseOrders();

    function parseOrder();

    function getShippingCode();

    function getShippingPrice();

    function getTax();

    function getItems();

    function parseItems();

    function parseItem();

}