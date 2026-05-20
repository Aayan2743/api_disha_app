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
        Schema::create('followups', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id');

            $table->unsignedBigInteger('appointment_id')->nullable();

            $table->date('followup_date');

            $table->text('remarks');

            $table->tinyInteger('status')
                ->default(0)
                ->comment('0=Pending,1=Completed');

            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followups');
    }
};