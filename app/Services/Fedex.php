<?php

namespace App\Services;

use App\Http\Requests\CreateShipmentFormRequest;
use App\Interfaces\ShippingAdapterInterface;
use App\Models\Shipment;
use FedEx\RateService\Request;
use FedEx\RateService\ComplexType;
use FedEx\RateService\SimpleType;

use FedEx\OpenShipService\Request as OpenShipRequest;
use FedEx\OpenShipService\ComplexType as OpenShipComplexType;
use FedEx\OpenShipService\SimpleType as OpenShipSimpleType;

use FedEx\ShipService;
use FedEx\ShipService\ComplexType as ShipComplexType;
use FedEx\ShipService\SimpleType as ShipSimpleType;
use Illuminate\Support\Facades\Storage;
use stdClass;

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
        $rateRequest->RequestedShipment->Shipper->Address->City = $data['from']['capital']['name'];
        $rateRequest->RequestedShipment->Shipper->Address->PostalCode = $data['from']['capital']['postal_code'];
        $rateRequest->RequestedShipment->Shipper->Address->CountryCode = $data['from']['code'];

        // //recipient
        $rateRequest->RequestedShipment->Recipient->Address->City = $data['to']['capital']['name'];
        $rateRequest->RequestedShipment->Recipient->Address->PostalCode = $data['to']['capital']['postal_code'];
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

        $res = [];

        if (!empty($rateReply->RateReplyDetails)) {
            foreach ($rateReply->RateReplyDetails as $rateReplyDetail) {
                if (!empty($rateReplyDetail->RatedShipmentDetails)) {
                    $res[$rateReplyDetail->ServiceType] = array_map(
                        function ($ratedShipmentDetail) {
                            return $ratedShipmentDetail->ShipmentRateDetail->RateType . ": " . $ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount;
                        },
                        $rateReplyDetail->RatedShipmentDetails
                    );
                }
            }
        }

        $standard = new stdClass;
        if (isset($res['INTERNATIONAL_ECONOMY']) && str_contains($res['INTERNATIONAL_ECONOMY'][0], ' ')) {
            $standard->CurrencyCode = "USD";
            $standard->Value = intval(explode(': ', $res['INTERNATIONAL_ECONOMY'][0])[1]);
        }

        $express = new stdClass;
        if (isset($res['INTERNATIONAL_PRIORITY']) && str_contains($res['INTERNATIONAL_PRIORITY'][0], ' ')) {
            $express->CurrencyCode = "USD";
            $express->Value = intval(explode(': ', $res['INTERNATIONAL_PRIORITY'][0])[1]);
        }

        return [
            'standard' => $standard,
            'express' => $express
        ];
    }

    public function createShipment(object $data)
    {

        //     $userCredential = new ShipComplexType\WebAuthenticationCredential();
        //     $userCredential
        //         ->setKey(env('FEDEX_KEY'))
        //         ->setPassword(env('FEDEX_PASSWORD'));

        //     $webAuthenticationDetail = new ShipComplexType\WebAuthenticationDetail();
        //     $webAuthenticationDetail->setUserCredential($userCredential);

        //     $clientDetail = new ShipComplexType\ClientDetail();
        //     $clientDetail
        //         ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
        //         ->setMeterNumber(env('FEDEX_METER_NUMBER'));

        //     $version = new ShipComplexType\VersionId();
        //     $version
        //         ->setMajor(26)
        //         ->setIntermediate(0)
        //         ->setMinor(0)
        //         ->setServiceId('ship');
        //     // dd($data);
        //     $shipperAddress = new ShipComplexType\Address();
        //     $shipperAddress
        //     ->setStreetLines(['Address Line 1'])
        // ->setCity('Austin')
        // ->setStateOrProvinceCode('TX')
        // ->setPostalCode('73301')
        // ->setCountryCode('US');
        //     // ->setStreetLines(['Address Line 1'])
        //         // ->setCity('Dubai')
        //         // ->setStateOrProvinceCode('')
        //         // ->setPostalCode('00000')
        //         // ->setCountryCode('AE');

        //     $shipperContact = new ShipComplexType\Contact();
        //     $shipperContact
        //         ->setCompanyName('Company Name')
        //         ->setEMailAddress('test@example.com')
        //         ->setPersonName('Person Name')
        //         ->setPhoneNumber(('123-123-1234'));

        //     $shipper = new ShipComplexType\Party();
        //     $shipper
        //         ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
        //         ->setAddress($shipperAddress)
        //         ->setContact($shipperContact);

        //     $recipientAddress = new ShipComplexType\Address();
        //     $recipientAddress
        //     ->setStreetLines(['Address Line 1'])
        //     ->setCity('Herndon')
        //     ->setStateOrProvinceCode('VA')
        //     ->setPostalCode('20171')
        //     ->setCountryCode('US');
        //     // ->setStreetLines(['Address Line 1'])
        //         // ->setCity('Cairo')
        //         // ->setStateOrProvinceCode('CA')
        //         // ->setPostalCode('11865')
        //         // ->setCountryCode('EG');

        //         // ->setStreetLines(['Address Line 1'])
        //     // ->setCity('Austin')
        //     // ->setStateOrProvinceCode('TX')
        //     // ->setPostalCode('73301')
        //     // ->setCountryCode('US');

        //     $recipientContact = new ShipComplexType\Contact();
        //     $recipientContact
        //         ->setPersonName('Contact Name')
        //         ->setPhoneNumber('1234567890');

        //     $recipient = new ShipComplexType\Party();
        //     $recipient
        //         ->setAddress($recipientAddress)
        //         ->setContact($recipientContact);

        //     $labelSpecification = new ShipComplexType\LabelSpecification();
        //     $labelSpecification
        //         ->setLabelStockType(new ShipSimpleType\LabelStockType(ShipSimpleType\LabelStockType::_PAPER_7X4POINT75))
        //         ->setImageType(new ShipSimpleType\ShippingDocumentImageType(ShipSimpleType\ShippingDocumentImageType::_PDF))
        //         ->setLabelFormatType(new ShipSimpleType\LabelFormatType(ShipSimpleType\LabelFormatType::_COMMON2D));

        //     $packageLineItem1 = new ShipComplexType\RequestedPackageLineItem();
        //     $packageLineItem1
        //         ->setSequenceNumber(1)
        //         ->setItemDescription('Product description')
        //         ->setDimensions(new ShipComplexType\Dimensions(array(
        //             'Width' => 10,
        //             'Height' => 10,
        //             'Length' => 25,
        //             'Units' => ShipSimpleType\LinearUnits::_IN
        //         )))
        //         ->setWeight(new ShipComplexType\Weight(array(
        //             'Value' => 2,
        //             'Units' => ShipSimpleType\WeightUnits::_LB
        //         )));


        //     $cus = new ShipComplexType\CustomsClearanceDetail();
        //     //  new ShipComplexType\CustomsClearanceDetail();
        //     $money = new ShipComplexType\Money();
        //     $money->setCurrency('USD');
        //     $money->setAmount('120');
        //     $cus->setCustomsValue($money);
        //     $commodity = new ShipComplexType\Commodity();
        //     $commodity->setNumberOfPieces(1);
        //     $commodity->setDescription('Test');
        //     $commodity->setCountryOfManufacture('EG');
        //     $commodity->setWeight(new ShipComplexType\Weight([
        //         'Units' => ShipSimpleType\WeightUnits::_LB,
        //         'Value' => 2
        //     ]));
        //     $commodity->setQuantity(1);
        //     $commodity->setQuantityUnits('EA');
        //     $commodity->setUnitPrice(new ShipComplexType\Money([
        //             'Currency' => 'USD',
        //             'Amount' => '120'
        //         ]));
        //     $cus->setCommodities($commodity->toArray());

        //     // $dutiesPayment = new ShipComplexType\Payment();
        //     // $dutiesPayment->setPaymentType(ShipSimpleType\PaymentType::_SENDER);
        //     // $cus->setDutiesPayment($dutiesPayment);
        //     // $cus->setDocumentContent(ShipSimpleType\InternationalDocumentContentType);
        //     // $cus->setDutiesPayment($money);
        //     // $CustomsClearanceDetail = [
        //     //     'DutiesPayment' => new ShipComplexType\Payment([
        //     //       'PaymentType' => ShipSimpleType\PaymentType::_SENDER, 
        //     //     ]),
        //     //     'DocumentContent' => ShipSimpleType\InternationalDocumentContentType::_NON_DOCUMENTS,
        //     //     'CustomsValue' => new ShipComplexType\Money([
        //     //       'Currency' => 'USD',
        //     //       'Amount' => $PriceExShipping
        //     //     ]),
        //     //     'Commodities' => $Commodities,
        //     //     'ExportDetail' => new ShipComplexType\ExportDetail([
        //     //       'ExportComplianceStatement' => '30.37(f)'
        //     //     ])
        //     //   ];

        //     $shippingChargesPayor = new ShipComplexType\Payor();
        //     $shippingChargesPayor->setResponsibleParty($shipper);

        //     $shippingChargesPayment = new ShipComplexType\Payment();
        //     $shippingChargesPayment
        //         ->setPaymentType(ShipSimpleType\PaymentType::_SENDER)
        //         ->setPayor($shippingChargesPayor);

        //     $requestedShipment = new ShipComplexType\RequestedShipment();
        //     $requestedShipment->setShipTimestamp(date('c'));
        //     $requestedShipment->setDropoffType(new ShipSimpleType\DropoffType(ShipSimpleType\DropoffType::_REGULAR_PICKUP));
        //     $requestedShipment->setServiceType(new ShipSimpleType\ServiceType(ShipSimpleType\ServiceType::_INTERNATIONAL_PRIORITY));
        //     $requestedShipment->setPackagingType(new ShipSimpleType\PackagingType(ShipSimpleType\PackagingType::_YOUR_PACKAGING));
        //     $requestedShipment->setShipper($shipper);
        //     $requestedShipment->setRecipient($recipient);
        //     $requestedShipment->setLabelSpecification($labelSpecification);
        //     $requestedShipment->setRateRequestTypes(array(new ShipSimpleType\RateRequestType(ShipSimpleType\RateRequestType::_PREFERRED)));
        //     $requestedShipment->setPackageCount(1);
        //     $requestedShipment->setRequestedPackageLineItems([
        //         $packageLineItem1
        //     ]);
        //     $requestedShipment->setShippingChargesPayment($shippingChargesPayment);
        //     $requestedShipment->setCustomsClearanceDetail($cus);

        //     $processShipmentRequest = new ShipComplexType\ProcessShipmentRequest();
        //     $processShipmentRequest->setWebAuthenticationDetail($webAuthenticationDetail);
        //     $processShipmentRequest->setClientDetail($clientDetail);
        //     $processShipmentRequest->setVersion($version);
        //     $processShipmentRequest->setRequestedShipment($requestedShipment);





        //     $shipService = new ShipService\Request();
        //     // //$shipService->getSoapClient()->__setLocation('https://ws.fedex.com:443/web-services/ship');
        //     $result = $shipService->getProcessShipmentReply($processShipmentRequest);
        //     dd($result);
        //     // // return response()->json($result);

        //     // // Save .pdf label    
        //     // // $labelPath = Storage::put('public/documents/label.pdf', $result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image);



















































        $userCredential = new ShipComplexType\WebAuthenticationCredential();
        $userCredential
            ->setKey(env('FEDEX_KEY'))
            ->setPassword(env('FEDEX_PASSWORD'));

        $webAuthenticationDetail = new ShipComplexType\WebAuthenticationDetail();
        $webAuthenticationDetail->setUserCredential($userCredential);

        $clientDetail = new ShipComplexType\ClientDetail();
        $clientDetail
            ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
            ->setMeterNumber(env('FEDEX_METER_NUMBER'));

        $version = new ShipComplexType\VersionId();
        $version
            ->setMajor(26)
            ->setIntermediate(0)
            ->setMinor(0)
            ->setServiceId('ship');

        $shipperAddress = new ShipComplexType\Address();
        $shipperAddress
            // ->setStreetLines(['Address Line 1'])
            // ->setCity('Austin')
            // ->setStateOrProvinceCode('TX')
            // ->setPostalCode('73301')
            // ->setCountryCode('US');

            ->setStreetLines(['Address Line 1'])
            ->setCity('Austin')
            ->setStateOrProvinceCode('TX')
            ->setPostalCode('73301')
            ->setCountryCode('US');

        $shipperContact = new ShipComplexType\Contact();
        $shipperContact
            ->setCompanyName('Company Name')
            ->setEMailAddress('test@example.com')
            ->setPersonName('Person Name')
            ->setPhoneNumber(('123-123-1234'));

        $shipper = new ShipComplexType\Party();
        $shipper
            ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
            ->setAddress($shipperAddress)
            ->setContact($shipperContact);

        $recipientAddress = new ShipComplexType\Address();
        $recipientAddress
            // ->setStreetLines(['Address Line 1'])
            // ->setCity('Dubai')
            // ->setStateOrProvinceCode('')
            // ->setPostalCode('00000')
            // ->setCountryCode('AE');  

            ->setStreetLines(['Address Line 1'])
            ->setCity('Richmond')
            ->setStateOrProvinceCode('BC')
            ->setPostalCode('V7C4V4')
            ->setCountryCode('CA');

        // ->setStreetLines(['Address Line 1'])
        //     ->setCity('Austin')
        //     ->setStateOrProvinceCode('TX')
        //     ->setPostalCode('73301')
        //     ->setCountryCode('US');
        $recipientContact = new ShipComplexType\Contact();
        $recipientContact
            ->setPersonName('Contact Name')
            ->setPhoneNumber('1234567890');

        $recipient = new ShipComplexType\Party();
        $recipient
            ->setAddress($recipientAddress)
            ->setContact($recipientContact);

        $labelSpecification = new ShipComplexType\LabelSpecification();
        $labelSpecification
            ->setLabelStockType(new ShipSimpleType\LabelStockType(ShipSimpleType\LabelStockType::_PAPER_7X4POINT75))
            ->setImageType(new ShipSimpleType\ShippingDocumentImageType(ShipSimpleType\ShippingDocumentImageType::_PDF))
            ->setLabelFormatType(new ShipSimpleType\LabelFormatType(ShipSimpleType\LabelFormatType::_COMMON2D));

        $packageLineItem1 = new ShipComplexType\RequestedPackageLineItem();
        $packageLineItem1
            ->setSequenceNumber(1)
            ->setItemDescription('Product description')
            ->setDimensions(new ShipComplexType\Dimensions(array(
                'Width' => 10,
                'Height' => 10,
                'Length' => 25,
                'Units' => ShipSimpleType\LinearUnits::_IN
            )))
            ->setWeight(new ShipComplexType\Weight(array(
                'Value' => 2,
                'Units' => ShipSimpleType\WeightUnits::_LB
            )));

        $cus = new ShipComplexType\CustomsClearanceDetail();
        //  new ShipComplexType\CustomsClearanceDetail();
        $money = new ShipComplexType\Money();
        $money->setCurrency('USD');
        $money->setAmount('120');
        $cus->setCustomsValue($money);

        $commodity = new ShipComplexType\Commodity();
        $commodity->setNumberOfPieces(1);
        $commodity->setDescription('Test');
        $commodity->setCountryOfManufacture('US');
        $commodity->setWeight(new ShipComplexType\Weight([
            'Units' => ShipSimpleType\WeightUnits::_LB,
            'Value' => 2
        ]));
        $commodity->setQuantity(1);
        $commodity->setQuantityUnits('EA');
        $commodity->setUnitPrice(new ShipComplexType\Money([
            'Currency' => 'USD',
            'Amount' => '120'
        ]));
        $commodity->setCustomsValue(new ShipComplexType\Money([
            'Currency' => 'USD',
            'Amount' => '120'
        ]));

        $cus->setCommodities($commodity->toArray());

        $cus->setDocumentContent(ShipSimpleType\InternationalDocumentContentType::_NON_DOCUMENTS);


        $shippingChargesPayor = new ShipComplexType\Payor();
        $shippingChargesPayor->setResponsibleParty($shipper);

        $shippingChargesPayment = new ShipComplexType\Payment();
        $shippingChargesPayment
            ->setPaymentType(ShipSimpleType\PaymentType::_SENDER)
            ->setPayor($shippingChargesPayor);
        // $creditCard = new ShipComplexType\CreditCard();
        // $creditCard->setNumber('4263093040006602');
        // $creditCard->setExpirationDate('09/21');
        //     // {
        //     const _ACCOUNT = 'ACCOUNT';
        //     const _CASH = 'CASH';
        //     const _COLLECT = 'COLLECT';
        //     const _CREDIT_CARD = 'CREDIT_CARD';
        //     const _RECIPIENT = 'RECIPIENT';
        //     const _SENDER = 'SENDER';
        //     const _THIRD_PARTY = 'THIRD_PARTY';
        // }
        $requestedShipment = new ShipComplexType\RequestedShipment();
        $requestedShipment->setShipTimestamp(date('c'));
        $requestedShipment->setDropoffType(new ShipSimpleType\DropoffType(ShipSimpleType\DropoffType::_REGULAR_PICKUP));
        $requestedShipment->setServiceType(new ShipSimpleType\ServiceType(ShipSimpleType\ServiceType::_FEDEX_EXPRESS_SAVER));
        $requestedShipment->setPackagingType(new ShipSimpleType\PackagingType(ShipSimpleType\PackagingType::_YOUR_PACKAGING));
        $requestedShipment->setShipper($shipper);
        $requestedShipment->setRecipient($recipient);
        $requestedShipment->setLabelSpecification($labelSpecification);
        $requestedShipment->setRateRequestTypes(array(new ShipSimpleType\RateRequestType(ShipSimpleType\RateRequestType::_PREFERRED)));
        $requestedShipment->setPackageCount(1);
        $requestedShipment->setRequestedPackageLineItems([
            $packageLineItem1
        ]);
        $requestedShipment->setShippingChargesPayment($shippingChargesPayment);
        $requestedShipment->setCustomsClearanceDetail($cus);

        $processShipmentRequest = new ShipComplexType\ProcessShipmentRequest();
        $processShipmentRequest->setWebAuthenticationDetail($webAuthenticationDetail);
        $processShipmentRequest->setClientDetail($clientDetail);
        $processShipmentRequest->setVersion($version);
        $processShipmentRequest->setRequestedShipment($requestedShipment);

        $shipService = new ShipService\Request();

        dd( $processShipmentRequest->toArray());
        //$shipService->getSoapClient()->__setLocation('https://ws.fedex.com:443/web-services/ship');
        $result = $shipService->getProcessShipmentReply($processShipmentRequest);
        Shipment::find($data->id)->update(['provider_status' => $processShipmentRequest->toArray()]);
    }
}
