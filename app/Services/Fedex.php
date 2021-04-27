<?php

namespace App\Services;

use App\Interfaces\ShippingAdapterInterface;
use FedEx\RateService\Request;
use FedEx\RateService\ComplexType;
use FedEx\RateService\SimpleType;

class Fedex implements ShippingAdapterInterface
{
    public function calculateRate($data)
    {        
        $rateRequest = new ComplexType\RateRequest();
        
        //authentication & client details
        $rateRequest->WebAuthenticationDetail->UserCredential->Key = env('FEDEX_KEY');
        $rateRequest->WebAuthenticationDetail->UserCredential->Password = env('FEDEX_PASSWORD');
        $rateRequest->ClientDetail->AccountNumber = env('FEDEX_ACCOUNT_NUMBER');
        $rateRequest->ClientDetail->MeterNumber = env('FEDEX_METER_NUMBER');
        
        $rateRequest->TransactionDetail->CustomerTransactionId = 'testing rate service request';
        
        //version
        $rateRequest->Version->ServiceId = 'crs';
        $rateRequest->Version->Major = 28;
        $rateRequest->Version->Minor = 0;
        $rateRequest->Version->Intermediate = 0;
        
        $rateRequest->ReturnTransitAndCommit = true;
        
        //shipper
        $rateRequest->RequestedShipment->PreferredCurrency = 'USD';
        $rateRequest->RequestedShipment->Shipper->Address->StreetLines = ['10 Fed Ex Pkwy'];
        $rateRequest->RequestedShipment->Shipper->Address->City = 'Memphis';
        $rateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = 'TN';
        $rateRequest->RequestedShipment->Shipper->Address->PostalCode = 38115;
        $rateRequest->RequestedShipment->Shipper->Address->CountryCode = 'US';
        
        //recipient
        $rateRequest->RequestedShipment->Recipient->Address->StreetLines = ['13450 Farmcrest Ct'];
        $rateRequest->RequestedShipment->Recipient->Address->City = 'Herndon';
        $rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = 'VA';
        $rateRequest->RequestedShipment->Recipient->Address->PostalCode = 20171;
        $rateRequest->RequestedShipment->Recipient->Address->CountryCode = 'US';
        
        //shipping charges payment
        $rateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;
        
        //rate request types
        $rateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_PREFERRED, SimpleType\RateRequestType::_LIST];
        
        $rateRequest->RequestedShipment->PackageCount = 2;
        
        //create package line items
        $rateRequest->RequestedShipment->RequestedPackageLineItems = [new ComplexType\RequestedPackageLineItem(), new ComplexType\RequestedPackageLineItem()];
        
        //package 1
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Value = 2;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Units = SimpleType\WeightUnits::_LB;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Length = 10;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Width = 10;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Height = 3;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Units = SimpleType\LinearUnits::_IN;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->GroupPackageCount = 1;
        
        $rateServiceRequest = new Request();
        //$rateServiceRequest->getSoapClient()->__setLocation(Request::PRODUCTION_URL); //use production URL
        
        $rateReply = $rateServiceRequest->getGetRatesReply($rateRequest); // send true as the 2nd argument to return the SoapClient's stdClass response.
        
        
        if (!empty($rateReply->RateReplyDetails)) {
            foreach ($rateReply->RateReplyDetails as $rateReplyDetail) {
                var_dump($rateReplyDetail->ServiceType);
                if (!empty($rateReplyDetail->RatedShipmentDetails)) {
                    foreach ($rateReplyDetail->RatedShipmentDetails as $ratedShipmentDetail) {
                        var_dump($ratedShipmentDetail->ShipmentRateDetail->RateType . ": " . $ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount);
                    }
                }
                echo "<hr />";
            }
        }
        
        return $rateReply;
    }
}
