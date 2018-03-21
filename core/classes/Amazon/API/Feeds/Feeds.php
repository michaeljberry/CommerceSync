<?php

namespace Amazon\API\Feeds;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation, APIProperties};

class Feeds
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "Feeds";
    protected static $versionDate = "2009-01-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_Overview.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/feeds/Feeds_ClientLibraries.html";
    protected static $dataTypes = [
        "FeedSubmissionInfo" => [
            "FeedSubmissionId",
            "FeedType" => [
                "format" => "FeedType"
            ],
            "SubmittedDate" => [
                "format" => "date"
            ],
            "FeedProcessingStatus" => [
                "format" => "FeedProcessingStatus"
            ],
            "StartedProcessingDate" => [
                "format" => "date"
            ],
            "CompletedProcessingDate" => [
                "format" => "date"
            ]
        ],
        "FeedProcessingStatus" => [
            "validWith" => [
                "_AWAITING_ASYNCHRONOUS_REPLY_",
                "_CANCELLED_",
                "_DONE_",
                "_IN_PROGRESS_",
                "_IN_SAFETY_NET_",
                "_SUBMITTED_",
                "_UNCONFIRMED_"
            ]
        ],
        "FeedType" => [
            "validWith" => [
                "POST_PRODUCT_DATA",
                "_POST_INVENTORY_AVAILABILITY_DATA_",
                "_POST_PRODUCT_OVERRIDES_DATA_",
                "_POST_PRODUCT_PRICING_DATA_",
                "_POST_PRODUCT_IMAGE_DATA_",
                "_POST_PRODUCT_RELATIONSHIP_DATA_",
                "_POST_FLAT_FILE_INVLOADER_DATA_",
                "_POST_FLAT_FILE_LISTINGS_DATA_",
                "_POST_FLAT_FILE_BOOKLOADER_DATA_",
                "_POST_FLAT_FILE_CONVERGENCE_LISTINGS_DATA_",
                "_POST_FLAT_FILE_LISTINGS_DATA_",
                "_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_",
                "_POST_UIEE_BOOKLOADER_DATA_",
                "_POST_STD_ACES_DATA_",
                "_POST_ORDER_ACKNOWLEDGEMENT_DATA_",
                "_POST_PAYMENT_ADJUSTMENT_DATA_",
                "_POST_ORDER_FULFILLMENT_DATA_",
                "_POST_INVOICE_CONFIRMATION_DATA_",
                "_POST_FLAT_FILE_ORDER_ACKNOWLEDGEMENT_DATA_",
                "_POST_FLAT_FILE_PAYMENT_ADJUSTMENT_DATA_",
                "_POST_FLAT_FILE_FULFILLMENT_DATA_",
                "_POST_FLAT_FILE_INVOICE_CONFIRMATION_DATA_",
                "_POST_FULFILLMENT_ORDER_REQUEST_DATA_",
                "_POST_FULFILLMENT_ORDER_CANCELLATION_REQUEST_DATA_",
                "_POST_FBA_INBOUND_CARTON_CONTENTS_",
                "_POST_FLAT_FILE_FULFILLMENT_ORDER_REQUEST_DATA_",
                "_POST_FLAT_FILE_FULFILLMENT_ORDER_CANCELLATION_REQUEST_DATA_",
                "_POST_FLAT_FILE_FBA_CREATE_INBOUND_PLAN_",
                "_POST_FLAT_FILE_FBA_UPDATE_INBOUND_PLAN_",
                "_POST_FLAT_FILE_FBA_CREATE_REMOVAL_",
                "_POST_ENHANCED_CONTENT_DATA_"
            ]
        ]
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }
}