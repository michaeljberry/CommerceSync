<?php
include __DIR__ . "/../../core/init.php";
ini_set("memory_limit", '2048M');
error_reporting(E_ALL & ~E_NOTICE);
$upload = basename($_FILES["csvToUpload"]["name"]);
$type = substr($upload, strrpos($upload, ".") + 1);
$size = $_FILES["csvToUpload"]["size"] / 1024;

$response = "";
$folder = WEBPLUGIN . "admin/amazontaxes/";
$downloadFolder = WEBPLUGIN . "admin/amazontaxes/";

//Add 2 Letter abbreviations to $statesToCollectTaxes array to have totals compiled
$statesToCollectTaxes = [htmlentities($_POST['state'])];

$citiesThatCollectTaxes = [
    "state" => [
        "CA" => [
            "county" => [
                "alameda" => [
                    "cities" => [
                        "albany", "hayward", "san leandro", "union city"
                    ]
                ],
                "amador" => [
                    "cities" => [
                        ""
                    ]
                ],
                "butte" => [
                    "cities" => [
                        "paradise"
                    ]
                ],
                "colusa" => [
                    "cities" => [
                        "williams"
                    ]
                ],
                "contra costa" => [
                    "cities" => [
                        "antioch", "concord", "el cerrito", "hercules",
                        "moraga", "orinda", "pinole", "pittsburg", "richmond",
                        "san pablo"
                    ]
                ],
                "del norte" => [
                    "cities" => [
                        ""
                    ]
                ],
                "el dorado" => [
                    "cities" => [
                        "placerville", "south lake tahoe"
                    ]
                ],
                "fresno" => [
                    "cities" => [
                        "huron", "reedley", "sanger", "selma"
                    ]
                ],
                "humbolt" => [
                    "cities" => [
                        "arcata", "eureka", "rio dell", "trinidad"
                    ]
                ],
                "imperial" => [
                    "cities" => [
                        "calexico"
                    ]
                ],
                "inyo" => [
                    "cities" => [
                        ""
                    ]
                ],
                "kern" => [
                    "cities" => [
                        "arvin", "delano", "ridgecrest"
                    ]
                ],
                "lake" => [
                    "cities" => [
                        "clearlake", "lakeport"
                    ]
                ],
                "los angeles" => [
                    "cities" => [
                        "avalon", "commerce", "culver city", "el monte",
                        "inglewood", "la mirada", "rico rivera",
                        "san fernando", "santa monica", "south el monte",
                        "south gate"
                    ]
                ],
                "madera" => [
                    "cities" => [
                        ""
                    ]
                ],
                "marin" => [
                    "cities" => [
                        "town of corte madera", "fairfax", "larkspur", "novato",
                        "town of anselmo", "san rafael", "sausalito"
                    ]
                ],
                "mariposa" => [
                    "cities" => [
                        ""
                    ]
                ],
                "mendocino" => [
                    "cities" => [
                        "fort bragg", "point arena", "ukiah", "willitis"
                    ]
                ],
                "merced" => [
                    "cities" => [
                        "atwater", "gustline", "los banos", "merced"
                    ]
                ],
                "mono" => [
                    "cities" => [
                        "mammoth lakes"
                    ]
                ],
                "monterey" => [
                    "cities" => [
                        "carmel", "del ray oaks", "gonzales", "greenfield",
                        "king city", "marina", "monterey", "pacific grove",
                        "salinas", "sand city", "seaside", "soledad"
                    ]
                ],
                "napa" => [
                    "cities" => [
                        ""
                    ]
                ],
                "nevada" => [
                    "cities" => [
                        "grass valley", "nevada city", "town of truckee"
                    ]
                ],
                "orange" => [
                    "cities" => [
                        "calbarea", "stanton"
                    ]
                ],
                "placer" => [
                    "cities" => [
                        ""
                    ]
                ],
                "plumas" => [
                    "cities" => [
                        ""
                    ]
                ],
                "riverside" => [
                    "cities" => [
                        "cathedral city", "coachella", "palm springs"
                    ]
                ],
                "sacramento" => [
                    "cities" => [
                        "galt", "rancho cordova", "sacramento"
                    ]
                ],
                "san benito" => [
                    "cities" => [
                        "hollister", "san juan balltista"
                    ]
                ],
                "san bernardino" => [
                    "cities" => [
                        "montclair", "san bernardino"
                    ]
                ],
                "san diego" => [
                    "cities" => [
                        "el cajon", "la mesa", "national city", "vista"
                    ]
                ],
                "san francisco" => [
                    "cities" => [
                        ""
                    ]
                ],
                "san joaquin" => [
                    "cities" => [
                        "lathrop", "menteca", "stockton"
                    ]
                ],
                "san luis obispo" => [
                    "cities" => [
                        "arroyo grande", "atascadero", "grover beach",
                        "morrow bay", "paso robles", "pismo beach",
                        "san luis obispo"
                    ]
                ],
                "san mateo" => [
                    "cities" => [
                        "san mateo", "south san francisco"
                    ]
                ],
                "santa barbara" => [
                    "cities" => [
                        "guadalupe", "santa maria"
                    ]
                ],
                "santa clara" => [
                    "cities" => [
                        "campbell"
                    ]
                ],
                "santa cruz" => [
                    "cities" => [
                        "capitola", "santa cruz", "scotts valley", "watsonville"
                    ]
                ],
                "shasta" => [
                    "cities" => [
                        "anderson"
                    ]
                ],
                "siskiyou" => [
                    "cities" => [
                        "dunsmuir", "mt shasta", "weed"
                    ]
                ],
                "solano" => [
                    "cities" => [
                        "benicia", "fairfield", "rio vista", "vacaville",
                        "vallejo"
                    ]
                ],
                "sonoma" => [
                    "cities" => [
                        "cotati", "healdsburg", "rohnert park", "santa rosa",
                        "sebastopol", "sonoma"
                    ]
                ],
                "stanislaus" => [
                    "cities" => [
                        "ceres", "oakdale"
                    ]
                ],
                "tehama" => [
                    "cities" => [
                        "red bluff"
                    ]
                ],
                "tulare" => [
                    "cities" => [
                        "dinuba", "farmersville", "porterville", "tulare",
                        "visalia"
                    ]
                ],
                "tuolumne" => [
                    "cities" => [
                        "sonora"
                    ]
                ],
                "ventura" => [
                    "cities" => [
                        "oxnard", "port hueneme"
                    ]
                ],
                "yolo" => [
                    "cities" => [
                        "davis", "west sacramento", "woodland"
                    ]
                ],
                "yuba" => [
                    "cities" => [
                        "wheatland"
                    ]
                ]
            ]
        ]
    ]

];
//print_r($citiesThatCollectTaxes);

