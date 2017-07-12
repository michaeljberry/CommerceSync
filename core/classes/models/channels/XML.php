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
            $tag .= $parameters[0];
            $tag .= '="';
            $tag .= $parameters[1];
            $tag .= '"';
        }
        $tag .= ">";
        $tag .= htmlspecialchars($tagContents);
        $tag .= "</$tagName>";
        return $tag;
    }

    public static function generateXML($value, $parentKey, $key)
    {
        $generatedXML = XML::openXMLParentTag($parentKey);
        $generatedXML .= XML::makeXML($value, $key);
        $generatedXML .= XML::closeXMLParentTag($parentKey);
        return $generatedXML;
    }

    public static function makeXML($xml, $parentKey = null)
    {
//        $xml = [
//            'Item' =>
//                [
//                    'Title' => 'The Whiz Bang Awesome Product',
//                    'SKU' => '123456',
//                    'NameValueList' => [
//                        [
//                            'Name' => 'Brand',
//                            'Value' => 'Unbranded'
//                        ],
//                        [
//                            'Name' => 'MPN',
//                            'Value' => '123456'
//                        ]
//                    ],
//                    'ShippingDetails' => [
//                        'ShippingServiceOptions' => [
//                            [
//                                'FreeShipping' => 'true',
//                                'ShippingService' => 'ShippingMethodStandard',
//                                'ShippingServiceCost' => '0.00',
//                                'ShippingServiceAdditionalCost' => '0.00',
//                                'ShippingServicePriority' => '1'
//                            ],
//                            [
//                                'ShippingService' => 'UPSGround',
//                                'ShippingServiceCost' => '9.99',
//                                'ShippingServiceAdditionalCost' => '9.99',
//                                'ShippingServicePriority' => '2'
//                            ]
//                        ],
//                    ]
//                ]
//        ];

        $generatedXML = '';
        foreach ($xml as $key => $value) {
            if (is_array($value)) {
                $generatedXML .= XML::parent($parentKey, $key, $value);
            } else {
                $parameters = null;
                $delimiter = '~';
                list($parameters, $key) = XML::parameterized($key, $delimiter, $parameters);
                $generatedXML .= XML::xmlTag($key, $value, $parameters);
            }
        }
        return $generatedXML;
    }

    /**
     * @param $value
     * @return bool
     */
    public static function keyExists($value): bool
    {
        return array_key_exists(0, $value);
    }

    /**
     * @param $key
     * @param $delimiter
     * @return bool
     */
    public static function stringContains($key, $delimiter): bool
    {
        return strpos($key, $delimiter) !== false;
    }

    /**
     * @param $parentKey
     * @param $value
     * @param $generatedXML
     * @param $key
     * @return string
     */
    public static function children($parentKey, $value, $key): string
    {
        if (! XML::keyExists($value)) {
            return XML::generateXML($value, $key, $parentKey);
        }
        return XML::makeXML($value, $parentKey);

    }

    /**
     * @param $parentKey
     * @param $key
     * @param $value
     * @param $generatedXML
     * @return string
     */
    public static function parent($parentKey, $key, $value): string
    {
        if ( !is_numeric($key)) {
            $parentKey = $key;
            return XML::children($parentKey, $value, $key);
        }
        return XML::generateXML($value, $parentKey, $key);

    }

    /**
     * @param $key
     * @param $delimiter
     * @param $parameters
     * @return array
     */
    public static function parameterized($key, $delimiter, $parameters): array
    {
        if (XML::stringContains($key, $delimiter)) {
            $param = substr($key, strpos($key, $delimiter) + 1);
            $attribute = strstr($param, '=', true);
            $attributeValue = substr($param, strpos($param, '=') + 1);
            $parameters[] = $attribute;
            $parameters[] = $attributeValue;
            $key = strstr($key, $delimiter, true);
        }
        return array($parameters, $key);
    }
}