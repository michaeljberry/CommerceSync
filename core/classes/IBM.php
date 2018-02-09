<?php

use models\ModelIBM as MIBMDB;

class IBM
{
    public static function getVAIInfo()
    {
        $company = "2";
        $location = "3";
        $library = "R37MODSDTA";
        $library2 = "R37FILES";
        $file = "VINITMB";
        $file2 = "VINITEM";
        $libfile = "VINUDEF";
        $i = 0;
        $fields[$i++] = "IFCOMP";
        $fields[$i++] = "IFLOC";
        $fields[$i++] = "IFITEM";
        $fields[$i++] = "ICTITL";
        $fields[$i++] = "CCITEM";
        $fields[$i++] = "ICITEM";
        $AryLength = count($fields);
        $sql = "SELECT " . $fields[0];
        for ($x = 1; $x < $AryLength; $x++) {
            $sql = $sql . "," . $fields[$x];
        }
        $sql = $sql . " FROM " . $library . "/" . $file . " LEFT OUTER JOIN " . $library . "/" . $file2 . " ON IFITEM = " . $file2 . ".ICITEM" . " LEFT OUTER JOIN " . $library2 . "/" . $libfile . " ON " . $file . ".IFITEM = " . $libfile . ".CCITEM" . " WHERE IFCOMP = '" . $company . "' AND IFLOC = '" . $location . "' AND IFITEM NOT LIKE '+%' AND IFITEM NOT LIKE '*%' AND IFITEM NOT LIKE '#%' FETCH FIRST 50 ROWS ONLY";
        echo $sql;
        echo "<br>";
        $result = MIBMDB::query($sql, [], 'fetch', PDO::FETCH_ASSOC);
        echo "<table><tr>";
        for ($x = 0; $x < $AryLength; $x++) {
            echo "<th> $fields[$x] </th>";
        }
        echo "</tr>";

        // Output Data of each row
        foreach ($result as $row) {
            echo "<tr> ";
            for ($x = 0; $x < $AryLength; $x++) {
                echo "<td>" . $row[$fields[$x]] . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        $sqlcount = "SELECT COUNT(" . $fields[0] . ") AS COUNT FROM " . $library . "/" . $file . " WHERE IFCOMP = '" . $company . "' AND IFLOC = '" . $location . "'";
        $count = MIBMDB::query($sqlcount, [], 'fetchColumn');
        echo $count . "<br />";
        $as400conn = null;
//        $query = $this->db->prepare("SELECT * FROM R37MODSDTA/JFILE4 WHERE IFITEM = 125839");
//        $query->execute();
//        return $query->fetchAll();
    }

    public static function getVAIInventory()
    {
        $sql = "SELECT CCITEM, CCF064, ICDIV, ICCLS, J6PL09 AS MAP, J6LPRC AS MSRP, J6PL10 AS PL10, (IFQOH - IFQCM) AS AVAIL, IFMOKI, CCF002 FROM R37MODSDTA/VINITMB LEFT OUTER JOIN R37MODSDTA/VINITEM ON IFITEM = ICITEM LEFT OUTER JOIN R37FILES/VINUDEF ON IFITEM = CCITEM LEFT OUTER JOIN R37FILES/VINPMAT ON IFCOMP = J6CMP AND IFLOC = J6LOC AND CCITEM = J6ITEM WHERE IFCOMP = '2' OR IFCOMP = '1' AND IFLOC NOT LIKE '2' AND IFLOC NOT LIKE '1Z' AND IFLOC NOT LIKE '8S' AND IFLOC NOT LIKE '1VRM' AND IFLOC NOT LIKE '4R' AND IFITEM NOT LIKE '+%' AND IFITEM NOT LIKE '*%' AND IFITEM NOT LIKE '#%' AND IFDEL NOT LIKE 'D' AND IFDEL NOT LIKE 'I' GROUP BY CCITEM, CCF064, ICDIV, ICCLS, J6PL09, J6LPRC, J6PL10, IFQOH, IFQCM, IFMOKI, CCF002 FETCH FIRST 50 ROWS ONLY";
        $result = MIBMDB::query($sql, [], 'fetch', PDO::FETCH_ASSOC);
        echo "<table>";

        $i = 0;
        $count = 0;
//        // Output Data of each row
        foreach ($result as $row) {
            echo "<tr>";
            if ($i == 0) {
                foreach ($row as $key => $value) {
                    echo "<th>" . $key . "</th>";
                }
            }
            echo "</tr>";
            echo "<tr>";
            foreach ($row as $r) {
                echo "<td>" . $r . "</td>";
            }
            echo "</tr>";
            $i++;
            $count++;
        }
        echo "</table>";
        echo $count . "<br />";
    }

    public static function sampleInv()
    {
        $sql = "SELECT ITITEM, ITAMPRICE, ITMMLITEM, ITMMLQTY, ITAMPRICE, ITAMITEM, ITAMQTY, ITEBITEM, ITEBQTY FROM R37MODSDTA/VIOSELLERC WHERE ITITEM NOT LIKE '+%' AND ITITEM NOT LIKE '*%' AND ITITEM NOT LIKE '#%' FETCH FIRST 50 ROWS ONLY";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getVIOCount()
    {
        $sql = "SELECT COUNT(ITITEM) AS COUNT FROM R37MODSDTA/VIOSELLERC WHERE ITITEM NOT LIKE '+%' AND ITITEM NOT LIKE '*%' AND ITITEM NOT LIKE '#%'";
        return MIBMDB::query($sql, [], 'fetchColumn');
    }

    public static function getBigCommerceInventory()
    {
        $sql = "SELECT ITITEM, ITMMLPRICE AS PRICE, ITMMLITEM AS ITEM, ITMMLQTY AS QTY FROM R37MODSDTA/VIOSELLCRM";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getWooCommerceInventory()
    {
        $sql = "SELECT ITITEM, ITMMLMSRP AS PRICE, ITMMLITEM AS ITEM, ITMMLQTY AS QTY FROM R37MODSDTA/VIOSELLCRM";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getEcommmerceInventory()
    {
        $sql = "SELECT ITITEM, ITMMLPRICE AS PRICE, ITMMLITEM AS ITEM, ITMMLQTY1 AS QTY FROM R37MODSDTA/VIOSELLCRM";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getInventory()
    {
        $sql = "SELECT ITITEM, {price column} AS PRICE, {item column} AS ITEM, {qty column} AS QTY FROM R37MODSDTA/VIOSELLERC";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getAmazonInventory()
    {
        $sql = "SELECT ITITEM, ITAMPRICE AS PRICE, ITAMITEM AS ITEM, ITAMQTY AS QTY FROM R37MODSDTA/VIOSELLCRA";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getEbayInventory()
    {
        $sql = "SELECT ITITEM, ITEBPRICE AS PRICE, ITEBITEM AS ITEM, ITEBQTY AS QTY FROM R37MODSDTA/VIOSELLCRE"; //ITEBPRICE AS PRICE
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getReverbInventory()
    {
        $sql = "SELECT ITITEM, ITREVPRICE AS PRICE, ITREVITEM AS ITEM, ITREVQTY AS QTY FROM R37MODSDTA/VIOSELLCRR";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function findInventory($item, $channel)
    {
        $qty1 = '';
        $qty2 = '';
        if ($channel == 'Amazon') {
            $itemcol = 'ITAMITEM';
            $qty1 = 'ITAMQTY1';
            $qty2 = 'ITAMQTY2';
        } elseif ($channel == 'Ebay') {
            $itemcol = 'ITEBITEM';
            $qty1 = 'ITEBQTY1';
            $qty2 = 'ITEBQTY2';
        } elseif ($channel == 'BigCommerce') {
            $itemcol = 'ITMMLITEM';
            $qty1 = 'ITMMLQTY1';
            $qty2 = 'ITMMLQTY2';
        } elseif ($channel == 'Reverb') {
            $itemcol = 'ITREVITEM';
            $qty1 = 'ITREVQTY1';
            $qty2 = 'ITREVQTY2';
        } elseif ($channel == 'Walmart') {
            $itemcol = 'ITWAITEM';
            $qty1 = 'ITWAQTY1';
            $qty2 = 'ITWAQTY2';
        }
        $sql = "SELECT $qty1 AS CO_ONE, $qty2 AS CO_TWO
                FROM R37MODSDTA/VIOSELLERC
                WHERE $itemcol = :item";
        $query_params = array(
            ':item' => $item
        );
        return MIBMDB::query($sql, $query_params, 'fetch', PDO::FETCH_ASSOC);
    }

    public static function getCount()
    {
//        $query = "SELECT COUNT(ICITEM) AS COUNT FROM R37MODSDTA/VINITEM WHERE ICITEM NOT LIKE '+%' AND ICITEM NOT LIKE '*%'";
        $sql = "SELECT COUNT(*) AS COUNT FROM (SELECT ICDEL, ICITEM, ICTITL, ICSUBT, ICDSC1, UP.IVVUPC0404 AS ICUPC, ICWGHT, ROW_NUMBER() OVER(ORDER BY ICITEM) AS ROWNUMBER FROM R37MODSDTA/VINITEM JOIN MELQRY/UPCFILE UP ON ICITEM = IVITEM LEFT JOIN R37MODSDTA/VINITMB ON IFITEM = ICITEM WHERE (ICDEL = 'A') OR (ICDEL != 'A' AND IFQOH > 3) GROUP BY ICDEL, ICITEM, ICTITL, ICSUBT, ICDSC1, UP.IVVUPC0404, ICWGHT) AS xxx";
        return MIBMDB::query($sql, [], 'fetchColumn');
    }

    public static function syncVAI($low = null, $high = null, $sku = null)
    {
        if (!$sku) {
            $sql = "SELECT * FROM (SELECT ICDEL, ICITEM, ICTITL, ICSUBT, ICDSC1, UP.IVVUPC0404 AS ICUPC, ICWGHT, ROW_NUMBER() OVER(ORDER BY ICITEM) AS ROWNUMBER FROM R37MODSDTA/VINITEM LEFT JOIN MELQRY/UPCFILE UP ON ICITEM = IVITEM LEFT JOIN R37MODSDTA/VINITMB ON IFITEM = ICITEM WHERE ICDEL != 'D' GROUP BY ICDEL, ICITEM, ICTITL, ICSUBT, ICDSC1, UP.IVVUPC0404, ICWGHT) AS xxx WHERE ROWNUMBER BETWEEN $low AND $high ORDER BY ICITEM";
        } else {
            $sql = "SELECT ICDEL, ICITEM, ICTITL, ICSUBT, ICDSC1, UP.IVVUPC0404 AS ICUPC, ICWGHT, ROW_NUMBER() OVER(ORDER BY ICITEM) AS ROWNUMBER FROM R37MODSDTA/VINITEM LEFT JOIN MELQRY/UPCFILE UP ON ICITEM = IVITEM LEFT JOIN R37MODSDTA/VINITMB ON IFITEM = ICITEM WHERE ICDEL != 'D' AND IFITEM = '$sku' GROUP BY ICDEL, ICITEM, ICTITL, ICSUBT, ICDSC1, UP.IVVUPC0404, ICWGHT";
        }
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function syncVAIPrice($low = null, $high = null, $company = 2)
    {
        $sql = "SELECT * FROM (SELECT J6ITEM, MAX(J6LPRC) AS J6LPRC, MAX(J6PL01) AS J6PL01, MAX(J6PL09) AS J6PL09, MAX(J6PL10) AS J6PL10, DECIMAL(AVG(FIFOCOST), 5, 2) AS FIFOCOST, ROW_NUMBER() OVER(ORDER BY J6ITEM) AS ROWNUMBER FROM R37FILES/VINPMAT LEFT JOIN MELQRY/FIFOCOST ON J6ITEM = U8ITEM LEFT JOIN R37MODSDTA/VIOSELLCRM ON ITITEM = J6ITEM WHERE J6CMP = $company AND J6LOC != '1VRM' AND J6DEL != 'I' AND J6PL10 > 0 AND ITMMLPRICE = J6PL10 GROUP BY J6ITEM ORDER BY J6ITEM) AS xxx WHERE ROWNUMBER BETWEEN $low AND $high ORDER BY J6ITEM";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function syncVAIPrices($sku, $company)
    {
        $sql = "SELECT J6DEL, J6ITEM, J6LPRC, J6PL01, J6PL09, J6PL10, FIFOCOST FROM R37FILES/VINPMAT LEFT JOIN MELQRY/FIFOCOST ON J6ITEM = U8ITEM WHERE J6CMP = $company AND J6LOC != '1VRM' AND J6ITEM = '$sku'";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getProductStatus()
    {
        $sql = "SELECT IFDEL FROM R37MODSDTA/VINITMB WHERE (IFDEL = 'A') OR (IFDEL != 'A' AND IFQOH > 3)";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }

    public static function getTrackingNum($order_id, $channelNumbers)
    {
        $sql = "SELECT CHCOM1 as USPS, E3TRAC as UPS FROM R37MODSDTA/VCOHEAD JOIN R37FILES/VCOONOT ON CHORD = OAORD JOIN R37MODSDTA/VCOSHB ON E3ORD = OAORD WHERE OACPO = '$order_id' AND OACUST IN ($channelNumbers)";
        return MIBMDB::query($sql, [], 'fetch', PDO::FETCH_ASSOC);
    }

    public static function getManualTrackingNum($order_id, $channelNumbers)
    {
        $sql = "SELECT SHCOM1 FROM R37MODSDTA/VSAHEAD JOIN R37FILES/VSAONOT ON SHORD = SAORD WHERE SACPO = '$order_id' AND SACUST IN ($channelNumbers)";
        return MIBMDB::query($sql, [], 'fetchColumn', PDO::FETCH_ASSOC);
    }

    public static function getSimilarTrackingNum($order_id, $channelNumbers)
    {
        $sql = "SELECT CHCOM1 FROM R37MODSDTA/VCOHEAD JOIN R37FILES/VCOONOT ON CHORD = OAORD WHERE OACPO LIKE '$order_id%' AND OACUST IN ($channelNumbers)";
        return MIBMDB::query($sql, [], 'fetchColumn');
    }

    public static function findNonstockItem($sku = null)
    {
        if (empty($sku)) {
            $sql = "SELECT IFITEM, IFQOH FROM (SELECT IFITEM, IFQOH, row_number() OVER (PARTITION BY IFITEM ORDER BY IFITEM) AS seqnum FROM R37MODSDTA/VINITMB WHERE ((IFDEL = 'I' AND IFQOH > 2) OR (IFDEL != 'I')) AND IFCOMP != '5' AND (IFLOC = '1' OR IFLOC = '1Z' OR IFLOC = '3' OR IFLOC = '4' OR IFLOC = '5' OR IFLOC = '3Z' OR IFLOC = '7S' OR IFLOC = '8' OR IFLOC = '8S') GROUP BY IFITEM, IFQOH) t WHERE seqnum = 1 ORDER BY IFITEM";
            return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT IFITEM FROM (SELECT IFITEM, IFQOH, row_number() OVER (PARTITION BY IFITEM ORDER BY IFITEM) AS seqnum FROM R37MODSDTA/VINITMB WHERE ((IFDEL = 'I' AND IFQOH > 2) OR (IFDEL != 'I')) AND IFCOMP != '5' AND (IFLOC = '1' OR IFLOC = '1Z' OR IFLOC = '3' OR IFLOC = '4' OR IFLOC = '5' OR IFLOC = '3Z' OR IFLOC = '7S' OR IFLOC = '8' OR IFLOC = '8S') GROUP BY IFITEM, IFQOH) t WHERE seqnum = 1 AND IFITEM = '$sku'";
            return MIBMDB::query($sql, [], 'fetchColumn', PDO::FETCH_ASSOC);
        }
    }

    public static function getSKUToDelete()
    {
        $sql = "SELECT IFITEM FROM R37MODSDTA/VINITMB JOIN R37MODSDTA/VIOSELLCRA ON IFITEM = ITITEM WHERE ((IFDEL = 'I' AND IFQOH < 3) OR (IFDEL = 'N') OR (IFQOH < 2 AND IFLOD < '20141231')) AND (IFCOMP != '5' OR IFCOMP != '2') AND (IFLOC = '1' OR IFLOC = '4' OR IFLOC = '8') GROUP BY IFITEM ORDER BY IFITEM";
        return MIBMDB::query($sql, [], 'fetchAll', PDO::FETCH_ASSOC);
    }
}
