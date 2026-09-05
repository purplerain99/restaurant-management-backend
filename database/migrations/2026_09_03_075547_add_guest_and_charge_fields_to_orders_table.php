<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('guest_name')->nullable()->after('restaurant_table_id');
            $table->string('guest_phone', 30)->nullable()->after('guest_name');

            $table->decimal('tax_amount', 12, 2)
                ->default(0)
                ->after('subtotal');

            $table->decimal('service_charge', 12, 2)
                ->default(0)
                ->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'guest_name',
                'guest_phone',
                'tax_amount',
                'service_charge',
            ]);
        });
    }
};