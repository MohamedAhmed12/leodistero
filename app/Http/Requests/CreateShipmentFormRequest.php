<?php

namespace App\Http\Requests;

use App\Models\State;
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
            "shipper_number" => ["required", "numeric"],
            "shipper_country_id" => ["required", "exists:countries,id"],
            "shipper_state_id" => ["required", "exists:states,id"],
            "shipper_adress_line" => ["required", "string"],
            "shipper_zip_code" => ["required", "string"],

            "recipient_name" => ["required", "string"],
            "recipient_email" => ["required", "email"],
            "recipient_number" => ["required", "numeric"],
            "recipient_country_id" => ["required", "exists:countries,id"],
            "recipient_state_id" => ["required", "exists:states,id"],
            "recipient_adress_line" => ["required", "string"],
            "recipient_zip_code" => ["required", "string"],

            "package_weight" => ["required", "numeric"],
            "package_length" => ["required", "numeric"],
            "package_width" => ["required", "numeric"],
            "package_height" => ["required", "numeric"],
            "package_value" => ["required", "numeric"],
            "package_pickup_location" => ['required', 'string'],
            "package_quantity" => ["required", "numeric"],
            "package_description" => ["required", "string"],
            // "package_shipping_date_time" => ['required', 'date','before_or_equal:' . now()->addDays(4)->toDateString()],
            "package_shipping_date_time" => ['required', 'string'],
            "package_due_date" => ['required', 'date', 'before_or_equal:' . now()->addDays(45)->toDateString()],
            "package_shipment_type" => ['required', 'in:standard,express']
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
