<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void { Schema::table('shipments',function(Blueprint $table){$table->string('skydropx_quotation_id')->nullable()->after('quote_response');$table->string('skydropx_rate_id')->nullable()->after('skydropx_quotation_id');$table->string('skydropx_shipment_id')->nullable()->after('skydropx_rate_id');$table->text('label_url')->nullable()->after('tracking_url');}); } public function down():void { Schema::table('shipments',fn(Blueprint $table)=>$table->dropColumn(['skydropx_quotation_id','skydropx_rate_id','skydropx_shipment_id','label_url'])); } };
