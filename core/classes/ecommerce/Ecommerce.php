<?php

namespace Ecommerce;


class Ecommerce
{

    //Prepare channel listings into arrays for manipulation

    public static function substring_between($haystack, $start, $end)
    {
        if (stripos($haystack, $start) === false || stripos($haystack, $end) === false) {
            return false;
        } else {
            $start_position = stripos($haystack, $start) + strlen($start);
            $end_position = stripos($haystack, $end, $start_position);
            return substr($haystack, $start_position, $end_position - $start_position);
        }
    }

    public static function dd($data)
    {
        echo '<br><pre>';
        print_r($data);
        echo '</pre><br>';
    }

    public static function ddXml($data)
    {

        echo "<br><pre>";
        echo htmlentities($data);
        echo "</pre>";

    }

    public static function arrayToString($array)
    {

        $newString = "";

        foreach ($array as $value) {

            if ($value === end($array)) {

                $newString .= "$value. ";

            } else {

                $newString .= "$value, ";

            }

        }

        return $newString;

    }

    public static function removeUrlProtocol($url)
    {

        return implode(array_slice(explode("/", preg_replace("/https?:\/\//", "", $url)), 0, 1));

    }

    protected static function cellOpeningTag($value, $cellType)
    {
        $openTag = '';
        $openTag .= "<$cellType ";
        $openTag .= Ecommerce::cellFormat($value);
        $openTag .= ">";
        return $openTag;
    }

    protected static function cellClosingTag($cellType)
    {
        $closingTag = "</$cellType>";
        return $closingTag;
    }

    protected static function cellFormat($value)
    {
        $format = '';
        if (isset($value['format'])) {
            if ($value['format'] !== 'aboveZero') {
                $format .= "class='{$value['format']}'";
            } else {
                if ($value['value'] < 0) {
                    $class = "loss";
                } else {
                    $class = "gain";
                }
                $format .= "class=$class";
            }
        }
        return $format;
    }

    protected static function cellValue($value, $cellType)
    {
        $cell = '';
        if ($cellType == 'th') {
            $cell .= ucfirst($value);
        } else {
            if (!is_array($value)) {
                $cell .= $value;
            } else {
                $cell .= isset($value['url']) ? "<a href='{$value['url']}' target='_blank'>" : "";
                $cell .= isset($value['display']) ? $value['display'] : $value['value'];
                $cell .= isset($value['url']) ? "</a>" : "";
            }
        }
        return $cell;
    }

    protected static function tableRow($array, $cellType = "td")
    {
        $row = "<tr>";
        foreach ($array as $key => $value) {
            $row .= Ecommerce::cellOpeningTag($value, $cellType);
            $row .= Ecommerce::cellValue($value, $cellType);
            $row .= Ecommerce::cellClosingTag($cellType);
        }
        $row .= "<tr>";

        return $row;
    }

    protected static function arrayToTableHead($array)
    {
        $head = "<thead>";
        $headArray = array_keys($array[0]);
        $head .= Ecommerce::tableRow($headArray, "th");
        $head .= "</thead>";

        return $head;
    }

    protected static function arrayToTableBody($array)
    {
        $body = "<tbody>";
        foreach ($array as $a) {
            $body .= Ecommerce::tableRow($a);
        }
        $body .= "</tbody>";

        return $body;
    }

    public static function arrayToTable($array, $tableLabel = '')
    {
        $table = $tableLabel;
        $table .= "<table class='tableBorder'>";
        $table .= Ecommerce::arrayToTableHead($array);
        $table .= Ecommerce::arrayToTableBody($array);
        $table .= "</table>";

        return $table;
    }

    public static function sortBy($sellers, $sortBy)
    {
        $priceArray = [];
        foreach ($sellers as $key => $row) {
            $priceArray[$key] = $row[$sortBy];
        }
        array_multisort($priceArray, SORT_ASC, $sellers);

        return $sellers;
    }

    public static function toDollars($cents)
    {
        $dollars = $cents / 100;
        return Ecommerce::formatMoney($dollars);
    }

