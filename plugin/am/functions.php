<?php
function invokeGetReportList(MarketplaceWebService_Interface $service, $request, $product = false) {
	try {
		$response = $service->getReportList($request);

		echo ("Service Response<br />");
		echo ("=============================================================================<br />");

		echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GetReportListResponse<br />");
		if ($response->isSetGetReportListResult()) {
			echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GetReportListResult<br />");
			$getReportListResult = $response->getGetReportListResult();
			if ($getReportListResult->isSetNextToken()) {
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NextToken<br />");
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $getReportListResult->getNextToken() . "<br />");
			}
			if ($getReportListResult->isSetHasNext()) {
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HasNext<br />");
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $getReportListResult->getHasNext() . "<br />");
			}
			$reportInfoList = $getReportListResult->getReportInfoList();
			foreach ($reportInfoList as $reportInfo) {
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ReportInfo<br />");
				if ($reportInfo->isSetReportId()) {
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ReportId<br />");
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getReportId() . "<br />");
					$report_id = $reportInfo->getReportId();
				} else {
					$report_id = '';
				}
                $report_type = '';
				if ($reportInfo->isSetReportType()) {
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ReportType<br />");
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getReportType() . "<br />");
                    $report_type = $reportInfo->getReportType();
				}
				if ($reportInfo->isSetReportRequestId()) {
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ReportRequestId<br />");
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getReportRequestId() . "<br />");
				}
				if ($reportInfo->isSetAvailableDate()) {
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AvailableDate<br />");
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getAvailableDate()->format(DATE_FORMAT) . "<br />");
				}
				if ($reportInfo->isSetAcknowledged()) {
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acknowledged<br />");
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getAcknowledged() . "<br />");
				}
				if ($reportInfo->isSetAcknowledgedDate()) {
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AcknowledgedDate<br />");
					echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getAcknowledgedDate()->format(DATE_FORMAT) . "<br />");
				}
                if($product){
                    if($report_type == '_GET_MERCHANT_LISTINGS_DATA_'){
                        return $report_id;
                    }
                }elseif(!empty($report_id) ) {
					$parameters = array (
						'Marketplace' => MARKETPLACE_ID,
						'Merchant' => MERCHANT_ID,
						'Report' => @fopen('php://memory', 'rw+'),
						'ReportId' => $report_id,
					);
					$report = new MarketplaceWebService_Model_GetReportRequest($parameters);
					invokeGetReport($service, $report);
				}
			}
		}
		if ($response->isSetResponseMetadata()) {
			echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ResponseMetadata<br />");
			$responseMetadata = $response->getResponseMetadata();
			if ($responseMetadata->isSetRequestId()) {
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RequestId<br />");
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $responseMetadata->getRequestId() . "<br />");
			}
		}

	} catch (MarketplaceWebService_Exception $ex) {
		echo("Caught Exception: " . $ex->getMessage() . "<br />");
		echo("Response Status Code: " . $ex->getStatusCode() . "<br />");
		echo("Error Code: " . $ex->getErrorCode() . "<br />");
		echo("Error Type: " . $ex->getErrorType() . "<br />");
		echo("Request ID: " . $ex->getRequestId() . "<br />");
		echo("XML: " . $ex->getXML() . "<br />");
	}
}

function invokeGetReport(MarketplaceWebService_Interface $service, $request, $product) {
	try {
		$response = $service->getReport($request);

		echo ("Service Response<br />");
		echo ("=============================================================================<br />");

		echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GetReportResponse<br />");
		if ($response->isSetGetReportResult()) {
			$getReportResult = $response->getGetReportResult();
			echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GetReport");

			if ($getReportResult->isSetContentMd5()) {
				echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ContentMd5");
				echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $getReportResult->getContentMd5() . "<br />");
			}
		}
		if ($response->isSetResponseMetadata()) {
			echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ResponseMetadata<br />");
			$responseMetadata = $response->getResponseMetadata();
			if ($responseMetadata->isSetRequestId()) {
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RequestId<br />");
				echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $responseMetadata->getRequestId() . "<br />");
			}
		}

		echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Report Contents<br />");
		if($product){
            return stream_get_contents($request->getReport());
        }else {
            echo(stream_get_contents($request->getReport()) . "<br />");
        }

	} catch (MarketplaceWebService_Exception $ex) {
		echo("Caught Exception: " . $ex->getMessage() . "<br />");
		echo("Response Status Code: " . $ex->getStatusCode() . "<br />");
		echo("Error Code: " . $ex->getErrorCode() . "<br />");
		echo("Error Type: " . $ex->getErrorType() . "<br />");
		echo("Request ID: " . $ex->getRequestId() . "<br />");
		echo("XML: " . $ex->getXML() . "<br />");
	}
}


