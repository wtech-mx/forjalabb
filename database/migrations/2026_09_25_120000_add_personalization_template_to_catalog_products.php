<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->boolean('is_personalizable')->default(false)->after('presentation_mode');
            $table->string('personalization_method', 30)->nullable()->after('is_personalizable');
            $table->unsignedTinyInteger('personalization_x')->default(50)->after('personalization_method');
            $table->unsignedTinyInteger('personalization_y')->default(52)->after('personalization_x');
            $table->unsignedTinyInteger('personalization_width')->default(34)->after('personalization_y');
            $table->unsignedTinyInteger('personalization_height')->default(12)->after('personalization_width');
            $table->unsignedTinyInteger('personalization_font_size')->default(58)->after('personalization_height');
            $table->string('personalization_font_family', 80)->default('Montserrat')->after('personalization_font_size');
            $table->string('personalization_text_color', 20)->default('#2b2118')->after('personalization_font_family');
        });
    }

    public function down(): void
    {
        Schema::table('catalog_products', function (Blueprint $table) {
            $table->dropColumn([
                'is_personalizable',
                'personalization_method',
                'personalization_x',
                'personalization_y',
                'personalization_width',
                'personalization_height',
                'personalization_font_size',
                'personalization_font_family',
                'personalization_text_color',
            ]);
        });
    }
};
