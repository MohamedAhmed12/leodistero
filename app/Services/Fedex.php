<?php

namespace App\Services;

use App\Http\Requests\CreateShipmentFormRequest;
use App\Interfaces\ShippingAdapterInterface;
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
        $rateRequest->RequestedShipment->Shipper->Address->City = $data['from']['default_state']['name'];
        $rateRequest->RequestedShipment->Shipper->Address->PostalCode = $data['from']['default_state']['postal_code'];
        $rateRequest->RequestedShipment->Shipper->Address->CountryCode = $data['from']['code'];

        // //recipient
        $rateRequest->RequestedShipment->Recipient->Address->City = $data['to']['default_state']['name'];
        $rateRequest->RequestedShipment->Recipient->Address->PostalCode = $data['to']['default_state']['postal_code'];
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

    public function createShipment(array $data)
    {

        $shipDate = new \DateTime();

        $createOpenShipmentRequest = new OpenShipComplexType\CreateOpenShipmentRequest();
        // web authentication detail
        $createOpenShipmentRequest->WebAuthenticationDetail->UserCredential->Key = env('FEDEX_KEY');
        $createOpenShipmentRequest->WebAuthenticationDetail->UserCredential->Password = env('FEDEX_PASSWORD');
        // client detail
        $createOpenShipmentRequest->ClientDetail->MeterNumber = env('FEDEX_METER_NUMBER');
        $createOpenShipmentRequest->ClientDetail->AccountNumber = env('FEDEX_ACCOUNT_NUMBER');
        // version
        $createOpenShipmentRequest->Version->ServiceId = 'ship';
        $createOpenShipmentRequest->Version->Major = 15;
        $createOpenShipmentRequest->Version->Intermediate = 0;
        $createOpenShipmentRequest->Version->Minor = 0;
        
        // package 1
        $requestedPackageLineItem1 = new OpenShipComplexType\RequestedPackageLineItem();
        $requestedPackageLineItem1->SequenceNumber = 1;
        $requestedPackageLineItem1->ItemDescription = 'Product description 1';
        $requestedPackageLineItem1->Dimensions->Width = 10;
        $requestedPackageLineItem1->Dimensions->Height = 10;
        $requestedPackageLineItem1->Dimensions->Length = 15;
        $requestedPackageLineItem1->Dimensions->Units = OpenShipSimpleType\LinearUnits::_IN;
        $requestedPackageLineItem1->Weight->Value = 2;
        $requestedPackageLineItem1->Weight->Units = OpenShipSimpleType\WeightUnits::_LB;
        
        // requested shipment
        $createOpenShipmentRequest->RequestedShipment->DropoffType = OpenShipSimpleType\DropoffType::_REGULAR_PICKUP;
        $createOpenShipmentRequest->RequestedShipment->ShipTimestamp = $shipDate->format('c');
        $createOpenShipmentRequest->RequestedShipment->ServiceType = OpenShipSimpleType\ServiceType::_FEDEX_2_DAY;
        $createOpenShipmentRequest->RequestedShipment->PackagingType = OpenShipSimpleType\PackagingType::_YOUR_PACKAGING;
        $createOpenShipmentRequest->RequestedShipment->LabelSpecification->ImageType = OpenShipSimpleType\ShippingDocumentImageType::_PDF;
        $createOpenShipmentRequest->RequestedShipment->LabelSpecification->LabelFormatType = OpenShipSimpleType\LabelFormatType::_COMMON2D;
        $createOpenShipmentRequest->RequestedShipment->LabelSpecification->LabelStockType = OpenShipSimpleType\LabelStockType::_PAPER_4X6;
        $createOpenShipmentRequest->RequestedShipment->RateRequestTypes = [OpenShipSimpleType\RateRequestType::_PREFERRED];
        $createOpenShipmentRequest->RequestedShipment->PackageCount = 1;
        $createOpenShipmentRequest->RequestedShipment->RequestedPackageLineItems = [$requestedPackageLineItem1];
        
        // requested shipment shipper
        $createOpenShipmentRequest->RequestedShipment->Shipper->AccountNumber = env('FEDEX_ACCOUNT_NUMBER');
        $createOpenShipmentRequest->RequestedShipment->Shipper->Address->StreetLines = ['1234 Main Street'];
        $createOpenShipmentRequest->RequestedShipment->Shipper->Address->City = 'Anytown';
        $createOpenShipmentRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = 'NY';
        $createOpenShipmentRequest->RequestedShipment->Shipper->Address->PostalCode = '12345';
        $createOpenShipmentRequest->RequestedShipment->Shipper->Address->CountryCode = 'US';
        $createOpenShipmentRequest->RequestedShipment->Shipper->Contact->CompanyName = 'Company Name';
        $createOpenShipmentRequest->RequestedShipment->Shipper->Contact->PersonName = 'Person Name';
        $createOpenShipmentRequest->RequestedShipment->Shipper->Contact->EMailAddress = 'shipper@example.com';
        $createOpenShipmentRequest->RequestedShipment->Shipper->Contact->PhoneNumber = '1-123-123-1234';
        
        // requested shipment recipient
        $createOpenShipmentRequest->RequestedShipment->Recipient->Address->StreetLines = ['54321 1st Ave.'];
        $createOpenShipmentRequest->RequestedShipment->Recipient->Address->City = 'Cairo';
        $createOpenShipmentRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = 'CA';
        $createOpenShipmentRequest->RequestedShipment->Recipient->Address->PostalCode = '11865';
        $createOpenShipmentRequest->RequestedShipment->Recipient->Address->CountryCode = 'EG';
        $createOpenShipmentRequest->RequestedShipment->Recipient->Contact->PersonName = 'John Doe';
        $createOpenShipmentRequest->RequestedShipment->Recipient->Contact->EMailAddress = 'recipient@example.com';
        $createOpenShipmentRequest->RequestedShipment->Recipient->Contact->PhoneNumber = '1-321-321-4321';
        
        // shipping charges payment
        $createOpenShipmentRequest->RequestedShipment->ShippingChargesPayment->Payor->ResponsibleParty = $createOpenShipmentRequest->RequestedShipment->Shipper;
        $createOpenShipmentRequest->RequestedShipment->ShippingChargesPayment->PaymentType = OpenShipSimpleType\PaymentType::_SENDER;
        
        // send the create open shipment request
        $openShipServiceRequest = new OpenShipRequest();
        $createOpenShipmentReply = $openShipServiceRequest->getCreateOpenShipmentReply($createOpenShipmentRequest);
        
        // shipment is created and we have an index number
        $index = $createOpenShipmentReply->Index;
        
        
        /********************************
         * Add a package to open shipment
         ********************************/
        $addPackagesToOpenShipmentRequest = new OpenShipComplexType\AddPackagesToOpenShipmentRequest();
        
        // set index
        $addPackagesToOpenShipmentRequest->Index = $index;
        
        // reuse web authentication detail from previous request
        $addPackagesToOpenShipmentRequest->WebAuthenticationDetail = $createOpenShipmentRequest->WebAuthenticationDetail;
        
        // reuse client detail from previous request
        $addPackagesToOpenShipmentRequest->ClientDetail = $createOpenShipmentRequest->ClientDetail;
        
        // reuse version from previous request
        $addPackagesToOpenShipmentRequest->Version = $createOpenShipmentRequest->Version;
        
        // requested package line item
        $requestedPackageLineItem2 = new OpenShipComplexType\RequestedPackageLineItem();
        $requestedPackageLineItem2->SequenceNumber = 2;
        $requestedPackageLineItem2->ItemDescription = 'New package added to open shipment';
        $requestedPackageLineItem2->Dimensions->Width = 20;
        $requestedPackageLineItem2->Dimensions->Height = 10;
        $requestedPackageLineItem2->Dimensions->Length = 12;
        $requestedPackageLineItem2->Dimensions->Units = OpenShipSimpleType\LinearUnits::_IN;
        $requestedPackageLineItem2->Weight->Value = 4;
        $requestedPackageLineItem2->Weight->Units = OpenShipSimpleType\WeightUnits::_LB;
        $addPackagesToOpenShipmentRequest->RequestedPackageLineItems = [$requestedPackageLineItem2];
        
        // send the add packages to open shipment request
        $addPackagesToOpenShipmentReply = $openShipServiceRequest->getAddPackagesToOpenShipmentReply($addPackagesToOpenShipmentRequest);
        
        // dump($addPackagesToOpenShipmentReply);
        
        /************************************
         * Retrieve the open shipment details
         ************************************/
        $retrieveOpenShipmentRequest = new OpenShipComplexType\RetrieveOpenShipmentRequest();
        $retrieveOpenShipmentRequest->Index = $index;
        $retrieveOpenShipmentRequest->WebAuthenticationDetail = $createOpenShipmentRequest->WebAuthenticationDetail;
        $retrieveOpenShipmentRequest->ClientDetail = $createOpenShipmentRequest->ClientDetail;
        $retrieveOpenShipmentRequest->Version = $createOpenShipmentRequest->Version;
        
        $retrieveOpenShipmentReply = $openShipServiceRequest->getRetrieveOpenShipmentReply($retrieveOpenShipmentRequest);
        
        // dump('then',$retrieveOpenShipmentReply);
        
        /***********************
         * Confirm open shipment
         ***********************/
        $confirmOpenShipmentRequest = new OpenShipComplexType\ConfirmOpenShipmentRequest();
        $confirmOpenShipmentRequest->WebAuthenticationDetail = $createOpenShipmentRequest->WebAuthenticationDetail;
        $confirmOpenShipmentRequest->ClientDetail = $createOpenShipmentRequest->ClientDetail;
        $confirmOpenShipmentRequest->Version = $createOpenShipmentRequest->Version;
        $confirmOpenShipmentRequest->Index = $index;
        
        $confirmOpenShipmentReply = $openShipServiceRequest->getConfirmOpenShipmentReply($confirmOpenShipmentRequest);
        
        dd(  $confirmOpenShipmentReply);










































        // $userCredential = new ShipComplexType\WebAuthenticationCredential();
        // $userCredential
        //     ->setKey(env('FEDEX_KEY'))
        //     ->setPassword(env('FEDEX_PASSWORD'));

        // $webAuthenticationDetail = new ShipComplexType\WebAuthenticationDetail();
        // $webAuthenticationDetail->setUserCredential($userCredential);

        // $clientDetail = new ShipComplexType\ClientDetail();
        // $clientDetail
        //     ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
        //     ->setMeterNumber(env('FEDEX_METER_NUMBER'));

        // $version = new ShipComplexType\VersionId();
        // $version
        //     ->setMajor(26)
        //     ->setIntermediate(0)
        //     ->setMinor(0)
        //     ->setServiceId('ship');
        // // dd($data);
        // $shipperAddress = new ShipComplexType\Address();
        // $shipperAddress
        //     ->setStreetLines(['Address Line 1'])
        //     ->setCity('Austin')
        //     ->setStateOrProvinceCode('TX')
        //     ->setPostalCode('73301')
        //     ->setCountryCode('US');

        // $shipperContact = new ShipComplexType\Contact();
        // $shipperContact
        //     ->setCompanyName('Company Name')
        //     ->setEMailAddress('test@example.com')
        //     ->setPersonName('Person Name')
        //     ->setPhoneNumber(('123-123-1234'));

        // $shipper = new ShipComplexType\Party();
        // $shipper
        //     ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
        //     ->setAddress($shipperAddress)
        //     ->setContact($shipperContact);

        //     $recipientAddress = new ShipComplexType\Address();
        //     $recipientAddress
        //         ->setStreetLines(['Address Line 1'])
        //         ->setCity('Central Toronto')
        //         ->setStateOrProvinceCode('CT')
        //         ->setPostalCode('M4S')
        //         ->setCountryCode('CA');
            
        // $recipientContact = new ShipComplexType\Contact();
        // $recipientContact
        //     ->setPersonName('Contact Name')
        //     ->setPhoneNumber('1234567890');

        // $recipient = new ShipComplexType\Party();
        // $recipient
        //     ->setAddress($recipientAddress)
        //     ->setContact($recipientContact);

        // $labelSpecification = new ShipComplexType\LabelSpecification();
        // $labelSpecification
        //     ->setLabelStockType(new ShipSimpleType\LabelStockType(ShipSimpleType\LabelStockType::_PAPER_7X4POINT75))
        //     ->setImageType(new ShipSimpleType\ShippingDocumentImageType(ShipSimpleType\ShippingDocumentImageType::_PDF))
        //     ->setLabelFormatType(new ShipSimpleType\LabelFormatType(ShipSimpleType\LabelFormatType::_COMMON2D));

        // $packageLineItem1 = new ShipComplexType\RequestedPackageLineItem();


        // $packageLineItem1
        //     ->setSequenceNumber(1)
        //     ->setItemDescription('Product description')
        //     ->setDimensions(new ShipComplexType\Dimensions(array(
        //         'Width' => 10,
        //         'Height' => 10,
        //         'Length' => 25,
        //         'Units' => ShipSimpleType\LinearUnits::_IN
        //     )))
        //     ->setWeight(new ShipComplexType\Weight(array(
        //         'Value' => 2,
        //         'Units' => ShipSimpleType\WeightUnits::_LB
        //     )));

        // // $money = new ShipComplexType\Money();
        // // $money->setCurrency('USD');
        // // $money->setAmount('120');
        // // $cus = new ShipComplexType\CustomsClearanceDetail();
        // // $cus->setCustomsValue($money);

        // $shippingChargesPayor = new ShipComplexType\Payor();
        // $shippingChargesPayor->setResponsibleParty($shipper);

        // $shippingChargesPayment = new ShipComplexType\Payment();
        // $shippingChargesPayment
        //     ->setPaymentType(ShipSimpleType\PaymentType::_SENDER)
        //     ->setPayor($shippingChargesPayor);

        // $requestedShipment = new ShipComplexType\RequestedShipment();
        // $requestedShipment->setShipTimestamp(date('c'));
        // $requestedShipment->setDropoffType(new ShipSimpleType\DropoffType(ShipSimpleType\DropoffType::_REGULAR_PICKUP));
        // $requestedShipment->setServiceType(new ShipSimpleType\ServiceType(ShipSimpleType\ServiceType::_INTERNATIONAL_PRIORITY));
        // $requestedShipment->setPackagingType(new ShipSimpleType\PackagingType(ShipSimpleType\PackagingType::_YOUR_PACKAGING));
        // $requestedShipment->setShipper($shipper);
        // $requestedShipment->setRecipient($recipient);
        // $requestedShipment->setLabelSpecification($labelSpecification);
        // $requestedShipment->setRateRequestTypes(array(new ShipSimpleType\RateRequestType(ShipSimpleType\RateRequestType::_PREFERRED)));
        // $requestedShipment->setPackageCount(1);
        // $requestedShipment->setRequestedPackageLineItems([
        //     $packageLineItem1
        // ]);
        // $requestedShipment->setShippingChargesPayment($shippingChargesPayment);
        // // $requestedShipment->setCustomsClearanceDetail($cus);

        // $processShipmentRequest = new ShipComplexType\ProcessShipmentRequest();
        // $processShipmentRequest->setWebAuthenticationDetail($webAuthenticationDetail);
        // $processShipmentRequest->setClientDetail($clientDetail);
        // $processShipmentRequest->setVersion($version);
        // $processShipmentRequest->setRequestedShipment($requestedShipment);





        // $shipService = new ShipService\Request();
        // //$shipService->getSoapClient()->__setLocation('https://ws.fedex.com:443/web-services/ship');
        // $result = $shipService->getProcessShipmentReply($processShipmentRequest);
        // dd($result);
        // // return response()->json($result);

        // // Save .pdf label    
        // // $labelPath = Storage::put('public/documents/label.pdf', $result->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image);

    }
}