function GetOrdersToProcess(MarketplaceWebService_Interface $service, $request, $params = array()) {

	$debug = isset($params['debug']) ? $params['debug'] : false;
	$additional = true;

	$orders = array();

	try {
		$response = $service->getReportList($request);

		if ($debug) { echo ("Service Response<br />"); }
		if ($debug) { echo ("=============================================================================<br />"); }

		if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GetReportListResponse<br />"); }

		if ($response->isSetGetReportListResult()) {
			if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GetReportListResult<br />"); }
			$getReportListResult = $response->getGetReportListResult();
			if ($getReportListResult->isSetNextToken()) {
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NextToken<br />"); }
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $getReportListResult->getNextToken() . "<br />"); }
			}
			if ($getReportListResult->isSetHasNext()) {
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HasNext<br />"); }
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $getReportListResult->getHasNext() . "<br />"); }
			}
			$reportInfoList = $getReportListResult->getReportInfoList();
			foreach ($reportInfoList as $reportInfo) {
				$item = array();
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ReportInfo<br />"); }
				$report_id = '';
				if ($reportInfo->isSetReportId()) {
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ReportId<br />"); }
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getReportId() . "<br />"); }
					$report_id = $reportInfo->getReportId();
				}
				$item['report_id'] = $report_id;
				$report_type = '';
				if ($reportInfo->isSetReportType()) {
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ReportType<br />"); }
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getReportType() . "<br />"); }
					$report_type = $reportInfo->getReportType();
				}
				$item['report_type'] = $report_type;
				$report_request_id = '';
				if ($reportInfo->isSetReportRequestId()) {
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ReportRequestId<br />"); }
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getReportRequestId() . "<br />"); }
					$report_request_id = $reportInfo->getReportRequestId();
				}
				$item['report_request_id'] = $report_request_id;
				$available_date = '';
				if ($reportInfo->isSetAvailableDate()) {
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AvailableDate<br />"); }
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getAvailableDate()->format(DATE_FORMAT) . "<br />"); }
					$available_date = $reportInfo->getAvailableDate()->format(DATE_FORMAT);
				}
				$item['available_date'] = $available_date;
				$acknowledged = '';
				if ($reportInfo->isSetAcknowledged()) {
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acknowledged<br />"); }
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getAcknowledged() . "<br />"); }
					$acknowledged = $reportInfo->getAcknowledged();
				}
				$item['acknowledged'] = $acknowledged;
				$acknowledge_date = '';
				if ($reportInfo->isSetAcknowledgedDate()) {
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AcknowledgedDate<br />"); }
					if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $reportInfo->getAcknowledgedDate()->format(DATE_FORMAT) . "<br />"); }
					$acknowledge_date = $reportInfo->getAcknowledgedDate()->format(DATE_FORMAT);
				}
				$item['acknowledge_date'] = $acknowledge_date;
				if($report_type == '_GET_ORDERS_DATA_') {
					if(!empty($report_id)) {
						$parameters = array (
							'Marketplace' => MARKETPLACE_ID,
							'Merchant' => MERCHANT_ID,
							'Report' => @fopen('php://memory', 'rw+'),
							'ReportId' => $report_id,
						);
						$report = new MarketplaceWebService_Model_GetReportRequest($parameters);
						GetOrderToProcess($service, $report, $item);
					}
					$orders[] = $item;
				}
			}
		}
		if ($response->isSetResponseMetadata()) {
			if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ResponseMetadata<br />"); }
			$responseMetadata = $response->getResponseMetadata();
			if ($responseMetadata->isSetRequestId()) {
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RequestId<br />"); }
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $responseMetadata->getRequestId() . "<br />"); }
			}
		}
	} catch (MarketplaceWebService_Exception $ex) {
		echo("Caught Exception: " . $ex->getMessage() . "<br />");
		echo("Response Status Code: " . $ex->getStatusCode() . "<br />");
		echo("Error Code: " . $ex->getErrorCode() . "<br />");
		echo("Error Type: " . $ex->getErrorType() . "<br />");
		echo("Request ID: " . $ex->getRequestId() . "<br />");
		echo("XML: " . $ex->getXML() . "<br />");
	}

	return $orders;
}


