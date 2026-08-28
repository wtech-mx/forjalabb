<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class DatabaseBackupService
{
    public function create(?string $prefix = null): string
    {
        $this->ensureMysql();
        $directory = storage_path('app/private/database-backups');
        File::ensureDirectoryExists($directory);
        $database = preg_replace('/[^A-Za-z0-9_-]/', '-', (string) config('database.connections.mysql.database'));
        $filename = ($prefix ? $prefix.'-' : '').$database.'-'.now()->format('Y-m-d-His').'.sql';
        $path = $directory.DIRECTORY_SEPARATOR.$filename;
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            throw new RuntimeException('No se pudo crear el archivo de respaldo.');
        }

        $process = new Process(array_merge([$this->binary('mysqldump')], $this->connectionArguments(), [
            '--single-transaction', '--quick', '--triggers', '--no-tablespaces',
            (string) config('database.connections.mysql.database'),
        ]), null, $this->environment());
        $process->setTimeout(600);
        $process->run(function (string $type, string $buffer) use ($handle): void {
            if ($type === Process::OUT) {
                fwrite($handle, $buffer);
            }
        });
        fclose($handle);

        if (! $process->isSuccessful()) {
            File::delete($path);
            throw new RuntimeException('No se pudo generar el respaldo: '.trim($process->getErrorOutput()));
        }

        return $path;
    }

    public function restore(string $sqlPath): void
    {
        $this->ensureMysql();
        $handle = fopen($sqlPath, 'rb');

        if ($handle === false) {
            throw new RuntimeException('No se pudo leer el respaldo seleccionado.');
        }

        $process = new Process(array_merge([$this->binary('mysql')], $this->connectionArguments(), [
            (string) config('database.connections.mysql.database'),
        ]), null, $this->environment(), $handle);
        $process->setTimeout(900);
        $process->run();
        fclose($handle);

        if (! $process->isSuccessful()) {
            throw new RuntimeException('MySQL rechazo el respaldo: '.trim($process->getErrorOutput()));
        }
    }

    private function ensureMysql(): void
    {
        if (config('database.default') !== 'mysql') {
            throw new RuntimeException('Los respaldos estan disponibles solamente para conexiones MySQL.');
        }
    }

    private function binary(string $name): string
    {
        $configured = config("database.backup_binaries.{$name}");
        $binary = $configured ?: (new ExecutableFinder)->find($name);

        if (! $binary && PHP_OS_FAMILY === 'Windows') {
            $laragonBin = dirname(PHP_BINARY, 3);
            $matches = glob($laragonBin.DIRECTORY_SEPARATOR.'mysql'.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.$name.'.exe') ?: [];
            rsort($matches, SORT_NATURAL);
            $binary = $matches[0] ?? null;
        }

        if (! $binary) {
            throw new RuntimeException("No se encontro {$name}. Configura DB_".strtoupper($name).'_BINARY en el archivo .env.');
        }

        return $binary;
    }

    private function connectionArguments(): array
    {
        $connection = config('database.connections.mysql');

        return [
            '--host='.(string) $connection['host'],
            '--port='.(string) $connection['port'],
            '--user='.(string) $connection['username'],
            '--default-character-set='.(string) $connection['charset'],
        ];
    }

    private function environment(): array
    {
        return ['MYSQL_PWD' => (string) config('database.connections.mysql.password')];
    }
}