//Store Tax Jurisdictions and Tax Totals/Jurisdiction in Tree
$taxTree = [];
$taxTreeList = "";

if ($_FILES["csvToUpload"]["error"] > 0) {
    $response = "Error: " . $_FILES["csvToUpload"]["error"] . "<br>";
} else {
    $response = "<br> Upload: $upload <br>";
    $response .= "Type: $type <br>";
    $response .= "Size: $size <br>";
    $tmpName = $_FILES["csvToUpload"]["tmp_name"];
    $taxesCsv = [];

    if (file_exists($folder . $_FILES["csvToUpload"]["name"])) {
        $response .= $_FILES["csvToUpload"]["name"] . " already exists";
    } else {
        $storagename = "amazon-taxes.csv";
        $taxTreeName = "amazon-taxes-tree.csv";
        $csvAsArray = array_map("str_getcsv", file($tmpName));

        $state = '';
        $cityTaxable = false;
        foreach ($csvAsArray as $key => $value) {
            $keyMinusOne = $key - 1;
            $keyPlusOne = $key + 1;

            //[0] - Column A in CSV - Order_ID
            $orderID = 0;

            //[23] - Column X in CSV - Total_Tax
            $totalTax = 23;

            //[42] - Column AQ in CSV - Jurisdiction Level
            $jurisdictionLevel = 42;

            //[43] - Column AR in CSV - Jurisdiction_Name
            $jurisdictionName = 43;

            //[44] - Column AS in CSV - Renamed to Tax_Marker
            $taxMarker = 44;

            //[57] - Column BF in CSV - Tax_Amount (for each Jurisdiction)
            $taxAmount = 57;

            //[58] - Column BG in CSV - Taxed_Jurisdiction_Tax_Rate
            $taxedJurisdictionTaxRate = 58;

            //[60] - Column BI in CSV - Tax_Calculation_Reason_Code
            //Taxable
            //Do not count NonTaxable, Exempt, ZeroRated
            $taxCalculationReasonCode = 60;

            //[62] - Column BK in CSV - Taxable_Amount
            $taxableAmount = 62;

            if ($key == 0) {
                $taxesCsv[] = $value;
                $taxesCsv[0][$taxMarker] = "Tax Marker";
            } else {
//                foreach($statesToCollectTaxes as $state) {
//                    $taxTree["state"][] = $state;
                if ($value[$jurisdictionLevel] == 'State') {
                    $state = $value[$jurisdictionName];
                }

                if (in_array($state, $statesToCollectTaxes) && $value[$orderID] != "") {
                    $taxesCsv[$key] = $value;
                    $taxesCsv[$key][$taxMarker] = "X";
                    $taxTree["state"][$state]["state_tax"] += $value[$taxAmount];
                    $taxTree["state"][$state]["total_tax_collected_in_state"] += $value[$taxAmount];
                    $taxTree["state"][$state]["state_taxable_amount"] += $value[$taxableAmount];
                } elseif ($value[$jurisdictionName] != $state && ($taxesCsv[$keyMinusOne][$taxMarker] == "X") && $value[$orderID] == "") {
                    //If the column AR in row does not match $state && column AS in the row above == 'X' && column A in row == ''
                    $taxesCsv[$key] = $value;
                    $taxesCsv[$key][$taxMarker] = "X";
                    if ($value[$jurisdictionLevel] == "City") {
                        //In Amazon Tax file, county always comes after city, so go to next row to get county name
                        $county = $csvAsArray[$keyPlusOne][$jurisdictionName];
                        $lowerCounty = strtolower($county);
                        $city = $value[$jurisdictionName];
                        if (in_array($state, $citiesThatCollectTaxes["state"]) && in_array_r(strtolower($city), $citiesThatCollectTaxes["state"][$state]["county"])) {
                            $cityTaxable = true;
                            $tax = $csvAsArray[$keyPlusOne][$taxableAmount];
//                            print_r($csvAsArray[$keyPlusOne]);
//                            echo "$county, $city - KeyPlusOne - $tax; ValueTaxable $value[$taxableAmount]<br><br><br>";
                            $taxTree["state"][$state]["county"][$county]["city"][$city]["city_taxable_amount"] += $tax;
                        } else {
                            $taxTree["state"][$state]["county"][$county]["county_taxable_amount"] += $value[$taxableAmount];
                        }
//                        $taxTree["state"][$state]["county"][$county]["city"][$city]["city_tax"] += $value[$taxAmount];
//                        $taxTree["state"][$state]["total_tax_collected_in_state"] += $value[$taxAmount];

                    } elseif ($value[$jurisdictionLevel] == "County") {
                        if (!$cityTaxable) {
                            $county = $value[$jurisdictionName];
//                        $taxTree["state"][$state]["county"][$county]["county_tax"] += $value[$taxAmount];
//                        $taxTree["state"][$state]["total_tax_collected_in_state"] += $value[$taxAmount];
                            $taxTree["state"][$state]["county"][$county]["county_taxable_amount"] += $value[$taxableAmount];
                        } else {
                            $cityTaxable = false;
                        }
                    }
//                    elseif($value[$jurisdictionLevel] == "District"){
//                        $district = $value[$jurisdictionName];
//                        $districtTotalTax = $value[$taxAmount];
//                        $taxTree["state"][$state]["county"][$county]["city"][$city]["district"][$district]["district_tax"] += $districtTotalTax;
//                        $taxTree["state"][$state]["total_tax_collected_in_state"] += $value[$taxAmount];
//                        $taxTree["state"][$state]["county"][$county]["city"][$city]["district"][$district]["district_taxable_amount"] += $value[$taxableAmount];
//                        $taxTree["state"][$state]["county"][$county]["city"][$city]["district_total_tax"] += $districtTotalTax;
//                        $taxTree["state"][$state]["county"][$county]["city"][$city]["district_total_taxable"] += $value[$taxableAmount];
////                            $taxTree["state"][$state]["county"][$county]["city"][$city]["district_total_rate"] += $value[$taxedJurisdictionTaxRate];
//                    }
                }
//                }
            }
        }
        $taxCSVResult = makeCSV($folder, $storagename, $taxesCsv);

        if ($taxCSVResult) {
            $response .= "<a href='" . $downloadFolder . $storagename . "'>Download the Amazon Tax Raw CSV</a>";
        }

        $list = printTree($taxTree, "");
        echo $list;
    }
}
echo $response;

