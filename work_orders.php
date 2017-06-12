<?php

include_once 'core/init.php';
include_once 'core/ibminit.php';

//$library = "R37MODSDTA";
//$file = "VINITEM";
//$i = 0;
////$fields[0] = '*';
////$fields[$i++] = "IFITEM"; //ITEM #
//$fields[$i++] = "ICITEM"; //ITEM #
////$fields[$i++] = "IFCOMP"; //COMPANY
////$fields[$i++] = "IFLOC"; //LOCATION
////$fields[$i++] = "IFBLOC"; //BIN LOCATION
////$fields[$i++] = "ICDSC1"; //DESCRIPTION 1
////$fields[$i++] = "ICDSC2"; //DESCRIPTION 2
////$fields[$i++] = "ICUPC"; //UPC
////$fields[$i++] = "ICEAN"; //EAN
////$fields[$i++] = "IFAVG"; //AVERAGE COST
////$fields[$i++] = "IFLST"; //LAST COST
////$fields[$i++] = "IFOTH"; //OTHER COST
////$fields[$i++] = "IFQOH"; //QUANTITY ON HAND
////$fields[$i++] = "IFQPO"; //QUANTITY ON PO
////$fields[$i++] = "IFQCM"; //QUANTITY COMMITTED
////$fields[$i++] = "IFQBO"; //QUANTITY BACKORDERED
////$fields[$i++] = "IFSMTD"; //SOLD MONTH-TO-DATE
////$fields[$i++] = "IFSYTD"; //SOLD YEAR-TO-DATE
////$fields[$i++] = "IFSLYR"; //SOLD LAST YEAR
////$fields[$i++] = "IFRMTD"; //RECEIVED MONTH-TO-DATE
////$fields[$i++] = "IFRYTD"; //RECEIVED YEAR-TO-DATE
////$fields[$i++] = "IFRLYR"; //RECEIVED LAST YEAR
////$fields[$i++] = "ICWGHT"; //WEIGHT
////$fields[$i++] = "IFSSM"; //SALES MONTH-TO-DATE
////$fields[$i++] = "IFSCM"; //SALES COSTS MONTH-TO-DATE
////$fields[$i++] = "IFTCY"; //TOTAL COST THIS YEAR
////$fields[$i++] = "IFTSY"; //TOTAL SALES THIS YEAR
////$fields[$i++] = "IFSSY"; //TOTAL SALES THIS YEAR
////$fields[$i++] = "IFSCY"; //TOTAL SALES COSTS THIS YEAR
//
//$AryLength = count($fields);
//$sql = "SELECT " . $fields[0] ;
//for($x = 1; $x < $AryLength; $x++) {
//    $sql = $sql . "," . $fields[$x] ;
//}
//$sql = $sql." FROM " . $library . "/" . $file . "";
//echo $sql;
//echo "<br>";
//$result = $ibmdb->query($sql);
//echo "<table><tr>";
//for($x = 0; $x < $AryLength; $x++) {
//    echo "<th> $fields[$x] </th>";
//}
//echo "</tr>";
//
//// Output Data of each row
//while($row = $result->fetch(PDO::FETCH_ASSOC)) {
//    echo "<tr> ";
//    for($x = 0; $x < $AryLength; $x++) {
//        echo "<td>" . $row[$fields[$x]] . "</td>";
//    }
//    echo "</tr>";
//}
//echo "</table>";
//$as400conn = null;
$ibmdata->get_work_orders();

//$rows = $ibmdata->get_work_orders();
//print_r($rows);
//$i = 0;
//$fieldCount = odbc_num_fields($rows);
//echo "<table class='table''><tr>";
//while($i < $fieldCount){
//    $i++;
//    $fieldName = odbc_field_name($rows, $i);
//    echo " <th>$fieldName</th>";
//}
//echo "</tr>";
//$qty = 0;
//$price = 0;
//$total = 0;
//$grand_total = 0;
//print_r($rows, 'EMPLOYEE_ID');
//
//while(odbc_fetch_row($rows)){
//    $i = 0;
//    echo " <tr>";
//    while($i<$fieldCount){
//        $i++;
//        $fieldData = trim(odbc_result($rows, $i));
//        if($fieldData == ""){
//            echo " <td>&nbsp;</td>";
//        }else{
//            echo " <td>$fieldData</td>";
//        }
//    }
//    echo "</tr>";
//}
//echo "</table>";
?>