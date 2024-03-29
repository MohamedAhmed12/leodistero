<?php

namespace App\Services;

use stdClass;
use FedEx\ShipService;
use App\Models\Shipment;
use Illuminate\Support\Str;
use FedEx\RateService\Request;
use FedEx\RateService\SimpleType;

use FedEx\RateService\ComplexType;
use Illuminate\Support\Facades\Storage;
use App\Interfaces\ShippingAdapterInterface;

use App\Http\Requests\CreateShipmentFormRequest;
use FedEx\ShipService\SimpleType as ShipSimpleType;
use FedEx\OpenShipService\Request as OpenShipRequest;
use FedEx\ShipService\ComplexType as ShipComplexType;
use FedEx\OpenShipService\SimpleType as OpenShipSimpleType;
use FedEx\OpenShipService\ComplexType as OpenShipComplexType;

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

         //recipient
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
// dd($rateReply);
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
            ->setStreetLines([$data->shipper_adress_line])
            ->setCity($data->shipperCity->name)
            ->setStateOrProvinceCode($data->shipperCity->iso2)
            ->setPostalCode($data->shipper_zip_code)
            ->setCountryCode($data->shipperCountry->code);

        $shipperContact = new ShipComplexType\Contact();
        $shipperContact
            ->setCompanyName('Company Name')
            ->setEMailAddress($data->shipper_email)
            ->setPersonName($data->shipper_name)
            ->setPhoneNumber($data->shipper_number);

        $shipper = new ShipComplexType\Party();
        $shipper
            ->setAccountNumber(env('FEDEX_ACCOUNT_NUMBER'))
            ->setAddress($shipperAddress)
            ->setContact($shipperContact);

        $recipientAddress = new ShipComplexType\Address();
        $recipientAddress
            ->setStreetLines([$data->recipient_adress_line])
            ->setCity($data->recipientCity->name)
            ->setStateOrProvinceCode($data->recipientCity->iso2)
            ->setPostalCode($data->recipient_zip_code)
            ->setCountryCode($data->recipientCountry->code);

        $recipientContact = new ShipComplexType\Contact();
        $recipientContact
            ->setPersonName($data->recipient_name)
            ->setPhoneNumber($data->recipient_number);

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
                'Width' =>  $data->package_width,
                'Height' => $data->package_height,
                'Length' => $data->package_length,
                'Units' => ShipSimpleType\LinearUnits::_IN
            )))
            ->setWeight(new ShipComplexType\Weight(array(
                'Value' => $data->package_weight,
                'Units' => ShipSimpleType\WeightUnits::_LB
            )));

        $shippingChargesPayor = new ShipComplexType\Payor();
        $shippingChargesPayor->setResponsibleParty($shipper);

        $shippingChargesPayment = new ShipComplexType\Payment();
        $shippingChargesPayment
            ->setPaymentType(ShipSimpleType\PaymentType::_SENDER)
            ->setPayor($shippingChargesPayor);

        $cus = new ShipComplexType\CustomsClearanceDetail();
        $money = new ShipComplexType\Money();
        $money->setCurrency('USD');
        $money->setAmount($data->package_value);
        $cus->setCustomsValue($money);

        $commodity = new ShipComplexType\Commodity();
        $commodity->setNumberOfPieces(1);
        $commodity->setDescription('Test');
        $commodity->setCountryOfManufacture('US');
        $commodity->setWeight(new ShipComplexType\Weight([
            'Units' => ShipSimpleType\WeightUnits::_LB,
            'Value' => $data->package_weight
        ]));
        $commodity->setQuantity($data->package_quantity);
        $commodity->setQuantityUnits('EA');
        $commodity->setUnitPrice(new ShipComplexType\Money([
            'Currency' => 'USD',
            'Amount' => $data->package_value
        ]));
        $commodity->setCustomsValue(new ShipComplexType\Money([
            'Currency' => 'USD',
            'Amount' => $data->package_value
        ]));

        $cus->setCommodities($commodity->toArray());
        $cus->setDutiesPayment($shippingChargesPayment);
        $cus->setDocumentContent(ShipSimpleType\InternationalDocumentContentType::_NON_DOCUMENTS);

        $requestedShipment = new ShipComplexType\RequestedShipment();
        $requestedShipment->setShipTimestamp(date('c'));
        $requestedShipment->setDropoffType(new ShipSimpleType\DropoffType(ShipSimpleType\DropoffType::_REGULAR_PICKUP));
        $shipmentType = $data->package_shipment_type == 'standard' ? ShipSimpleType\ServiceType::_INTERNATIONAL_ECONOMY : ShipSimpleType\ServiceType::_INTERNATIONAL_PRIORITY;
        $requestedShipment->setServiceType(new ShipSimpleType\ServiceType($shipmentType));
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
        //$shipService->getSoapClient()->__setLocation('https://ws.fedex.com:443/web-services/ship');
        $res = $shipService->getProcessShipmentReply($processShipmentRequest);

        if ($res->HighestSeverity == 'SUCCESS' && isset($res->CompletedShipmentDetail) && isset($res->CompletedShipmentDetail->CompletedPackageDetails)) {
            $image= $res->CompletedShipmentDetail->CompletedPackageDetails[0]->Label->Parts[0]->Image;
            $labelPath = "public/documents/" . Str::random() . '_' . time() . '.pdf';

            Storage::put(
                $labelPath,
                $image
            );

            Shipment::whereId($data->id)->update([
                'label_path' => asset(Storage::url($labelPath)),
                'provider_shipment_id' => $res->CompletedShipmentDetail->CompletedPackageDetails[0]->TrackingIds[0]->TrackingNumber,
                'provider_status' => '',
                'status' => 'shipped'
            ]);
        } elseif ($res->HighestSeverity == 'ERROR') {
            Shipment::whereId($data->id)->update([
                'provider_status' => isset($res->Notifications) ? $res->Notifications[0]->Message : '',
                'status' => 'pending_need_updates'
            ]);
        }
 }
}
