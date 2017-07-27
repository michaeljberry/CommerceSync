<?php

namespace controllers\channels;


class XMLController
{

    private static $delimiter = '~';

    public static function xmlOpenTag()
    {
        $openTag = '<?xml version="1.0" encoding="UTF-8"?>';
        return $openTag;
    }

    public static function openingXMLTag($tagName, $parameters = null)
    {
        $openingTag = "<$tagName";
        if ($parameters) {
            $openingTag .= " ";
            $openingTag .= XMLController::xmlParameters($parameters);
        }
        $openingTag .= ">";
        return $openingTag;
    }

    public static function closingXMLTag($tagname)
    {
        return "</$tagname>" . "\r\n";
    }

    /**
     * @param $tagName
     * @param $tagContents
     * @param null $parameters
     * @return string
     */
    public static function xmlTag($tagName, $tagContents, $parameters = null)
    {
        $tag = XMLController::openingXMLTag($tagName, $parameters);
        $tag .= htmlspecialchars($tagContents);
        $tag .= XMLController::closingXMLTag($tagName);
        return $tag;
    }

    /**
     * @param $key
     * @param $value
     * @param $parentKey
     * @return string
     */
    public static function generateXML($key, $value, $parentKey)
    {
        list($parameters, $parentKey) = XMLController::parameterized($parentKey);
        $generatedXML = XMLController::openingXMLTag($parentKey, $parameters) . "\r\n";
        $generatedXML .= XMLController::makeXML($value, $key);
        $generatedXML .= XMLController::closingXMLTag($parentKey);
        return $generatedXML;
    }

    /**
     * @param $xml
     * @param null $parentKey
     * @return string
     */
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
//                                'ShippingServiceCost~currency=USD' => '0.00',
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
            $generatedXML .= XMLController::generate($key, $value, $parentKey);
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
     * @return bool
     */
    public static function stringContains($key): bool
    {
        return strpos($key, XMLController::$delimiter) !== false;
    }

    /**
     * @param $parentKey
     * @param $value
     * @param $key
     * @return string
     */
    public static function children($parentKey, $value, $key): string
    {
        if (! XMLController::keyExists($value)) {
            return XMLController::generateXML($parentKey, $value, $key);
        }
        return XMLController::makeXML($value, $parentKey);

    }

    /**
     * @param $key
     * @param $value
     * @param $parentKey
     * @return string
     * @internal param $generatedXML
     */
    public static function parent($key, $value, $parentKey): string
    {
        if ( !is_numeric($key)) {
            $parentKey = $key;
            return XMLController::children($parentKey, $value, $key);
        }
        return XMLController::generateXML($key, $value, $parentKey);

    }

    /**
     * @param $key
     * @return array
     * @internal param $parameters
     * @internal param $delimiter
     */
    public static function parameterized($key): array
    {
        $parameters = null;
        if (XMLController::stringContains($key)) {
            $param = substr($key, strpos($key, XMLController::$delimiter) + 1);
            $attribute = strstr($param, '=', true);
            $attributeValue = substr($param, strpos($param, '=') + 1);
            $parameters[] = $attribute;
            $parameters[] = $attributeValue;
            $key = strstr($key, XMLController::$delimiter, true);
        }
        return array($parameters, $key);
    }

    /**
     * @param $key
     * @param $value
     * @param $parentKey
     * @return string
     * @internal param $generatedXML
     */
    public static function generate($key, $value, $parentKey): string
    {
        if (!is_array($value)) {
            list($parameters, $key) = XMLController::parameterized($key);
            return XMLController::xmlTag($key, $value, $parameters);
        }
        return XMLController::parent($key, $value, $parentKey);
    }

    /**
     * @param $parameters
     * @return string
     */
    public static function xmlParameters($parameters): string
    {
        if(is_array($parameters)) {
            $tag = $parameters[0];
            $tag .= '="';
            $tag .= $parameters[1];
            $tag .= '"';
        }else{
            $tag = $parameters;
        }
        return $tag;
    }
}
