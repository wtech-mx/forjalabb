<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->replaceFolios(fn (int $id) => 'P'.$id);
    }

    public function down(): void
    {
        $this->replaceFolios(fn (int $id) => 'P-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT));
    }

    private function replaceFolios(callable $folio): void
    {
        $orders = DB::table('orders')->orderBy('id')->get(['id']);

        foreach ($orders as $order) {
            DB::table('orders')->where('id', $order->id)->update(['folio' => '__forjalab_'.$order->id]);
        }

        foreach ($orders as $order) {
            DB::table('orders')->where('id', $order->id)->update(['folio' => $folio((int) $order->id)]);
        }
    }
};
