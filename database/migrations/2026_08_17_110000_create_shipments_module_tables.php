<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('public_token',64)->unique(); $table->string('method',30); $table->string('status',30)->default('preparing');
            $table->string('carrier')->nullable(); $table->string('tracking_number')->nullable(); $table->text('tracking_url')->nullable();
            $table->string('destination_postal_code',10)->nullable(); $table->string('destination_state')->nullable(); $table->string('destination_city')->nullable(); $table->string('destination_neighborhood')->nullable(); $table->text('destination_address')->nullable();
            $table->decimal('cod_amount',12,2)->default(0); $table->decimal('quoted_amount',12,2)->nullable(); $table->string('quoted_service')->nullable();
            $table->decimal('parcel_weight',8,2)->nullable(); $table->unsignedInteger('parcel_length')->nullable(); $table->unsignedInteger('parcel_width')->nullable(); $table->unsignedInteger('parcel_height')->nullable();
            $table->json('quote_response')->nullable(); $table->timestamp('shipped_at')->nullable(); $table->timestamp('delivered_at')->nullable(); $table->timestamps();
        });
        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id(); $table->foreignId('shipment_id')->constrained()->cascadeOnDelete(); $table->string('phase',30); $table->string('title'); $table->text('description')->nullable(); $table->timestamp('occurred_at'); $table->boolean('is_public')->default(true); $table->timestamps();
        });
        Schema::create('shipment_media', function (Blueprint $table) {
            $table->id(); $table->foreignId('shipment_event_id')->constrained()->cascadeOnDelete(); $table->string('media_type',20); $table->string('file_path'); $table->string('original_name')->nullable(); $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('shipment_media'); Schema::dropIfExists('shipment_events'); Schema::dropIfExists('shipments'); }
};
