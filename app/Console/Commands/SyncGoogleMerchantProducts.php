<?php

namespace App\Console\Commands;

use App\Services\GoogleMerchantService;
use Illuminate\Console\Command;

class SyncGoogleMerchantProducts extends Command
{
    protected $signature = 'merchant:sync';
    protected $description = 'Sincroniza el catalogo activo con Google Merchant Center';

    public function handle(GoogleMerchantService $merchant): int
    {
        if (! $merchant->configured()) { $this->error('Google Merchant aun no esta configurado.'); return self::FAILURE; }
        $result = $merchant->syncCatalog();
        $this->info("Sincronizados: {$result['synced']}; omitidos: {$result['skipped']}; errores: ".count($result['errors']));
        foreach ($result['errors'] as $error) $this->warn($error);
        return $result['errors'] ? self::FAILURE : self::SUCCESS;
    }
}
