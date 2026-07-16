<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_tags', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->index();
            $table->string('token', 40)->unique();
            $table->boolean('is_active')->default(true);
            $table->string('tag_code')->nullable()->index();
            $table->string('display_name');
            $table->string('owner_name')->nullable();
            $table->string('owner_phone', 30)->nullable();
            $table->string('secondary_contact_name')->nullable();
            $table->string('secondary_contact_phone', 30)->nullable();
            $table->string('blood_type', 10)->nullable();
            $table->text('allergies')->nullable();
            $table->text('medical_notes')->nullable();
            $table->text('public_notes')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('club_name')->nullable();
            $table->string('pet_species')->nullable();
            $table->string('pet_breed')->nullable();
            $table->string('vet_name')->nullable();
            $table->string('vet_phone', 30)->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_tags');
    }
};
