<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('smart_tags', function (Blueprint $table) {
            $table->boolean('is_blood_donor')->default(false)->after('blood_type');
        });

        DB::table('smart_tags')->orderBy('id')->lazy()->each(function (object $tag): void {
            $type = $tag->type === 'biker' ? 'BKR' : 'DOG';
            $blood = strtoupper(str_replace(['+', '-'], ['P', 'N'], $tag->blood_type ?: 'XX'));
            $donor = $tag->type === 'biker' ? 'ND' : 'PET';

            DB::table('smart_tags')
                ->where('id', $tag->id)
                ->update(['tag_code' => sprintf('LC-%s-%s-%s-%06d', $type, $blood, $donor, $tag->id)]);
        });

        Schema::table('smart_tags', function (Blueprint $table) {
            $table->unique('tag_code');
        });
    }

    public function down(): void
    {
        Schema::table('smart_tags', function (Blueprint $table) {
            $table->dropUnique(['tag_code']);
            $table->dropColumn('is_blood_donor');
        });
    }
};
