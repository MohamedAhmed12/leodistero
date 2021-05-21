<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('shipments', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('tracking_id')->nullable();
        //     $table->unsignedBigInteger('user_id');
        //     $table->enum(
        //         'provider',
        //         [
        //             'aramex',
        //             'dhl',
        //             'fedex',
        //         ]
        //     );
        //     $table->enum(
        //         'status',
        //         [
        //             'pending_admin_review',
        //             'pending_need_updates',
        //             'pending_user_payment',
        //             'paid',
        //             'shipped'
        //         ]
        //     )
        //         ->default('pending_admin_review');
        //     $table->string('provider_status')->nullable();
        //     $table->string('shipper_name');
        //     $table->string('shipper_email');
        //     $table->string('shipper_number');
        //     $table->unsignedBigInteger('shipper_country_id');
        //     $table->unsignedBigInteger('shipper_state_id');
        //     $table->string('shipper_adress_line');
        //     $table->string('shipper_zip_code');
        //     $table->string('recipient_name');
        //     $table->string('recipient_email');
        //     $table->string('recipient_number');
        //     $table->unsignedBigInteger('recipient_country_id');
        //     $table->unsignedBigInteger('recipient_state_id');
        //     $table->string('recipient_adress_line');
        //     $table->string('recipient_zip_code');
        //     $table->string('package_weight');
        //     $table->string('package_length');
        //     $table->string('package_width');
        //     $table->string('package_height');
        //     $table->string('package_quantity');
        //     $table->string('package_pickup_location');
        //     $table->string('package_description');
        //     $table->string('package_shipping_date_time');
        //     $table->string('package_due_date');
        //     $table->string('package_shipment_type');
        //     $table->timestamps();

        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        //     $table->foreign('shipper_country_id')->references('id')->on('countries')->onDelete('cascade');
        //     $table->foreign('shipper_state_id')->references('id')->on('states')->onDelete('cascade');
        //     $table->foreign('recipient_country_id')->references('id')->on('countries')->onDelete('cascade');
        //     $table->foreign('recipient_state_id')->references('id')->on('states')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments');
    }
}
