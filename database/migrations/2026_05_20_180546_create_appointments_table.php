<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->enum('appointment_type', [

                'online',
                'offline',

            ]);

            $table->enum('client_type', [

                'new_client',
                'existing_client',

            ]);

            $table->unsignedBigInteger('client_id')->nullable();

            $table->string('client_name')->nullable();

            $table->string('client_phone')->nullable();

            $table->date('appointment_date');

            $table->time('appointment_time');

            $table->decimal('fee_amount', 10, 2)->default(0);

            $table->enum('payment_method', [

                'cash',
                'online_payment',

            ]);

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('added_by')->nullable();

            $table->tinyInteger('status')
                ->default(1)
                ->comment('1=Booked,2=Completed,3=Cancelled');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};