<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            $table->string('event_name')->nullable()->after('facility_id');
            $table->string('pic_name')->nullable()->after('event_name');
            $table->string('pic_phone', 50)->nullable()->after('pic_name');
            $table->string('destination')->nullable()->after('purpose');
            $table->boolean('driver_needed')->default(false)->after('destination');
            $table->string('driver_name')->nullable()->after('driver_needed');
            $table->string('letter_number', 100)->nullable()->after('eticket_token');
        });

        Schema::create('facility_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit_scope')->default('campus');
            $table->integer('total_stock')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('facility_booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('facility_bookings')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('facility_items')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_booking_items');
        Schema::dropIfExists('facility_items');

        Schema::table('facility_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'event_name',
                'pic_name',
                'pic_phone',
                'destination',
                'driver_needed',
                'driver_name',
                'letter_number',
            ]);
        });
    }
};