    public static function toCents($dollars)
    {
        return $dollars * 100;
    }

    public static function roundMoney($number, $places = 2)
    {
        return round($number, $places);
    }

    public static function formatMoney($number, $places = 2)
    {
        return number_format($number, $places);
    }

    public static function formatMoneyNoComma($number)
    {
        return number_format((float)$number, '2', '.', '');
    }

    public static function createFormattedDate($date, $format = 'Y/m/d')
    {
        $date = date_create($date);
        $date = $date->format($format);
        return $date;
    }

    public static function orderItemHtml($item, $total)
    {
        $sku = $item['sku'];
        $name = $item['name'];
        $price = Ecommerce::formatMoney($item['price']);
        $total += $price;
        $quantity = $item['quantity'];
        $itemHtml = "<dt><span class='hide'>Quantity x Name</span></dt>
        <dd class='product-name'>$quantity x $name<br>
        <b>$sku</b>
        </dd>
        <dt><span class='hide'>Price</span></dt>
        <dd class='product-total'><i class='fa fa-usd'></i><b>$price</b></dd><hr>";
        return [
            $itemHtml,
            $total
        ];
    }

    public static function orderHtml($oi, $total, $item_html)
    {
        extract($oi);
        $date = Ecommerce::createFormattedDate($oi['date'], 'm/d/Y');
        $tracking_url = '';
        if ($carrier == 'USPS') {
            $tracking_url = 'https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=' . $tracking_num;
        } elseif ($carrier == 'FedEx') {
            $tracking_url = 'https://www.fedex.com/apps/fedextrack/?tracknumbers=' . $tracking_num . '&language=en&cntry_code=us';
        } elseif ($carrier == 'UPS') {
            $tracking_url = 'https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=' . $tracking_num . '&loc=en_us';
        }
        $date_processed = Ecommerce::createFormattedDate($oi['date'], 'm/d/Y H:i:s');
        $status = 'Unshipped';
        if ($track_successful == '1') {
            $status = 'Shipped';
        }
        $html = "<table class='popuptable'>
        <thead>
            <th>Date</th>
            <th>Order Number</th>
            <th>Channel</th>
            <th>Status</th>
            <th>Total</th>
        </thead>
        <tbody>
            <tr>
                <td>$date</td>
                <td>$order_num</td>
                <td>$channel</td>
                <td>$status</td>
                <td><i class='fa fa-usd'></i><b>$total</b></td>
            </tr>
            <tr>
                <td colspan='5'>
                    <article class='one-third'>
                    <h3>Customer</h3>
                        <dl class='order-dl'>
                            <dt>
                                <span class='hide'>Customer Details</span>
                                <i class='fa fa-map-marker'></i>
                            </dt>
                            <dd>$first_name $last_name<br>
                            $street_address<br>";
        $html .= (!empty($street_address2) ? $street_address2 . '<br>' : '');
        $html .= "$city, $state_abbr $zip
                            </dd>
                        </dl>
                    </article>
                    <article class='one-third'>
                        <h3>Order Status</h3>
                        <dl class='order-dl'>
                            <dt>
                                <span class='hide'>Date Processed</span>
                                <i class='fa fa-calendar'></i>
                            </dt>
                            <dd>$date_processed</dd>
                            <dt>
                                <span class='hide'>Tracking</span>
                                <i class='fa fa-truck'></i>
                            </dt>
                            <dd>$carrier<br>";
        $html .= (!empty($tracking_url) ? "<a class='product_link' href=" . $tracking_url . " target='_blank'>" : "");
        $html .= "$tracking_num";
        $html .= (!empty($tracking_url) ? "</a>" : "");
        $html .= "</dd>
                        </dl>
                    </article>
                    <article class='one-third'>
                    <h3>Items</h3>
                        <dl class='item-list'>
                            $item_html
                        </dl>
                    </article>
                </td>
            </tr>";
        $html .= "</tbody>
        </table>";
        return $html;
    }

}