<?php

namespace controllers\channels\order;

use models\channels\order\OrderStats;
use ecommerce\Ecommerce;

class OrderStatsController
{
    public static function addToArray($array, $channel_array, $period)
    {
        foreach ($array as $t) {
            $channel = $t['channel'];
            $t_sales = $t['sales'];
            $t_units_sold = $t['units_sold'];
            $channel_array[$channel][$period]['sales'] = $t_sales;
            $channel_array[$channel][$period]['units_sold'] = $t_units_sold;
        }
        return $channel_array;
    }

    public static function prepareStatJson($statsArray, $duration = 'daily')
    {
        $stats = self::eachStatRow($statsArray, $duration);
        $jsonarray = $stats['json'];
        $datearray = $stats['stats_date'];

        $maxkey = self::getMaxKey($jsonarray);

        $jsonarray2 = self::sortAndFillInArray($jsonarray, $maxkey);

        $jsonarray2['x'] = $datearray;
        return $jsonarray2;
    }

    public static function eachStatRow($statsArray, $duration)
    {
        $jsonarray = [];
        $datearray = [];
        $returnArray = [];
        $timeformat = '';
        if ($duration === 'daily') {
            $timeformat = 'Y-m-d';
        } elseif ($duration === 'monthly') {
            $timeformat = 'Y-m';
        }

        foreach ($statsArray as $r => $value) {
            $date = Ecommerce::createFormattedDate($value['stats_date'], $timeformat);

            if (!preg_grep('/' . $date . '/', $datearray)) {
                $datearray[] = $date;
                $key = array_search($date, $datearray);
            }

            foreach ($value as $k => $v) {
                if ($k === 'channel') {
                    $channel = $v;
                } elseif ($k === 'stats_date') {
                    continue;
                } else {
                    $var = $k;
                    $$var = $v;
                    $jsonarray[$channel . '-' . $var][$key] = $$var;
                }
            }
        }
        $returnArray['json'] = $jsonarray;
        $returnArray['stats_date'] = $datearray;
        return $returnArray;
    }

    public static function getMaxKey($array)
    {
        $maxkey = 0;
        foreach ($array as $json) {
            $keys = array_keys($json);
            $numkey = end($keys);
            if ($numkey > $maxkey) {
                $maxkey = $numkey;
            }
        }
        return $maxkey;
    }

    public static function sortAndFillInArray($jsonarray, $maxkey)
    {
        foreach ($jsonarray as $key => $json) {
            for ($i = 0; $i <= $maxkey; $i++) {
                if (!array_key_exists($i, $json)) {
                    $json[$i] = '0';
                }
            }
            $json2 = $json;
            ksort($json2);
            $jsonarray2[$key] = $json2;
        }
        return $jsonarray2;
    }
    
