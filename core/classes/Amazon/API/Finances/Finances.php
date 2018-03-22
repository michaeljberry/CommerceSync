<?php

namespace Amazon\API\Finances;

use Amazon\API\{APIMethods, APIParameters, APIParameterValidation, APIProperties};

class Finances
{

    use APIMethods;
    use APIParameters;
    use APIProperties;
    use APIParameterValidation;

    protected static $feedType = "";
    protected static $feedContent = "";
    protected static $feed = "Finances";
    protected static $versionDate = "2015-05-01";
    private static $overviewUrl = "http://docs.developer.amazonservices.com/en_US/fba_inventory/MWS_GetServiceStatus.html";
    private static $libraryUpdateUrl = "http://docs.developer.amazonservices.com/en_US/finances/Finances_ClientLibraries.html";
    protected static $dataTypes = [
        "AdjustmentEvent" => [
            "AdjustmentType" => [
                "validWith" => [
                    "FBAInventoryReimbursement",
                    "ReserveEvent",
                    "PostageBilling",
                    "PostageRefund",
                    "LostOrDamagedReimbursement",
                    "CanceledButPickedUpReimbursement",
                    "ReimbursementClawback"
                ]
            ],
            "AdjustmentAmount" => [
                "format" => "CurrencyAmount"
            ],
            "AdjustmentItemList" => [
                "format" => "AdjustmentItem"
            ],
            "PostedDate" => [
                "format" => "date"
            ]
        ],
        "AdjustmentItem" => [
            "Quantity",
            "PerUnitAmount" => [
                "format" => "CurrencyAmount"
            ],
            "TotalAmount" => [
                "format" => "Currency"
            ],
            "SellerSKU",
            "FnSKU",
            "ProductDescription",
            "ASIN"
        ],
        "ChargeComponent" => [
            "ChargeType" => [
                "validWith" => [
                    "Principal",
                    "Tax",
                    "MarketplaceFacilitatorTax-Principal",
                    "MarketplaceFacilitatorTax-Shipping",
                    "MarketplaceFacilitatorTax-Giftwrap",
                    "MarketplaceFacilitatorTax-Other",
                    "Discount",
                    "TaxDiscount",
                    "CODItemCharge",
                    "CODItemTaxCharge",
                    "CODOrderCharge",
                    "CODOrderTaxCharge",
                    "CODShippingCharge",
                    "CODShippingTaxCharge",
                    "ShippingCharge",
                    "ShippingTax",
                    "Goodwill",
                    "Giftwrap",
                    "GiftwrapTax",
                    "RestockingFee",
                    "ReturnShipping",
                    "PointsFee",
                    "GenericDeduction",
                    "FreeReplacementREturnShipping",
                    "PaymentMethodFee",
                    "ExportCharge",
                    "SAFE-TReimbursement",
                    "TCS-CGST",
                    "TCS-SGST",
                    "TCS-IGST",
                    "TCS-UTGST"
                ]
            ],
            "ChargeAmount" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "ChargeInstrument" => [
            "Description",
            "Tail",
            "Amount" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "CouponPaymentEvent" => [
            "PostedDate" => [
                "format" => "date"
            ],
            "CouponId",
            "SellerCouponDescription",
            "ClipOrRedemptionCount",
            "PaymentEventId",
            "FeeComponent" => [
                "format" => "FeeComponent"
            ],
            "ChargeComponent" => [
                "format" => "ChargeComponent"
            ],
            "TotalAmount" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "CurrencyAmount" => [
            "CurrencyCode" => [
                "length" => 3
            ],
            "CurrencyAmount"
        ],
        "DebtRecoveryEvent" => [
            "DebtRecoveryType" => [
                "validWith" => [
                    "DebtPayment",
                    "DebtPaymentFailure",
                    "DebtAdjustment"
                ]
            ],
            "RecoveryAmount" => [
                "format" => "CurrencyAmount"
            ],
            "OverPaymentCredit" => [
                "format" => "CurrencyAmount"
            ],
            "DebtRecoveryItemList" => [
                "format" => "DebtRecoveryItem"
            ],
            "ChargeInstrumentList" => [
                "format" => "ChargeInstrument"
            ]
        ],
        "DebtRecoveryItem" => [
            "RecoveryAmount" => [
                "format" => "CurrencyAmount"
            ],
            "OriginalAmount" => [
                "format" => "CurrencyAmount"
            ],
            "GroupBeginDate" => [
                "format" => "date"
            ],
            "GroupEndDate" => [
                "format" => "date"
            ]
        ],
        "DirectPayment" => [
            "DirectPaymentType" => [
                "StoredValueCardRevenue",
                "StoredValueCardRefund",
                "PrivateLabelCreditCardRevenue",
                "PrivateLabelCreditCardRefund",
                "CollectOnDeliveryRevenue",
                "CollectOnDeliveryRefund"
            ],
            "DirectPaymentAmount" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "FBALiquidationEvent" => [
            "PostedDate",
            "OriginalRemovalOrderId",
            "LiquidationProceedsAmount" => [
                "format" => "CurrencyAmount"
            ],
            "LiquidationFeeAmount" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "FeeComponent" => [
            "FeeType" => [
                "format" => "FeeTypes"
            ],
            "FeeAmount" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "FeeTypes" => [
            "Commisssion",
            "CouponClipFee",
            "CouponRedemptionFee",
            "FixedClosingFee",
            "FreshInboundTransportationFee",
            "HighVolumeListingFee",
            "ImagingServicesFee",
            "MFNPostageFee",
            "ReferralFee",
            "RefundCommission",
            "SalesTaxCollectionFee",
            "Subscription",
            "TextbookRentalBuyoutFee",
            "TextbookRentalExtensionFee",
            "TextbookRentalServiceFee",
            "VariableClosingFee",
            "BubblewrapFee",
            "FBACustomerReturnPerOrderFee",
            "FBACustomerReturnPerUnitFee",
            "FBACustomerReturnWeightBasedFee",
            "FBADisposalFee",
            "FBAFulfillmentCODFee",
            "FBAInboundConvenienceFee",
            "FBAInboundTransportationFee",
            "FBAInboundTransportationProgramFee",
            "FBALongTermStorageFee",
            "FBAPerOrderFulfillmentFee",
            "FBAPerUnitFulfillmentFee",
            "FBARemovalFee",
            "FBAStorageFee",
            "FBATransportationFee",
            "FBAWeightBasedFee",
            "FulfillmentFee",
            "FulfillmentNetworkFee",
            "LabelingFee",
            "OpaqueBaggingFee",
            "PolybaggingFee",
            "SSOFFulfillmentFee",
            "TapingFee",
            "TransportationFee",
            "UnitFulfillmentFee"
        ],
        "FinancialEventGroup" => [
            "FinancialEventGroupId",
            "ProcessingStatus" => [
                "validWith" => [
                    "Open",
                    "Closed"
                ]
            ],
            "FundTransferStatus",
            "OriginalTotal" => [
                "format" => "CurrencyAmount"
            ],
            "ConvertedTotal" => [
                "format" => "CurrencyAmount"
            ],
            "FundTransferDate" => [
                "format" => "date"
            ],
            "TraceId",
            "AccountTail",
            "BeginningBalance" => [
                "format" => "CurrencyAmount"
            ],
            "FinancialEventGroupStart" => [
                "format" => "date"
            ],
            "FinancialEventGroupEnd" => [
                "format" => "date"
            ]
        ],
        "FinancialEvents" => [
            "ShipmentEventList" => [
                "format" => "ShipmentEvent"
            ],
            "RefundEventList" => [
                "format" => "ShipmentEvent"
            ],
            "GuaranteeClaimEventList" => [
                "format" => "ShipmentEvent"
            ],
            "ChargebackEventList" => [
                "format" => "ShipmentEvent"
            ],
            "PayWithAmazonEventList" => [
                "format" => "PayWithAmazonEvent"
            ],
            "ServiceProviderCreditEventList" => [
                "format" => "SolutionProviderCreditEvent"
            ],
            "RetrochargeEventList" => [
                "format" => "RetrochargeEvent"
            ],
            "RentalTransactionEventList" => [
                "format" => "RentalTransactionEvent"
            ],
            "PerformanceBondRefundEventList" => [
                "format" => "PerformanceBondRefundEvent"
            ],
            "ProductAdsPaymentEventList" => [
                "format" => "ProductAdsPaymentEvent"
            ],
            "ServiceFeeEventList" => [
                "format" => "ServiceFeeEvent"
            ],
            "DebtRecoveryEventList" => [
                "format" => "DebtRecoveryEvent"
            ],
            "LoanServicingEventList" => [
                "format" => "LoanServicingEvent"
            ],
            "AdjustmentEventList" => [
                "format" => "AdjustmentEvent"
            ],
            "CouponPaymentEventList" => [
                "format" => "CouponPaymentEvent"
            ],
            "SAFETReimbursementEventList" => [
                "format" => "SAFETReimbursementEvent"
            ],
            "SellerReviewEnrollmentPaymentEventList" => [
                "format" => "SellerReviewEnrollmentPaymentEvent"
            ],
            "FBALiquidationEventList" => [
                "format" => "FBALiquidationEvent"
            ],
            "ImagingServicesFeeEventList" => [
                "format" => "ImagingServicesFeeEvent"
            ]
        ],
        "ImagingServicesFeeEvent" => [
            "ImagingRequestBillingItemID",
            "ASIN",
            "PostedDate",
            "FeeList" => [
                "format" => "FeeComponent"
            ]
        ],
        "LoanServicingEvent" => [
            "LoanAmount" => [
                "format" => "CurrencyAmount"
            ],
            "SourceBusinessEventType" => [
                "validWith" => [
                    "LoanAdvance",
                    "LoanPayment",
                    "LoanRefund"
                ]
            ]
        ],
        "PayWithAmazonEvent" => [
            "SellerOrderId",
            "TranactionPostedDate" => [
                "format" => "date"
            ],
            "BusinessObjectType" => [
                "validWith" => [
                    "PaymentContract"
                ]
            ],
            "SalesChannel",
            "Charge" => [
                "format" => "ChargeComponent"
            ],
            "FeeList" => [
                "format" => "FeeComponent"
            ],
            "PaymentAmountType" => [
                "validWith" => [
                    "Sales"
                ]
            ],
            "AmountDescription",
            "FulfillmentChannel" => [
                "validWith" => [
                    "AFN",
                    "MFN"
                ]
            ],
            "StoreName"
        ],
        "PerformanceBondRefundEvent" => [
            "MarketplaceCOuntryCode" => [
                "length" => 2
            ],
            "Amount" => [
                "format" => "CurrencyAmount"
            ],
            "ProductGroupList"
        ],
        "ProductAdsPaymentEvent" => [
            "postedDate",
            "transactionType" => [
                "validWith" => [
                    "charge",
                    "refund"
                ]
            ],
            "invoiceId",
            "baseValue" => [
                "format" => "CurrencyAmount"
            ],
            "taxValue" => [
                "format" => "CurrencyAmount"
            ],
            "transactionValue" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "Promotion" => [
            "PromotionType",
            "PromotionId",
            "PromotionAmount" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "RentalTransactionEvent" => [
            "AmazonOrderId",
            "RentalEventType" => [
                "validWith" => [
                    "RentalCustomerPayment-Buyout",
                    "RentalCustomerPayment-Extension",
                    "RentalCustomerRefund-Buyout",
                    "RentalCustomerRefund-Extension",
                    "RentalHandlingFee",
                    "RentalChargeFailureReimbursement",
                    "RentalLostItemReimbursement"
                ]
            ],
            "ExtensionLength",
            "PosedDate" => [
                "format" => "date"
            ],
            "RentalChargeList" => [
                "format" => "ChargeComponent"
            ],
            "RentalFeeList" => [
                "format" => "FeeComponent"
            ],
            "MarketplaceName",
            "RentalInitialValue" => [
                "format" => "CurrencyAmount"
            ],
            "RentalReimbursement" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "RetrochargeEvent" => [
            "RetrochargeEventType" => [
                "validWith" => [
                    "Retrocharge",
                    "RetrochargeReversal"
                ]
            ],
            "AmazonOrderId",
            "PostedDate" => [
                "format" => "date"
            ],
            "BaseTax" => [
                "format" => "CurrencyAmount"
            ],
            "ShippingTax" => [
                "format" => "CurrencyAmount"
            ],
            "MarketplaceName"
        ],
        "SAFETReimbursementEvent" => [
            "PostedDate" => [
                "format" => "date"
            ],
            "SAFETClaimId",
            "ReimbursedAmount" => [
                "format" => "CurrencyAmount"
            ],
            "SAFETReimbursementItemList" => [
                "format" => "SAFETReimbursementItem"
            ]
        ],
        "SAFETReimbursementItem" => [
            "ItemChargeList" => [
                "format" => "ChargeComponent"
            ]
        ],
        "SellerReviewEnrollmentPaymentEvent" => [
            "PostedDate" => [
                "format" => "date"
            ],
            "EnrollmentId",
            "ParentASIN",
            "FeeComponent" => [
                "format" => "FeeComponent"
            ],
            "ChargeComponent" => [
                "format" => "ChargeComponent"
            ],
            "TotalAmount" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "ServiceFeeEvent" => [
            "AmazonOrderId",
            "FeeReason",
            "FeeList" => [
                "format" => "FeeComponent"
            ],
            "SellerSKU",
            "FnSKU",
            "FeeDescription",
            "ASIN"
        ],
        "ShipmentEvent" => [
            "AmazonOrderId",
            "SellerOrderId",
            "MarketplaceName",
            "OrderChargeList" => [
                "format" => "ChargeComponent"
            ],
            "OrderChargeAdjustmentList" => [
                "format" => "ChargeComponent"
            ],
            "ShipmentFeeList" => [
                "format" => "FeeComponent"
            ],
            "ShipmentFeeAdjustmentList" => [
                "format" => "FeeComponent"
            ],
            "OrderFeeList" => [
                "format" => "FeeComponent"
            ],
            "OrderFeeAdjustmentList" => [
                "format" => "FeeComponent"
            ],
            "DriectPaymentList" => [
                "format" => "DirectPayment"
            ],
            "PostedDate" => [
                "format" => "date"
            ],
            "ShipmentItemList" => [
                "format" => "ShipmentItem"
            ],
            "ShipmentItemAdjustmentList" => [
                "format" => "ShipmentItem"
            ]
        ],
        "ShipmentItem" => [
            "SellerSKU",
            "OrderItemId",
            "OrderAdjustmentItemId",
            "QuantityShipped",
            "ItemChargeList" => [
                "format" => "ChargeComponent"
            ],
            "ItemTaxWithheldList" => [
                "format" => "TaxWithheldComponent"
            ],
            "ItemChargeAdjustmentList" => [
                "format" => "ChargeComponent"
            ],
            "ItemFeeList" => [
                "format" => "FeeComponent"
            ],
            "ItemFeeAdjustmentList" => [
                "format" => "FeeComponent"
            ],
            "PromotionList" => [
                "format" => "Promotion"
            ],
            "PromotionAdjustmentList" => [
                "format" => "Promotion"
            ],
            "CostOfPointsGranted" => [
                "format" => "CurrencyAmount"
            ],
            "CostOfPointsReturned" => [
                "format" => "CurrencyAmount"
            ]
        ],
        "SolutionProviderCreditEvent" => [
            "ProviderTransactionType" => [
                "validWith" => [
                    "ProviderCredit",
                    "ProviderCreditReversal"
                ]
            ],
            "SellerOrderId",
            "MarketplaceId",
            "MarketplaceCountryCode",
            "SellerId",
            "SellerStoreName",
            "ProviderId",
            "ProviderStoreName"
        ],
        "TaxWithheldComponent" => [
            "TaxCollectionModel" => [
                "validWith" => [
                    "MarketplaceFacilitator"
                ]
            ],
            "TaxesWithheld" => [
                "format" => "ChargeComponent",
                "validWith" => [
                    "MarketplaceFacilitatorTax-Principal",
                    "MarketplaceFacilitatorTax-Shipping",
                    "MarketplaceFacilitatorTax-GiftWrap",
                    "MarketplaceFacilitatorTax-Other"
                ]
            ]
        ]
    ];

    public function __construct($parametersToSet = null)
    {

        static::setParameters($parametersToSet);

        static::verifyParameters();

    }

}