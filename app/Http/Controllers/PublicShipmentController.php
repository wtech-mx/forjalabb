<?php
namespace App\Http\Controllers;
use App\Models\Shipment;
use Illuminate\View\View;
class PublicShipmentController extends Controller { public function __invoke(string $token):View { $shipment=Shipment::where('public_token',$token)->with(['order.customer','order.items','events'=>fn($q)=>$q->where('is_public',true)->orderByDesc('occurred_at'),'events.media'])->firstOrFail(); return view('shipments.public',compact('shipment')); } }
