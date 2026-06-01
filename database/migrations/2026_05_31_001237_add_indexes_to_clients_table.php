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
        Schema::table('clients', function (Blueprint $table) {
            $table->index('lead_type', 'clients_lead_type_index');
            $table->index('created_at', 'clients_created_at_index');
            $table->index('phone', 'clients_phone_index');
            $table->index('fullname', 'clients_fullname_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('clients_lead_type_index');
            $table->dropIndex('clients_created_at_index');
            $table->dropIndex('clients_phone_index');
            $table->dropIndex('clients_fullname_index');
        });
    }
};