function printTree($tree, $html)
{
    $html .= "<ul>";
    foreach ($tree as $key => $state) {
//        print_r($state);
        $html .= "<li><u>States</u>";
        foreach ($state as $key2 => $value) {
//            $stateTax = $value["state_tax"];
            $stateTaxableAmount = $value["state_taxable_amount"];
//            $totalTaxCollectedInState = $value["total_tax_collected_in_state"];
            $html .= "<ul><li>$key2(State)";
            $html .= "<ul>";
            $html .= "<li>State Taxes - Total Taxable Amount: $stateTaxableAmount;</li>"; //Tax Collected: $stateTax; Total Tax Collected (State, County, City, District): $totalTaxCollectedInState
            $html .= "<li><u>Counties</u><ul>";

            ksort($value['county']);

            foreach ($value["county"] as $key3 => $county) {
//                $countyTax = $county["county_tax"];
                $countyTaxableAmount = $county["county_taxable_amount"];
                $html .= "<li>$key3 County, $key2";
                $html .= "<ul>";
                $html .= "<li>County Taxes - Total Taxable Amount: $countyTaxableAmount</li>"; //Tax Collected: $countyTax;
                if ($county["city"]) {
                    $html .= "<li><u>Cities</u><ul>";
                    foreach ($county["city"] as $key4 => $city) {
//                    $cityTax = $city["city_tax"];
                        $cityTaxableAmount = $city["city_taxable_amount"];
//                    $districtTotalTax = $city['district_total_tax'];
//                    $districtTotalTaxable = $city["district_total_taxable"];
//                    $districtTotalRate = $city["district_total_rate"];
                        $html .= "<li>$key4 City, $key2";
                        $html .= "<ul>";
                        $html .= "<li>City Taxes - Total Taxable Amount: $cityTaxableAmount</li>"; //TaxCollected: $cityTax;
//                    $html .= "<li>Taxes for All Districts in this City: Total Tax: $districtTotalTax</li>"; //; Total Tax Rate: $districtTotalRate; ; Total Taxable Amount: $districtTotalTaxable
//                    $html .= "<li><u>Districts</u><ul>";
//                    foreach($city["district"] as $key5 => $district){
//                        $districtTax = $district["district_tax"];
//                        $districtTaxableAmount = $district["district_taxable_amount"];
//                        $html .= "<li>$key5 District";
//                        $html .= "<ul>";
//                        $html .= "<li>District Taxes- Tax Collected: $districtTax; Total Taxable Amount: $districtTaxableAmount</li>";
//                        $html .= "</ul>";
//                        $html .= "</li>";
//                    }
//                    $html .= "</ul></li>";
                        $html .= "</ul>";
                        $html .= "</li>";
                    }
                    $html .= "</ul></li>";
                }
                $html .= "</ul>";
                $html .= "</li>";
            }
            $html .= "</ul></li>";
            $html .= "</ul>";
            $html .= "</li>";
        }
        $html .= "</ul></li>";
    }
    $html .= "</ul>";
    return $html;
}

function makeCSV($folder, $filename, $array)
{
    $fp = fopen($folder . $filename, 'w');
    foreach ($array as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
    return true;
}

function in_array_r($needle, $haystack, $strict = false)
{
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }
    return false;
}