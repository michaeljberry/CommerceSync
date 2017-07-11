<?php

namespace models\channels;


class XML
{

    public static function xmlOpenTag()
    {
        $openTag = '<?xml version="1.0" encoding="UTF-8"?>';
        return $openTag;
    }

    public static function openXMLParentTag($tagName, $param = null)
    {
        $parentTag = "<$tagName ";
        if (!empty($param)) {
            $parentTag .= $param;
        }
        $parentTag .= ">";
        return $parentTag;
    }

    public static function closeXMLParentTag($tagname)
    {
        return "</$tagname>";
    }

    public static function xmlTag($tagName, $tagContents, $parameters = null)
    {
        $tag = "<$tagName";
        if ($parameters) {
            $tag .= " ";
            $tag .= $parameters[0] . '="' . $parameters[1] . '"';
        }
        $tag .= ">";
        $tag .= htmlspecialchars($tagContents);
        $tag .= "</$tagName>";
        return $tag;
    }

    public static function generateXML($value, $pkey, $key)
    {
        $generatedXML = XML::openXMLParentTag($pkey);
        $generatedXML .= XML::makeXML($value, $key);
        $generatedXML .= XML::closeXMLParentTag($pkey);
        return $generatedXML;
    }

    public static function makeXML($xml, $pkey = null)
    {
        //        $xml = [
//            'Item' =>
//            [
//                'Title' => 'The Whiz Bang Awesome Product',
//                'SKU' => '123456',
//                'NameValueList' => [
//                    'Name' => 'Brand',
//                    'Value' => 'Unbranded'
//                ],
//                'NameValueList' => [
//                    'Name' => 'MPN',
//                    'Value' => '123456'
//                ],
//                'ShippingDetails' => [
//                    'ShippingServiceOptions' => [
//                        'FreeShipping' => 'true',
//                        'ShippingService' => 'ShippingMethodStandard',
//                        'ShippingServiceCost' => '0.00',
//                        'ShippingServiceAdditionalCost' => '0.00',
//                        'ShippingServicePriority' => '1'
//                    ],
//                    'ShippingServiceOptions' => [
//                        'ShippingService' => 'UPSGround',
//                        'ShippingServiceCost' => '9.99',
//                        'ShippingServiceAdditionalCost' => '9.99',
//                        'ShippingServicePriority' => '2'
//                    ]
//                ]
//            ]
//        ];

        $generatedXML = '';
        foreach ($xml as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $generatedXML .= XML::generateXML($value, $pkey, $key);
//                    $generatedXML .= Ecommerce::openXMLParentTag($pkey);
//                    $generatedXML .= Ecommerce::makeXML($value, $key);
//                    $generatedXML .= Ecommerce::closeXMLParentTag($pkey);
                } else {
                    $pkey = $key;
                    if (array_key_exists(0, $value)) {
                        $generatedXML .= XML::makeXML($value, $pkey);
                    } else {
                        $generatedXML .= XML::generateXML($value, $key, $pkey);
//                        $generatedXML .= Ecommerce::openXMLParentTag($key);
//                        $generatedXML .= Ecommerce::makeXML($value, $pkey);
//                        $generatedXML .= Ecommerce::closeXMLParentTag($key);
                    }
                }
            } else {
                $parameters = null;
                $delimiter = '~';
                if (strpos($key, $delimiter) !== false) {
                    $param = substr($key, strpos($key, $delimiter) + 1);
                    $attribute = strstr($param, '=', true);
                    $attributeValue = substr($param, strpos($param, '=') + 1);
                    $parameters[] = $attribute;
                    $parameters[] = $attributeValue;
                    $key = strstr($key, $delimiter, true);
                }
                $generatedXML .= XML::xmlTag($key, $value, $parameters);
            }
        }
        return $generatedXML;
    }
}