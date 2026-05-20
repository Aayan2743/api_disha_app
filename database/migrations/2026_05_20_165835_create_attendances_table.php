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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
              $table->unsignedBigInteger('user_id');

            $table->date('attendance_date');

            $table->dateTime('punch_in')->nullable();

            $table->dateTime('punch_out')->nullable();

            $table->decimal('punch_in_latitude', 10, 7)->nullable();

            $table->decimal('punch_in_longitude', 10, 7)->nullable();

            $table->decimal('punch_out_latitude', 10, 7)->nullable();

            $table->decimal('punch_out_longitude', 10, 7)->nullable();

            $table->text('punch_in_address')->nullable();

            $table->text('punch_out_address')->nullable();

            $table->integer('total_minutes')->default(0);



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};