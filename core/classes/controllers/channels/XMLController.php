<?php

namespace controllers\channels;


class XMLController
{

    private static $delimiter = '~';

    /**
     * Create XML encoding declaration
     *
     * @return string
     */
    public static function xmlOpenTag(): string
    {
        $openTag = '<?xml version="1.0" encoding="UTF-8"?>';
        return $openTag;
    }

    /**
     * Begin XML compilation
     *
     * Sample XML Array to parse
     * $xml = [
     *  'Item' =>
     *      [
     *          'Title' => 'The Whiz Bang Awesome Product',
     *          'SKU' => '123456',
     *          'NameValueList' => [
     *              [
     *                  'Name' => 'Brand',
     *                  'Value' => 'Unbranded'
     *              ],
     *              [
     *                  'Name' => 'MPN',
     *                  'Value' => '123456'
     *              ]
     *          ],
     *          'ShippingDetails' => [
     *              'ShippingServiceOptions' => [
     *                  [
     *                      'FreeShipping' => 'true',
     *                      'ShippingService' => 'ShippingMethodStandard',
     *                      'ShippingServiceCost~currency=USD' => '0.00',
     *                      'ShippingServiceAdditionalCost' => '0.00',
     *                      'ShippingServicePriority' => '1'
     *                  ],
     *                  [
     *                      'ShippingService' => 'UPSGround',
     *                      'ShippingServiceCost' => '9.99',
     *                      'ShippingServiceAdditionalCost' => '9.99',
     *                      'ShippingServicePriority' => '2'
     *                  ]
     *              ],
     *          ]
     *      ]
     *  ];
     *
     * @param $xml
     * @param null $parentKey
     * @return string
     */
    public static function makeXML($xml, $parentKey = null): string
    {
        $generatedXML = '';
        foreach ($xml as $key => $value) {
            $generatedXML .= XMLController::generate($key, $value, $parentKey);
        }
        return $generatedXML;
    }

    /**
     * Check if passed value is array, if not, create element
     * If value is array, then element is parent and has child elements
     *
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
            return XMLController::xmlElement($key, $value, $parameters);
        }
        return XMLController::parent($key, $value, $parentKey);
    }

    /**
     * Compile single XML element
     *
     * @param $tagName
     * @param $tagContents
     * @param null $parameters
     * @return string
     */
    public static function xmlElement($tagName, $tagContents, $parameters = null): string
    {
        $tag = XMLController::openingXMLTag($tagName, $parameters);
        $tag .= htmlspecialchars($tagContents);
        $tag .= XMLController::closingXMLTag($tagName);
        return $tag;
    }

    /**
     * Create opening XML tag with optional parameters
     *
     * @param $tagName
     * @param null $parameters
     * @return string
     */
    public static function openingXMLTag($tagName, $parameters = null): string
    {
        $openingTag = "<$tagName";
        if ($parameters) {
            $openingTag .= " ";
            $openingTag .= XMLController::xmlParameters($parameters);
        }
        $openingTag .= ">";
        return $openingTag;
    }

    /**
     * Compile parameters for opening XML tag
     *
     * @param $parameters
     * @return string
     */
    public static function xmlParameters($parameters): string
    {
        if (is_array($parameters)) {
            $tag = $parameters[0];
            $tag .= '="';
            $tag .= $parameters[1];
            $tag .= '"';
        } else {
            $tag = $parameters;
        }
        return $tag;
    }

    /**
     * Create closing XML tag
     *
     * @param $tagName
     * @return string
     */
    public static function closingXMLTag($tagName): string
    {
        return "</$tagName>" . PHP_EOL;
    }

    /**
     * Check if parent element has numeric key or not
     * If key is not numeric, then proceed to compile individual child elements
     * If key is numeric, then loop array of children to then compile
     *
     * @param $key
     * @param $value
     * @param $parentKey
     * @return string
     * @internal param $generatedXML
     */
    public static function parent($key, $value, $parentKey): string
    {
        if (!is_numeric($key)) {
            $parentKey = $key;
            return XMLController::children($parentKey, $value, $key);
        }
        return XMLController::generateXML($key, $value, $parentKey);
    }

    /**
     * Check if XML element has child elements
     *
     * @param $parentKey
     * @param $value
     * @param $key
     * @return string
     */
    public static function children($parentKey, $value, $key): string
    {
        if (!XMLController::keyExists($value)) {
            return XMLController::generateXML($parentKey, $value, $key);
        }
        return XMLController::makeXML($value, $parentKey);
    }

    /**
     * Check if key exists in an array
     *
     * @param $value
     * @return bool
     */
    public static function keyExists($value): bool
    {
        return array_key_exists(0, $value);
    }

    /**
     * Compile nested XML elements
     *
     * @param $key
     * @param $value
     * @param $parentKey
     * @return string
     */
    public static function generateXML($key, $value, $parentKey): string
    {
        list($parameters, $parentKey) = XMLController::parameterized($parentKey);
        $generatedXML = XMLController::openingXMLTag($parentKey, $parameters) . PHP_EOL;
        $generatedXML .= XMLController::makeXML($value, $key);
        $generatedXML .= XMLController::closingXMLTag($parentKey);
        return $generatedXML;
    }

    /**
     * Check if key has the delimiter, and if so, setup parameters on XML element
     *
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
        return [$parameters, $key];
    }

    /**
     * Check if a string contains the delimiter
     *
     * @param $key
     * @return bool
     */
    public static function stringContains($key): bool
    {
        return strpos($key, XMLController::$delimiter) !== false;
    }
}