function GetOrderToProcess(MarketplaceWebService_Interface $service, $request, &$order_item, $params = array()) {
	$debug = isset($params['debug']) ? $params['debug'] : false;

	try {
		$response = $service->getReport($request);


		if ($debug) { echo ("Service Response<br />"); }
		if ($debug) { echo ("=============================================================================<br />"); }

		if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GetReportResponse<br />"); }

		$order_item['report_result'] = '';
		if ($response->isSetGetReportResult()) {
			$getReportResult = $response->getGetReportResult();
			if ($debug) { echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GetReport"); }

			if ($getReportResult->isSetContentMd5()) {
				if ($debug) { echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ContentMd5"); }
				if ($debug) { echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $getReportResult->getContentMd5() . "<br />"); }
				$order_item['report_result'] = $getReportResult->getContentMd5();
			}
		}
		$order_item['response_metadata'] = '';
		if ($response->isSetResponseMetadata()) {
			if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ResponseMetadata<br />"); }
			$responseMetadata = $response->getResponseMetadata();
			if ($responseMetadata->isSetRequestId()) {
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RequestId<br />"); }
				if ($debug) { echo("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $responseMetadata->getRequestId() . "<br />"); }
				$order_item['response_metadata'] = $responseMetadata->getRequestId();
			}
		}

		$order_item['report'] = stream_get_contents($request->getReport());
		if ($debug) { echo ("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Report Contents<br />"); }
		if ($debug) { echo (stream_get_contents($request->getReport()) . "<br />"); }

	} catch (MarketplaceWebService_Exception $ex) {
		echo("Caught Exception: " . $ex->getMessage() . "<br />");
		echo("Response Status Code: " . $ex->getStatusCode() . "<br />");
		echo("Error Code: " . $ex->getErrorCode() . "<br />");
		echo("Error Type: " . $ex->getErrorType() . "<br />");
		echo("Request ID: " . $ex->getRequestId() . "<br />");
		echo("XML: " . $ex->getXML() . "<br />");
	}
}

function invokeRequestReport(MarketplaceWebService_Interface $service, $request)
{
    try {
        $response = $service->requestReport($request);

        echo ("Service Response\n");
        echo ("=============================================================================\n");

        echo("        RequestReportResponse\n");
        if ($response->isSetRequestReportResult()) {
            echo("            RequestReportResult\n");
            $requestReportResult = $response->getRequestReportResult();

            if ($requestReportResult->isSetReportRequestInfo()) {

                $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                echo("                ReportRequestInfo\n");
                if ($reportRequestInfo->isSetReportRequestId())
                {
                    echo("                    ReportRequestId\n");
                    echo("                        " . $reportRequestInfo->getReportRequestId() . "\n");
                }
                $report_request_id = $reportRequestInfo->getReportRequestId();
                $report_type = '';
                if ($reportRequestInfo->isSetReportType())
                {
                    echo("                    ReportType\n");
                    echo("                        " . $reportRequestInfo->getReportType() . "\n");
                    $report_type = $reportRequestInfo->getReportType();

                }
                if ($reportRequestInfo->isSetStartDate())
                {
                    echo("                    StartDate\n");
                    echo("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                }
                if ($reportRequestInfo->isSetEndDate())
                {
                    echo("                    EndDate\n");
                    echo("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                }
                if ($reportRequestInfo->isSetSubmittedDate())
                {
                    echo("                    SubmittedDate\n");
                    echo("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                }
                if ($reportRequestInfo->isSetReportProcessingStatus())
                {
                    echo("                    ReportProcessingStatus\n");
                    echo("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                }
//                if($report_type == '_GET_MERCHANT_LISTINGS_DATA_') {
//                    if(!empty($report_request_id)) {
//                        $parameters = array (
//                            'Marketplace' => MARKETPLACE_ID,
//                            'Merchant' => MERCHANT_ID,
////                            'Report' => @fopen('php://memory', 'rw+'),
//                            'ReportRequestIdList' => $report_request_id,
//                        );
//                        $report = new MarketplaceWebService_Model_GetReportRequestListRequest($parameters);
//                        print_r($report);
//                    }
//                }
            }
        }
        if ($response->isSetResponseMetadata()) {
            echo("            ResponseMetadata\n");
            $responseMetadata = $response->getResponseMetadata();
            if ($responseMetadata->isSetRequestId())
            {
                echo("                RequestId\n");
                echo("                    " . $responseMetadata->getRequestId() . "\n");
            }
        }

    } catch (MarketplaceWebService_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
    }
}

function invokeGetReportRequestList(MarketplaceWebService_Interface $service, $request)
{
    try {
        $response = $service->getReportRequestList($request);

        echo ("Service Response\n");
        echo ("=============================================================================\n");

        echo("        GetReportRequestListResponse\n");
        if ($response->isSetGetReportRequestListResult()) {
            echo("            GetReportRequestListResult\n");
            $getReportRequestListResult = $response->getGetReportRequestListResult();
            if ($getReportRequestListResult->isSetNextToken())
            {
                echo("                NextToken\n");
                echo("                    " . $getReportRequestListResult->getNextToken() . "\n");
            }
            if ($getReportRequestListResult->isSetHasNext())
            {
                echo("                HasNext\n");
                echo("                    " . $getReportRequestListResult->getHasNext() . "\n");
            }
            $reportRequestInfoList = $getReportRequestListResult->getReportRequestInfoList();
            foreach ($reportRequestInfoList as $reportRequestInfo) {
                echo("                ReportRequestInfo\n");
                if ($reportRequestInfo->isSetReportRequestId())
                {
                    echo("                    ReportRequestId\n");
                    echo("                        " . $reportRequestInfo->getReportRequestId() . "\n");
                }
                if ($reportRequestInfo->isSetReportType())
                {
                    echo("                    ReportType\n");
                    echo("                        " . $reportRequestInfo->getReportType() . "\n");
                }
                if ($reportRequestInfo->isSetStartDate())
                {
                    echo("                    StartDate\n");
                    echo("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                }
                if ($reportRequestInfo->isSetEndDate())
                {
                    echo("                    EndDate\n");
                    echo("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                }
                if ($reportRequestInfo->isSetSubmittedDate())
                {
                    echo("                    SubmittedDate\n");
                    echo("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                }
                if ($reportRequestInfo->isSetReportProcessingStatus())
                {
                    echo("                    ReportProcessingStatus\n");
                    echo("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                }
            }
        }
        if ($response->isSetResponseMetadata()) {
            echo("            ResponseMetadata\n");
            $responseMetadata = $response->getResponseMetadata();
            if ($responseMetadata->isSetRequestId())
            {
                echo("                RequestId\n");
                echo("                    " . $responseMetadata->getRequestId() . "\n");
            }
        }

    } catch (MarketplaceWebService_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
    }
}

function objectsIntoArray($arrObjData, $arrSkipIndices = array()) {
	$arrData = array();

	// if input is object, convert into array
	if (is_object($arrObjData)) {
		$arrObjData = get_object_vars($arrObjData);
	}

	if (is_array($arrObjData)) {
		foreach ($arrObjData as $index => $value) {
			if (is_object($value) || is_array($value)) {
				$value = objectsIntoArray($value, $arrSkipIndices); // recursive call
			}
			if (in_array($index, $arrSkipIndices)) {
				continue;
			}
			$arrData[$index] = $value;
		}
	}
	return $arrData;
}

?>