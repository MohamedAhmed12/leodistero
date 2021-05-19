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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id');
            $table->unsignedBigInteger('user_id');
            $table->enum(
                'provider',
                [
                    'aramex',
                    'dhl',
                    'fedex',
                ]
            );
            $table->enum(
                'status',
                [
                    'pending_admin_review',
                    'pending_need_updates',
                    'pending_user_payment',
                    'paid',
                    'shipped'
                ]
            )
                ->default('pending_admin_review');
            $table->string('provider_status')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
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
