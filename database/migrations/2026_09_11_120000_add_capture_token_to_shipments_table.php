<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('capture_token', 64)->nullable()->unique()->after('public_token');
        });

        DB::table('shipments')->select('id')->orderBy('id')->each(function ($shipment) {
            DB::table('shipments')->where('id', $shipment->id)->update(['capture_token' => Str::random(48)]);
        });
    }

    public function down(): void
    {
        Schema::table('shipments', fn (Blueprint $table) => $table->dropColumn('capture_token'));
    }
};
