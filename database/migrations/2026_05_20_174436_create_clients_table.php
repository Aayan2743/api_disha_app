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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

              $table->enum('call_type', [

                'incoming',
                'outgoing'

            ]);

            $table->enum('lead_type', [

                'cold',
                'hot',
                'warm'

            ]);

            $table->string('fullname');

            $table->string('phone');

            $table->string('location')->nullable();

            $table->string('referance')->nullable();

            $table->string('case_type')->nullable();

            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('added_by')->nullable();

            $table->tinyInteger('status')
                    ->default(1)
                    ->comment('1=Active,0=Inactive');

            $table->timestamps();

        });
           
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};