<?php

namespace App\Http\Requests;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "shipper_name" => ["required", "string"],
            "shipper_email" => ["required", "email"],
            "shipper_number" => ["required", "numeric", "min:8"],
            "shipper_country_id" => ["required", "exists:countries,id"],
            "shipper_city_id" => ["required", "exists:cities,id"],
            "shipper_adress_line" => ["required", "string", "min:5"],
            "shipper_zip_code" => ["required", "string"],

            "recipient_name" => ["required", "string"],
            "recipient_email" => ["required", "email"],
            "recipient_number" => ["required", "numeric", "min:8"],
            "recipient_country_id" => ["required", "exists:countries,id"],
            "recipient_city_id" => ["required", "exists:cities,id"],
            "recipient_adress_line" => ["required", "string", "min:5"],
            "recipient_zip_code" => ["required", "string"],

            "package_weight" => ["required", "numeric"],
            "package_length" => ["required", "numeric"],
            "package_width" => ["required", "numeric"],
            "package_height" => ["required", "numeric"],
            "package_value" => ["required", "numeric"],
            "package_quantity" => ["required", "numeric"],
            "package_pickup_location" => ['required', 'string'],
            "package_description" => ["required", "string"],
            // "package_shipping_date_time" => ['required', 'date','before_or_equal:' . now()->addDays(4)->toDateString()],
            "package_shipping_date_time" => ['required', 'string'],
            "package_due_date" => ['required', 'date', 'before_or_equal:' . now()->addDays(45)->toDateString()],
            "package_shipment_type" => ['required', 'in:standard,express'],

            "provider" => ['nullable', 'in:fedex,dhl,aramex']
        ];
    }


    /**
     * UPdate on Data after validation
     * @return array
     */
    public function validated()
    {
        $validatedData = $this->validator->validated();

        // $validatedData['shipper_country'] = Country::find($validatedData['shipper_country']);
        // // $validatedData['shipper_state'] = $this->extractNameFromDB(State::class, $validatedData['shipper_state']);
        // // $validatedData['recipient_country'] = Country::find($validatedData['recipient_country']);
        // $validatedData['recipient_state'] = $this->extractNameFromDB(State::class, $validatedData['recipient']['state']);
        $validatedData['user_id'] = auth()->user()->id;
        return $validatedData;
    }


    /**
     * Extracat country or state name from database
     * @return string
     */
    public function extractNameFromDB(string $model, string $id)
    {
        return  $model::select('name')
            ->whereId($id)
            ->first()['name'];
    }
}
