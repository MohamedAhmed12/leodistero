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
        // $rateRequest->RequestedShipment->Shipper->Address->StreetLines = ['10 Fed Ex Pkwy'];
        $rateRequest->RequestedShipment->Shipper->Address->City = $data['from']['name'];
        // $rateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = 'TN';
        // $rateRequest->RequestedShipment->Shipper->Address->PostalCode = 38115;
        $rateRequest->RequestedShipment->Shipper->Address->CountryCode = $data['from']['code'];

        //recipient
        // $rateRequest->RequestedShipment->Recipient->Address->StreetLines = ['13450 Farmcrest Ct'];
        $rateRequest->RequestedShipment->Recipient->Address->City = $data['to']['name'];
        // $rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = 'VA';
        // $rateRequest->RequestedShipment->Recipient->Address->PostalCode = 20171;
        $rateRequest->RequestedShipment->Recipient->Address->CountryCode = $data['to']['code'];

        //shipping charges payment
        $rateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;

        //rate request types
        $rateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_PREFERRED, SimpleType\RateRequestType::_LIST];

        $rateRequest->RequestedShipment->PackageCount = 1;

        //create package line items
        $rateRequest->RequestedShipment->RequestedPackageLineItems = [new ComplexType\RequestedPackageLineItem()];

        //package 1
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Value = $data['weight'];
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Units = SimpleType\WeightUnits::_LB;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Length = $data['length'];;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Width = $data['width'];;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Height = $data['height'];;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Units = SimpleType\LinearUnits::_IN;
        $rateRequest->RequestedShipment->RequestedPackageLineItems[0]->GroupPackageCount = 1;

        $rateServiceRequest = new Request();
        //$rateServiceRequest->getSoapClient()->__setLocation(Request::PRODUCTION_URL); //use production URL

        $rateReply = $rateServiceRequest->getGetRatesReply($rateRequest); // send true as the 2nd argument to return the SoapClient's stdClass response.


        // if (!empty($rateReply->RateReplyDetails)) {
        //     foreach ($rateReply->RateReplyDetails as $rateReplyDetail) {
        //         var_dump($rateReplyDetail->ServiceType);
        //         if (!empty($rateReplyDetail->RatedShipmentDetails)) {
        //             foreach ($rateReplyDetail->RatedShipmentDetails as $ratedShipmentDetail) {
        //                 var_dump($ratedShipmentDetail->ShipmentRateDetail->RateType . ": " . $ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount);
        //             }
        //         }
        //         echo "<hr />";
        //     }
        // }

        return $rateReply->RateReplyDetails[0]->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetFedExCharge->toArray();
        // echo $json_result;
        // $rateReply = json_decode($rateReply);
        // return response()->json((array)$rateReply);
        // return json_encode($rateReply->RateReplyDetails);
    }
}