    public static function stats_table($channel = null, $period = 'THISMTD', $period2 = null, $period3 = null)
    {
        $html = "<div>***Stats are only available after 9/08/2015***</div>
                <table class='stattable'><thead>
                <tr>
                <th>Channel</th>
                <th colspan='2'>Today</th>
                <th colspan='2'>Yesterday</th>
                <th colspan='2'>Week To Date</th>
                <th colspan='2'>MTD</th>
                <th colspan='2'>YTD</th>
                </tr>
                <tr>
                <th></th>
                <th>Sales</th>
                <th>Units Sold</th>
                <th>Sales</th>
                <th>Units Sold</th>
                <th>Sales</th>
                <th>Units Sold</th>
                <th>Sales</th>
                <th>Units Sold</th>
                <th>Sales</th>
                <th>Units Sold</th>
                </tr>
                </thead>";
        //Today
        //Yesterday
        //WTD
        //MTD
        //YTD
        //LYMTD
        //LYYTD
        $channel_array = [];
        $channel_array2 = [];
        //getOrderStatsSum($channel, $time, $period)
        $today = OrderStats::getSum($channel, 'TODAY', $period2, $period3);
        if (!empty($today[0]['channel'])) {
            $channel_array = self::addToArray($today, $channel_array, 'today');
        }

        $yesterday = OrderStats::getSum($channel, 'YESTERDAY', $period2, $period3);
        if (!empty($yesterday[0]['channel'])) {
            $channel_array = self::addToArray($yesterday, $channel_array, 'yesterday');
        }

        $wtd = OrderStats::getSum($channel, 'THISWTD', $period2, $period3);
        if (!empty($wtd[0]['channel'])) {
            $channel_array = self::addToArray($wtd, $channel_array, 'wtd');
        }

        $mtd = OrderStats::getSum($channel, 'THISMTD', $period2, $period3);
        if (!empty($mtd[0]['channel'])) {
            $channel_array = self::addToArray($mtd, $channel_array, 'mtd');
        }

        $ytd = OrderStats::getSum($channel, 'THISYTD', $period2, $period3);
        if (!empty($ytd[0]['channel'])) {
            $channel_array = self::addToArray($ytd, $channel_array, 'ytd');
        }

        $mtdly = OrderStats::getSum($channel, 'MTDLASTYEAR', $period2, $period3);
        if (!empty($mtdly[0]['channel'])) {
            $channel_array2 = self::addToArray($mtdly, $channel_array2, 'mtdly');
        }

        $mtotally = OrderStats::getSum($channel, 'LASTYEARMONTH', $period2, $period3);
        if (!empty($mtotally[0]['channel'])) {
            $channel_array2 = self::addToArray($mtotally, $channel_array2, 'mtotally');
        }

        $ytdly = OrderStats::getSum($channel, 'LASTYTD', $period2, $period3);
        if (!empty($ytdly[0]['channel'])) {
            $channel_array2 = self::addToArray($ytdly, $channel_array2, 'ytdly');
        }

        $ytotally = OrderStats::getSum($channel, 'LASTYEAR', $period2, $period3);
        if (!empty($ytotally[0]['channel'])) {
            $channel_array2 = self::addToArray($ytotally, $channel_array2, 'ytotally');
        }

        foreach ($channel_array as $key => $value) {
            $t_sales = Ecommerce::formatMoney((!empty($value['today']['sales']) ? $value['today']['sales'] : "0.00"));
            $t_units = (!empty($value['today']['units_sold']) ? $value['today']['units_sold'] : "0");
            $yesterday_sales = Ecommerce::formatMoney((!empty($value['yesterday']['sales']) ? $value['yesterday']['sales'] : "0.00"));
            $yesterday_units = (!empty($value['yesterday']['units_sold']) ? $value['yesterday']['units_sold'] : "0");
            $w_sales = Ecommerce::formatMoney((!empty($value['wtd']['sales']) ? $value['wtd']['sales'] : "0.00"));
            $w_units = (!empty($value['wtd']['units_sold']) ? $value['wtd']['units_sold'] : "0");
            $m_sales = Ecommerce::formatMoney((!empty($value['mtd']['sales']) ? $value['mtd']['sales'] : "0.00"));
            $m_units = (!empty($value['mtd']['units_sold']) ? $value['mtd']['units_sold'] : "0");
            $y_sales = Ecommerce::formatMoney((!empty($value['ytd']['sales']) ? $value['ytd']['sales'] : "0.00"));
            $y_units = (!empty($value['ytd']['units_sold']) ? $value['ytd']['units_sold'] : "0");
            $html .= "<tr>";
            $html .= "<td>$key</td>";
            $html .= "<td>$t_sales</td>";
            $html .= "<td>$t_units</td>";
            $html .= "<td>$yesterday_sales</td>";
            $html .= "<td>$yesterday_units</td>";
            $html .= "<td>$w_sales</td>";
            $html .= "<td>$w_units</td>";
            $html .= "<td>$m_sales</td>";
            $html .= "<td>$m_units</td>";
            $html .= "<td>$y_sales</td>";
            $html .= "<td>$y_units</td>";
            $html .= "</tr>";
        }
        if (!empty($ytdly[0]['channel'])) {
            $html .= "<tr>";
            $html .= "<th colspan='11'>Last Year</th>";
            $html .= "</tr>";
            $html .= "<tr>";
            $html .= "<th></th>";
            $html .= "<th colspan='6'></th>";
            $html .= "<th>Sales</th>";
            $html .= "<th>Units</th>";
            $html .= "<th>Sales</th>";
            $html .= "<th>Units</th>";
            $html .= "</tr>";
            foreach ($channel_array2 as $key => $value) {
                $m_sales = Ecommerce::formatMoney((!empty($value['mtdly']['sales']) ? $value['mtdly']['sales'] : "0.00"));
                $m_units = (!empty($value['mtdly']['units_sold']) ? $value['mtdly']['units_sold'] : "0");
                $mtotal_sales = Ecommerce::formatMoney((!empty($value['mtotally']['sales']) ? $value['mtotally']['sales'] : "0.00"));
                $mtotal_units = (!empty($value['mtotally']['units_sold']) ? $value['mtotally']['units_sold'] : "0");
                $y_sales = Ecommerce::formatMoney((!empty($value['ytdly']['sales']) ? $value['ytdly']['sales'] : "0.00"));
                $y_units = (!empty($value['ytdly']['units_sold']) ? $value['ytdly']['units_sold'] : "0");
                $ytotal_sales = Ecommerce::formatMoney((!empty($value['ytotally']['sales']) ? $value['ytotally']['sales'] : "0.00"));
                $ytotal_units = (!empty($value['ytotally']['units_sold']) ? $value['ytotally']['units_sold'] : "0");
                $html .= "<tr>";
                $html .= "<td>$key</td>";
                $html .= "<td colspan='6'></td>";
                $html .= "<td>$m_sales<br>Total: $mtotal_sales</td>";
                $html .= "<td>$m_units<br>Total: $mtotal_units</td>";
                $html .= "<td>$y_sales<br>Total: $ytotal_sales</td>";
                $html .= "<td>$y_units<br>Total: $ytotal_units</td>";
                $html .= "</tr>";
            }
        }
        $html .= "</table>";
        echo $html;
    }

    public static function formatChannelRecentSales($ebayRecentSales)
    {
        $items = $ebayRecentSales->searchResult;
        foreach ($items->item as $item) {
            $soldDate = Ecommerce::createFormattedDate($item->listingInfo->endTime, 'Y-m-d');
            $url = $item->viewItemURL;
        }
    }
}