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
            "ship_from.name" => ["required", "string"],
            "ship_from.email" => ["required", "email"],
            "ship_from.number" => ["required", "numeric"],
            "ship_from.country" => ["required", "exists:countries,id"],
            "ship_from.state" => ["required", "exists:states,id"],
            "ship_from.adress_line" => ["required", "string"],
            "ship_from.zip_code" => ["required", "string"],
            
            "ship_to.name" => ["required", "string"],
            "ship_to.email" => ["required", "email"],
            "ship_to.number" => ["required", "numeric"],
            "ship_to.country" => ["required", "exists:countries,id"],
            "ship_to.state" => ["required", "exists:states,id"],
            "ship_to.adress_line" => ["required", "string"],
            "ship_to.zip_code" => ["required", "string"],

            "package.weight" => ["required", "numeric"],
            "package.length" => ["required", "numeric"],
            "package.width" => ["required", "numeric"],
            "package.height" => ["required", "numeric"],
            "package.value" => ["required", "numeric"],
            "package.pickup_location" => ['required', 'string'],
            "package.quantity" => ["required", "numeric"],
            "package.description" => ["required", "string"],
            "package.shipping_date_time" => ['required', 'date','before_or_equal:' . now()->addDays(4)->toDateString()],
            "package.due_date" => ['required', 'date', 'before_or_equal:' . now()->addDays(45)->toDateString()],
            "package.shipment_type" => ['required','in:standard,express']
        ];
    }


    /**
     * UPdate on Data after validation
     * @return array
     */
    public function validated()
    {
        $validatedData = $this->validator->validated();

        $validatedData['ship_from']['country'] = Country::find( $validatedData['ship_from']['country']);
        $validatedData['ship_from']['state'] = $this->extractNameFromDB(State::class, $validatedData['ship_from']['state']);
        $validatedData['ship_to']['country'] = Country::find($validatedData['ship_to']['country']);
        $validatedData['ship_to']['state'] = $this->extractNameFromDB(State::class, $validatedData['ship_to']['state']);

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
